<?php

namespace App\Http\Controllers;

use App\Models\Cctv;
use App\Http\Controllers\StorageSettingsController;
use Illuminate\Http\Request;

class DetectionArchiveController extends Controller
{
    /**
     * Display the detection archive page
     */
    public function index(Request $request)
    {
        // Get cameras for dropdown
        $cameras = Cctv::select('id', 'name')->orderBy('name')->get();
        
        // Mock detection files based on filters
        $selectedCamera = $request->get('camera');
        $selectedDate = $request->get('date', date('Y-m-d'));
        
        $detectionFiles = $this->getMockDetectionFiles($selectedCamera, $selectedDate);
        
        // Get storage status
        $storageController = new StorageSettingsController();
        $storageData = $storageController->getSettings();
        $storageStatus = $storageData['storageStatus'];
        
        return view('admin.security.detection-archive', compact('cameras', 'detectionFiles', 'selectedCamera', 'selectedDate', 'storageStatus'));
    }
    
    /**
     * Generate mock detection files for demonstration
     */
    private function getMockDetectionFiles($cameraFilter = null, $date = null)
    {
        $files = [];
        $cameras = ['camera01', 'camera02', 'camera03', 'camera04'];
        
        // If specific camera is selected, filter by that camera
        if ($cameraFilter && $cameraFilter !== 'all') {
            $cameras = [$cameraFilter];
        }
        
        foreach ($cameras as $camera) {
            // Generate mock files for each camera
            for ($i = 1; $i <= 8; $i++) {
                $isVideo = rand(0, 1);
                $timestamp = strtotime($date . ' ' . sprintf('%02d:%02d:%02d', rand(0, 23), rand(0, 59), rand(0, 59)));
                
                $files[] = [
                    'id' => $camera . '_' . $i,
                    'camera' => $camera,
                    'filename' => $isVideo ? "detection_video_{$i}.mp4" : "detection_frame_{$i}.jpg",
                    'type' => $isVideo ? 'video' : 'image',
                    'size' => $isVideo ? rand(5, 25) . ' MB' : rand(200, 800) . ' KB',
                    'timestamp' => $timestamp,
                    'time_label' => date('H:i:s', $timestamp),
                    'date' => date('Y-m-d', $timestamp),
                    'detection_type' => $this->getRandomDetectionType(),
                    'confidence' => rand(70, 95),
                ];
            }
        }
        
        // Sort by timestamp descending
        usort($files, function($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
        });
        
        return $files;
    }
    
    /**
     * Get random detection type for mock data
     */
    private function getRandomDetectionType()
    {
        $types = ['Person', 'Vehicle', 'Motion', 'Face', 'Package'];
        return $types[array_rand($types)];
    }
    
    /**
     * Mock file preview endpoint
     */
    public function preview(Request $request)
    {
        $fileId = $request->get('file_id');
        
        // Return mock preview data
        return response()->json([
            'success' => true,
            'file_id' => $fileId,
            'type' => str_contains($fileId, 'video') ? 'video' : 'image',
            'url' => '/mock-preview/' . $fileId,
            'message' => 'Mock preview loaded successfully'
        ]);
    }
    
    /**
     * Mock file download endpoint
     */
    public function download(Request $request)
    {
        $fileId = $request->get('file_id');
        
        // In a real implementation, this would stream the actual file
        return response()->json([
            'success' => true,
            'file_id' => $fileId,
            'download_url' => '/mock-download/' . $fileId,
            'message' => 'Mock download initiated'
        ]);
    }
}
