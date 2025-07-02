<?php

namespace App\Http\Controllers;

use App\Enums\BroadcastDataType;
use App\Models\SenderNumber;
use App\Services\BroadcastService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class CctvUploadController extends Controller
{
    /**
     * Handle public CCTV file upload from third-party clients
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'file' => 'required|file|max:204800|mimes:jpg,jpeg,png,mp4,avi,mov', // 200MB max
                'cctv_name' => 'required|string|max:255',
                'detection_type' => 'required|string|max:50', 
                'timestamp' => 'nullable|date_format:c', 
            ]);

            if ($validator->fails()) {
                Log::warning('CCTV upload validation failed', [
                    'errors' => $validator->errors(),
                    'request_data' => $request->except(['file'])
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 400);
            }

            $file = $request->file('file');
            $cctvName = $request->input('cctv_name');
            $detectionType = $request->input('detection_type');
            $timestamp = $request->input('timestamp');

            // Use provided timestamp or current time
            $dateTime = $timestamp ? Carbon::parse($timestamp) : Carbon::now();
            
            // Extract file information
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            
            // Sanitize camera name for use in file path
            $sanitizedCctvName = $this->sanitizeForPath($cctvName);
            
            // Build MinIO storage path: {camera-name}/{yyyy}/{mm}/{dd}/{detection_type}/{original_filename}.{ext}
            $storagePath = sprintf(
                '%s/%s/%s/%s/%s/%s.%s',
                $sanitizedCctvName,
                $dateTime->format('Y'),
                $dateTime->format('m'),
                $dateTime->format('d'),
                $detectionType,
                $originalFilename,
                $extension
            );

            // Upload to MinIO
            $uploadResult = $this->uploadToMinIO($file, $storagePath);
            
            
            if (!$uploadResult['success']) {
                Log::error('Failed to upload file to MinIO', [
                    'storage_path' => $storagePath,
                    'error' => $uploadResult['error']
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to upload file to storage',
                    'error' => $uploadResult['error']
                ], 500);
            }

            // Generate the file URL for the broadcast
            $fileUrl = url('api/archive/fetch/' . $storagePath);

            // Send broadcast notification with the file URL
            try {
                // Get the first available sender number
                $sender = SenderNumber::first();
                
                if ($sender) {
                    $broadcastService = new BroadcastService();
                    $broadcastResult = $broadcastService->sendBroadcast(
                        $sender,
                        BroadcastDataType::CCTV,
                        null,  // resultId is null since we're sending a file
                        true,  // isFile set to true
                        $fileUrl
                    );
                    
                    Log::info('Broadcast notification sent', [
                        'broadcast_result' => $broadcastResult,
                        'file_url' => $fileUrl
                    ]);
                } else {
                    Log::warning('No sender number found for broadcast notification');
                }
            } catch (\Exception $e) {
                // Log but don't stop execution if broadcast fails
                Log::error('Failed to send broadcast notification', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }

            Log::info('CCTV file uploaded successfully', [
                'cctv_name' => $cctvName,
                'detection_type' => $detectionType,
                'storage_path' => $storagePath,
                'file_size' => $file->getSize(),
                'timestamp' => $dateTime->toISOString()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'data' => [
                    'storage_path' => $storagePath,
                    'cctv_name' => $cctvName,
                    'detection_type' => $detectionType,
                    'timestamp' => $dateTime->toISOString(),
                    'file_size' => $this->formatFileSize($file->getSize()),
                    'original_filename' => $file->getClientOriginalName()
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('CCTV upload failed with exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'error' => 'An unexpected error occurred while processing the upload'
            ], 500);
        }
    }

    /**
     * Upload file to MinIO storage
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $storagePath
     * @return array
     */
    private function uploadToMinIO($file, $storagePath)
    {
        try {
            $client = $this->getMinIOClient();
            $bucket = config('storage.minio.bucket', 'detection-archive');
            
            // Check if bucket exists, create if it doesn't
            if (!$client->doesBucketExist($bucket)) {
                $client->createBucket($bucket);
                Log::info('Created MinIO bucket', ['bucket' => $bucket]);
            }
            
            // Upload file
            $result = $client->putObject([
                'Bucket' => $bucket,
                'Key' => $storagePath,
                'Body' => fopen($file->getPathname(), 'rb'),
                'ContentType' => $file->getMimeType(),
                'Metadata' => [
                    'original-filename' => $file->getClientOriginalName(),
                    'upload-timestamp' => Carbon::now()->toISOString(),
                    'file-size' => (string) $file->getSize()
                ]
            ]);
            
            return [
                'success' => true,
                'etag' => $result['ETag'] ?? null,
                'version_id' => $result['VersionId'] ?? null
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get MinIO S3 client instance
     * 
     * @return \Aws\S3\S3Client
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
     * Sanitize string for use in file paths
     * 
     * @param string $input
     * @return string
     */
    private function sanitizeForPath($input)
    {
        // Remove or replace problematic characters for file paths
        $sanitized = preg_replace('/[^a-zA-Z0-9\-_]/', '_', $input);
        
        // Remove multiple consecutive underscores
        $sanitized = preg_replace('/_{2,}/', '_', $sanitized);
        
        // Trim underscores from start and end
        return trim($sanitized, '_');
    }

    /**
     * Format file size in human readable format
     * 
     * @param int $sizeBytes
     * @return string
     */
    private function formatFileSize($sizeBytes)
    {
        if ($sizeBytes < 1024) {
            return $sizeBytes . ' B';
        } elseif ($sizeBytes < 1048576) {
            return round($sizeBytes / 1024, 1) . ' KB';
        } elseif ($sizeBytes < 1073741824) {
            return round($sizeBytes / 1048576, 1) . ' MB';
        } else {
            return round($sizeBytes / 1073741824, 1) . ' GB';
        }
    }
}
