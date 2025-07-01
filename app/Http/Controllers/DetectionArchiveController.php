<?php

namespace App\Http\Controllers;

use App\Models\Cctv;
use App\Http\Controllers\StorageSettingsController;
use App\Services\CctvService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DetectionArchiveController extends Controller
{
    protected CctvService $cctvService;

    public function __construct(CctvService $cctvService)
    {
        $this->cctvService = $cctvService;
    }

    /**
     * Display the detection archive page
     */
    public function index(Request $request)
    {
        // Get cameras from external service
        $knownCameras = $this->getCamerasFromService();
        
        // Get filters from request
        $selectedCamera = $request->get('camera');
        $selectedDate = $request->get('date', date('Y-m-d'));
        $selectedDetectionType = $request->get('detection_type');
        $selectedTimeRange = $request->get('time_range');
        
        Log::info('DetectionArchive: Request parameters', [
            'camera' => $selectedCamera,
            'date' => $selectedDate,
            'detection_type' => $selectedDetectionType,
            'time_range' => $selectedTimeRange,
            'all_params' => $request->all()
        ]);
        
        // Get detection files from MinIO
        $detectionFiles = $this->getDetectionFilesFromMinIO(
            $selectedCamera, 
            $selectedDate, 
            $selectedDetectionType, 
            $selectedTimeRange
        );
        
        // Build comprehensive camera list for dropdown
        $cameras = $this->buildCameraDropdownList($knownCameras, $selectedDate);
        
        // Get storage status
        $storageController = new StorageSettingsController();
        $storageData = $storageController->getSettings();
        $storageStatus = $storageData['storageStatus'];
        
        return view('admin.security.detection-archive', compact(
            'cameras', 
            'detectionFiles', 
            'selectedCamera', 
            'selectedDate', 
            'selectedDetectionType',
            'selectedTimeRange',
            'storageStatus'
        ));
    }

    /**
     * Build camera dropdown list including discovered cameras from MinIO
     */
    private function buildCameraDropdownList($knownCameras, $selectedDate)
    {
        $cameraList = collect();
        
        // Add known cameras
        foreach ($knownCameras as $camera) {
            $cameraList->push((object) [
                'id' => $camera->id,
                'name' => $camera->name,
                'is_identified' => true,
                'sort_order' => 1 // Known cameras first
            ]);
        }
        
        // Discover cameras from MinIO for the selected date
        $dateObj = \DateTime::createFromFormat('Y-m-d', $selectedDate);
        $year = $dateObj->format('Y');
        $month = $dateObj->format('m');
        $day = $dateObj->format('d');
        
        $discoveredCameraIds = $this->discoverAllCameraIds($year, $month, $day);
        $knownCameraIds = $knownCameras->pluck('id')->toArray();
        
        // Add unidentified cameras found in MinIO
        foreach ($discoveredCameraIds as $cameraId) {
            if (!in_array($cameraId, $knownCameraIds)) {
                $cameraList->push((object) [
                    'id' => $cameraId,
                    'name' => "Unidentified ({$cameraId})",
                    'is_identified' => false,
                    'sort_order' => 2 // Unidentified cameras after known ones
                ]);
            }
        }
        
        // Sort by identified status first, then by name
        return $cameraList->sortBy([
            ['sort_order', 'asc'],
            ['name', 'asc']
        ]);
    }

    /**
     * Get cameras from external CCTV service
     */
    private function getCamerasFromService()
    {
        try {
            // Skip CCTV service for now to test MinIO functionality
            Log::info('DetectionArchive: Skipping CCTV service call for testing');
            return collect();
            
            $cameras = $this->cctvService->getAllCameras();
            if (!$cameras) {
                return collect();
            }
            
            return collect($cameras)->map(function ($camera) {
                return (object) [
                    'id' => $camera['id'],
                    'name' => $camera['name']
                ];
            });
        } catch (\Exception $e) {
            Log::error('Failed to fetch cameras from service: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get detection files from MinIO based on filters
     */
    private function getDetectionFilesFromMinIO($cameraFilter, $date, $detectionType, $timeRange)
    {
        try {
            $files = [];
            $bucket = config('storage.minio.bucket', 'detection-archive');
            
            // Debug logging
            Log::info('DetectionArchive: Starting file retrieval', [
                'camera_filter' => $cameraFilter,
                'date' => $date,
                'detection_type' => $detectionType,
                'time_range' => $timeRange
            ]);
            
            // Parse date for path structure
            $dateObj = \DateTime::createFromFormat('Y-m-d', $date);
            if (!$dateObj) {
                Log::error('DetectionArchive: Invalid date format', ['date' => $date]);
                return [];
            }
            
            $year = $dateObj->format('Y');
            $month = $dateObj->format('m');
            $day = $dateObj->format('d');
            
            Log::info('DetectionArchive: Parsed date', [
                'year' => $year,
                'month' => $month,
                'day' => $day
            ]);
            
            // Get known cameras from service for name mapping
            $knownCameras = $this->getCamerasFromService();
            $cameraMap = [];
            foreach ($knownCameras as $camera) {
                $cameraMap[$camera->id] = $camera->name;
            }
            
            Log::info('DetectionArchive: Known cameras', [
                'count' => count($knownCameras),
                'camera_map' => $cameraMap
            ]);
            
            if ($cameraFilter && $cameraFilter !== 'all') {
                // Specific camera filter - check only that camera
                $cameraIds = [$cameraFilter];
                Log::info('DetectionArchive: Using specific camera filter', ['camera_ids' => $cameraIds]);
            } else {
                // "Show All Cameras" - discover all camera folders in MinIO
                $cameraIds = $this->discoverAllCameraIds($year, $month, $day);
                Log::info('DetectionArchive: Discovered cameras from MinIO', ['camera_ids' => $cameraIds]);
            }
            
            foreach ($cameraIds as $cameraId) {
                $basePath = "{$cameraId}/{$year}/{$month}/{$day}/";
                
                Log::info('DetectionArchive: Processing camera', [
                    'camera_id' => $cameraId,
                    'base_path' => $basePath
                ]);
                
                // Determine camera info
                $cameraInfo = (object) [
                    'id' => $cameraId,
                    'name' => $cameraMap[$cameraId] ?? null,
                    'is_identified' => isset($cameraMap[$cameraId])
                ];
                
                // Get detection types (folders) for this camera/date
                $detectionTypes = $this->getDetectionTypesForPath($basePath);
                
                Log::info('DetectionArchive: Found detection types', [
                    'camera_id' => $cameraId,
                    'detection_types' => $detectionTypes
                ]);
                
                foreach ($detectionTypes as $detectionTypeFolder) {
                    // Skip if detection type filter is applied and doesn't match
                    if ($detectionType && $detectionType !== 'all' && $detectionTypeFolder !== $detectionType) {
                        Log::info('DetectionArchive: Skipping detection type due to filter', [
                            'detection_type_folder' => $detectionTypeFolder,
                            'filter' => $detectionType
                        ]);
                        continue;
                    }
                    
                    $detectionPath = $basePath . $detectionTypeFolder . '/';
                    $filesInPath = $this->getFilesFromMinIOPath($detectionPath);
                    
                    Log::info('DetectionArchive: Found files in path', [
                        'detection_path' => $detectionPath,
                        'file_count' => count($filesInPath),
                        'files' => $filesInPath
                    ]);
                    
                    foreach ($filesInPath as $file) {
                        $fileInfo = $this->parseFileInfo($file, $cameraInfo, $detectionTypeFolder, $date);
                        
                        // Apply time range filter
                        if ($this->matchesTimeRange($fileInfo, $timeRange)) {
                            $files[] = $fileInfo;
                            Log::info('DetectionArchive: Added file to results', [
                                'file' => $fileInfo['filename'],
                                'camera' => $fileInfo['camera_name']
                            ]);
                        } else {
                            Log::info('DetectionArchive: File filtered out by time range', [
                                'file' => $fileInfo['filename'],
                                'time_range' => $timeRange
                            ]);
                        }
                    }
                }
            }
            
            // Sort by timestamp descending
            usort($files, function($a, $b) {
                return $b['timestamp'] - $a['timestamp'];
            });
            
            Log::info('DetectionArchive: Final results', [
                'total_files' => count($files)
            ]);
            
            return $files;
            
        } catch (\Exception $e) {
            Log::error('Failed to fetch files from MinIO: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return [];
        }
    }

    /**
     * Discover all camera IDs that have files for the given date
     */
    private function discoverAllCameraIds($year, $month, $day)
    {
        try {
            $bucket = config('storage.minio.bucket', 'detection-archive');
            $client = $this->getMinIOClient();
            
            // List all top-level "folders" (camera IDs) that have data for this date
            $datePrefix = "{$year}/{$month}/{$day}/";
            $cameraIds = [];
            
            Log::info('DetectionArchive: Starting camera discovery', [
                'bucket' => $bucket,
                'date_prefix' => $datePrefix
            ]);
            
            // Use a broader search to find all camera folders
            $result = $client->listObjectsV2([
                'Bucket' => $bucket,
                'Delimiter' => '/',
                'MaxKeys' => 1000
            ]);
            
            Log::info('DetectionArchive: Top-level folder listing', [
                'has_common_prefixes' => isset($result['CommonPrefixes']),
                'prefix_count' => isset($result['CommonPrefixes']) ? count($result['CommonPrefixes']) : 0
            ]);
            
            if (isset($result['CommonPrefixes'])) {
                foreach ($result['CommonPrefixes'] as $prefix) {
                    $cameraId = rtrim($prefix['Prefix'], '/');
                    
                    Log::info('DetectionArchive: Checking camera folder', [
                        'camera_id' => $cameraId,
                        'prefix' => $prefix['Prefix']
                    ]);
                    
                    // Check if this camera has files for the target date
                    $checkPath = "{$cameraId}/{$datePrefix}";
                    try {
                        Log::info('DetectionArchive: Checking path for files', [
                            'check_path' => $checkPath
                        ]);
                        
                        $checkResult = $client->listObjectsV2([
                            'Bucket' => $bucket,
                            'Prefix' => $checkPath,
                            'MaxKeys' => 1
                        ]);
                        
                        $hasFiles = isset($checkResult['Contents']) && count($checkResult['Contents']) > 0;
                        Log::info('DetectionArchive: File check result', [
                            'camera_id' => $cameraId,
                            'has_files' => $hasFiles,
                            'file_count' => isset($checkResult['Contents']) ? count($checkResult['Contents']) : 0
                        ]);
                        
                        if ($hasFiles) {
                            $cameraIds[] = $cameraId;
                        }
                    } catch (\Exception $e) {
                        Log::error('DetectionArchive: Error checking camera path', [
                            'camera_id' => $cameraId,
                            'error' => $e->getMessage()
                        ]);
                        // Skip cameras that can't be checked
                        continue;
                    }
                }
            }
            
            Log::info('DetectionArchive: Camera discovery complete', [
                'discovered_cameras' => $cameraIds
            ]);
            
            return $cameraIds;
            
        } catch (\Exception $e) {
            Log::error('Failed to discover camera IDs: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get detection types (folders) for a given path
     */
    private function getDetectionTypesForPath($basePath)
    {
        try {
            $bucket = config('storage.minio.bucket', 'detection-archive');
            $client = $this->getMinIOClient();
            
            $result = $client->listObjectsV2([
                'Bucket' => $bucket,
                'Prefix' => $basePath,
                'Delimiter' => '/'
            ]);
            
            $detectionTypes = [];
            
            // Get folders (CommonPrefixes)
            if (isset($result['CommonPrefixes'])) {
                foreach ($result['CommonPrefixes'] as $prefix) {
                    $relativePath = str_replace($basePath, '', $prefix['Prefix']);
                    $pathParts = explode('/', trim($relativePath, '/'));
                    if (!empty($pathParts[0]) && !in_array($pathParts[0], $detectionTypes)) {
                        $detectionTypes[] = $pathParts[0];
                    }
                }
            }
            
            return $detectionTypes;
        } catch (\Exception $e) {
            Log::error('Failed to get detection types: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get files from MinIO path
     */
    private function getFilesFromMinIOPath($path)
    {
        try {
            $bucket = config('storage.minio.bucket', 'detection-archive');
            $client = $this->getMinIOClient();
            
            $result = $client->listObjectsV2([
                'Bucket' => $bucket,
                'Prefix' => $path
            ]);
            
            $files = [];
            
            if (isset($result['Contents'])) {
                foreach ($result['Contents'] as $object) {
                    $objectKey = $object['Key'];
                    // Only include actual files (not directories)
                    if (!str_ends_with($objectKey, '/')) {
                        $files[] = $objectKey;
                    }
                }
            }
            
            return $files;
        } catch (\Exception $e) {
            Log::error('Failed to get files from path: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Parse file information from MinIO object
     */
    private function parseFileInfo($filePath, $cameraInfo, $detectionType, $date)
    {
        $pathParts = explode('/', $filePath);
        $filename = end($pathParts);
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        // Determine file type
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];
        $videoExtensions = ['mp4', 'avi', 'mov', 'mkv', 'wmv'];
        
        if (in_array($extension, $imageExtensions)) {
            $type = 'image';
        } elseif (in_array($extension, $videoExtensions)) {
            $type = 'video';
        } else {
            $type = 'file';
        }
        
        // Try to extract timestamp from filename or use file metadata
        $timestamp = $this->extractTimestampFromFile($filePath, $date);
        
        // Get file size from MinIO
        $size = $this->getFileSize($filePath);
        
        return [
            'id' => md5($filePath),
            'camera_id' => $cameraInfo->id,
            'camera_name' => $cameraInfo->name ?? 'Unidentified',
            'camera_is_identified' => $cameraInfo->is_identified,
            'filename' => $filename,
            'full_path' => $filePath,
            'type' => $type,
            'size' => $size,
            'timestamp' => $timestamp,
            'time_label' => date('H:i:s', $timestamp),
            'date' => $date,
            'detection_type' => ucfirst($detectionType),
        ];
    }

    /**
     * Extract timestamp from file path or metadata
     */
    private function extractTimestampFromFile($filePath, $date)
    {
        try {
            // Try to get file metadata first
            $bucket = config('storage.minio.bucket', 'detection-archive');
            $client = $this->getMinIOClient();
            
            $result = $client->headObject([
                'Bucket' => $bucket,
                'Key' => $filePath
            ]);
            
            if (isset($result['LastModified'])) {
                return $result['LastModified']->getTimestamp();
            }
        } catch (\Exception $e) {
            Log::debug('Could not get file metadata for timestamp: ' . $e->getMessage());
        }
        
        // Fallback: try to extract from filename
        $filename = basename($filePath);
        
        // Look for timestamp patterns in filename (e.g., 20240101_143052 or similar)
        if (preg_match('/(\d{8})[_-](\d{6})/', $filename, $matches)) {
            $dateStr = $matches[1]; // YYYYMMDD
            $timeStr = $matches[2]; // HHMMSS
            
            $timestamp = \DateTime::createFromFormat('Ymd_His', $dateStr . '_' . $timeStr);
            if ($timestamp) {
                return $timestamp->getTimestamp();
            }
        }
        
        // Final fallback: use date with random time
        $baseTimestamp = strtotime($date);
        $randomOffset = rand(0, 86399); // Random time within the day
        return $baseTimestamp + $randomOffset;
    }

    /**
     * Get file size from MinIO
     */
    private function getFileSize($filePath)
    {
        try {
            $bucket = config('storage.minio.bucket', 'detection-archive');
            $client = $this->getMinIOClient();
            
            $result = $client->headObject([
                'Bucket' => $bucket,
                'Key' => $filePath
            ]);
            
            $sizeBytes = $result['ContentLength'] ?? 0;
            
            // Convert to human readable format
            if ($sizeBytes < 1024) {
                return $sizeBytes . ' B';
            } elseif ($sizeBytes < 1048576) {
                return round($sizeBytes / 1024, 1) . ' KB';
            } else {
                return round($sizeBytes / 1048576, 1) . ' MB';
            }
        } catch (\Exception $e) {
            Log::debug('Could not get file size: ' . $e->getMessage());
            return 'Unknown';
        }
    }

    /**
     * Check if file matches time range filter
     */
    private function matchesTimeRange($fileInfo, $timeRange)
    {
        if (!$timeRange || $timeRange === 'all') {
            return true;
        }
        
        $hour = (int) date('H', $fileInfo['timestamp']);
        
        switch ($timeRange) {
            case 'morning':
                return $hour >= 6 && $hour < 12;
            case 'afternoon':
                return $hour >= 12 && $hour < 18;
            case 'evening':
                return $hour >= 18 && $hour < 24;
            case 'night':
                return $hour >= 0 && $hour < 6;
            default:
                return true;
        }
    }

    /**
     * Get MinIO client
     */
    private function getMinIOClient()
    {
        $endpoint = config('storage.minio.endpoint');
        $accessKey = config('storage.minio.access_key');
        $secretKey = config('storage.minio.secret_key');
        $useSSL = config('storage.minio.use_ssl', false);
        
        return new \Aws\S3\S3Client([
            'version' => 'latest',
            'region' => config('storage.minio.region', 'us-east-1'),
            'endpoint' => ($useSSL ? 'https://' : 'http://') . $endpoint,
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key' => $accessKey,
                'secret' => $secretKey,
            ],
        ]);
    }
    
    /**
     * File preview endpoint
     */
    public function preview(Request $request)
    {
        try {
            $fileId = $request->get('file_id');
            $filePath = $request->get('file_path');
            
            if (!$filePath) {
                return response()->json([
                    'success' => false,
                    'message' => 'File path is required'
                ], 400);
            }
            
            $bucket = config('storage.minio.bucket', 'detection-archive');
            $client = $this->getMinIOClient();
            
            // Check if file exists
            try {
                $client->headObject([
                    'Bucket' => $bucket,
                    'Key' => $filePath
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found'
                ], 404);
            }
            
            // Generate a presigned URL for preview (valid for 1 hour)
            $cmd = $client->getCommand('GetObject', [
                'Bucket' => $bucket,
                'Key' => $filePath
            ]);
            
            $request = $client->createPresignedRequest($cmd, '+1 hour');
            $presignedUrl = (string) $request->getUri();
            
            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];
            $videoExtensions = ['mp4', 'avi', 'mov', 'mkv', 'wmv'];
            
            $type = in_array($extension, $imageExtensions) ? 'image' : 
                   (in_array($extension, $videoExtensions) ? 'video' : 'file');
            
            return response()->json([
                'success' => true,
                'file_id' => $fileId,
                'type' => $type,
                'url' => $presignedUrl,
                'message' => 'Preview loaded successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to generate preview: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate preview'
            ], 500);
        }
    }
    
    /**
     * File download endpoint
     */
    public function download(Request $request)
    {
        try {
            $fileId = $request->get('file_id');
            $filePath = $request->get('file_path');
            
            if (!$filePath) {
                return response()->json([
                    'success' => false,
                    'message' => 'File path is required'
                ], 400);
            }
            
            $bucket = config('storage.minio.bucket', 'detection-archive');
            $client = $this->getMinIOClient();
            
            // Check if file exists
            try {
                $result = $client->headObject([
                    'Bucket' => $bucket,
                    'Key' => $filePath
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found'
                ], 404);
            }
            
            // Generate a presigned URL for download (valid for 1 hour)
            $cmd = $client->getCommand('GetObject', [
                'Bucket' => $bucket,
                'Key' => $filePath,
                'ResponseContentDisposition' => 'attachment; filename="' . basename($filePath) . '"'
            ]);
            
            $request = $client->createPresignedRequest($cmd, '+1 hour');
            $downloadUrl = (string) $request->getUri();
            
            return response()->json([
                'success' => true,
                'file_id' => $fileId,
                'download_url' => $downloadUrl,
                'filename' => basename($filePath),
                'size' => $this->formatBytes($result['ContentLength'] ?? 0),
                'message' => 'Download link generated successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to generate download link: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate download link'
            ], 500);
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes)
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        } elseif ($bytes < 1048576) {
            return round($bytes / 1024, 1) . ' KB';
        } else {
            return round($bytes / 1048576, 1) . ' MB';
        }
    }

}
