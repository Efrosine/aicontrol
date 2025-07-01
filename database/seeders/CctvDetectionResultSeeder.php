<?php

namespace Database\Seeders;

use App\Models\Cctv;
use App\Models\CctvDetectionResult;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CctvDetectionResultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First check if we have any CCTVs in the database
        $cctvs = Cctv::all();

        // If no CCTVs exist, create one for our seeder
        if ($cctvs->isEmpty()) {
            $cctv = Cctv::create([
                'name' => 'Demo CCTV',
                'origin_url' => 'http://demo-camera.example.com',
                'stream_url' => 'rtsp://demo-camera.example.com/stream',
                'location' => 'Main Entrance'
            ]);
        } else {
            $cctv = $cctvs->first();
        }

        // Create sample detection results
        CctvDetectionResult::create([
            'cctv_id' => $cctv->id,
            'data' => json_encode([
                'detection_type' => 'Person',
                'confidence' => 0.95,
                'bounding_box' => [
                    'x' => 120,
                    'y' => 80,
                    'width' => 60,
                    'height' => 180
                ],
                'timestamp' => now()->timestamp
            ]),
            'snapshoot_url' => 'detections/sample-person-detection.jpg',
        ]);

        CctvDetectionResult::create([
            'cctv_id' => $cctv->id,
            'data' => json_encode([
                'detection_type' => 'Vehicle',
                'confidence' => 0.89,
                'bounding_box' => [
                    'x' => 300,
                    'y' => 150,
                    'width' => 200,
                    'height' => 120
                ],
                'timestamp' => now()->subMinutes(30)->timestamp
            ]),
            'snapshoot_url' => 'detections/sample-vehicle-detection.jpg',
        ]);

        CctvDetectionResult::create([
            'cctv_id' => $cctv->id,
            'data' => json_encode([
                'detection_type' => 'Suspicious Activity',
                'confidence' => 0.75,
                'bounding_box' => [
                    'x' => 200,
                    'y' => 100,
                    'width' => 150,
                    'height' => 150
                ],
                'timestamp' => now()->subHours(2)->timestamp
            ]),
            'snapshoot_url' => 'detections/sample-activity-detection.jpg',
        ]);
    }
}
