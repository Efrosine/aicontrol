<?php

namespace App\Http\Controllers;

use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

class StorageSettingsController extends Controller
{
    /**
     * Display storage settings
     */
    public function getSettings()
    {
        $settings = [
            'endpoint' => config('storage.minio.endpoint'),
            'access_key' => config('storage.minio.access_key'),
            'secret_key' => config('storage.minio.secret_key'),
            'bucket' => config('storage.minio.bucket'),
            'timeout' => config('storage.connection.timeout'),
            'retry_attempts' => config('storage.connection.retry_attempts'),
        ];

        $storageStatus = $this->getStorageStatus();

        return compact('settings', 'storageStatus');
    }

    /**
     * Update storage settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|string',
            'access_key' => 'required|string',
            'secret_key' => 'required|string',
            'bucket' => 'required|string',
            'timeout' => 'required|integer|min:5|max:300',
            'retry_attempts' => 'required|integer|min:1|max:10',
        ]);

        try {
            // Update .env file
            $this->updateEnvFile([
                'MINIO_ENDPOINT' => $request->endpoint,
                'MINIO_ACCESS_KEY' => $request->access_key,
                'MINIO_SECRET_KEY' => $request->secret_key,
                'MINIO_BUCKET' => $request->bucket,
                'MINIO_TIMEOUT' => $request->timeout,
                'MINIO_RETRY_ATTEMPTS' => $request->retry_attempts,
            ]);

            // Clear config cache
            Artisan::call('config:clear');

            // Log activity
            ActivityService::logSystemActivity(
                'updated',
                'storage',
                "Storage settings updated: endpoint={$request->endpoint}, bucket={$request->bucket}",
                'success'
            );

            return response()->json([
                'success' => true,
                'message' => 'Storage settings updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update storage settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test storage connection
     */
    public function testConnection()
    {
        try {
            $status = $this->getStorageStatus();
            
            return response()->json($status);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'offline',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get storage service status
     */
    private function getStorageStatus()
    {
        try {
            $endpoint = config('storage.minio.endpoint');
            $accessKey = config('storage.minio.access_key');
            $secretKey = config('storage.minio.secret_key');
            
            // Parse endpoint to get host and port
            $parsedEndpoint = parse_url('http://' . $endpoint);
            $host = $parsedEndpoint['host'] ?? $endpoint;
            $port = $parsedEndpoint['port'] ?? 9000;
            
            // Simple connection test to MinIO health endpoint
            $healthUrl = "http://{$host}:{$port}/minio/health/live";
            
            $startTime = microtime(true);
            $response = Http::timeout(config('storage.connection.timeout', 30))
                ->get($healthUrl);
            $responseTime = microtime(true) - $startTime;

            if ($response->successful()) {
                return [
                    'status' => 'online',
                    'response_time' => $responseTime,
                    'endpoint' => $endpoint,
                ];
            } else {
                return [
                    'status' => 'offline',
                    'error' => 'HTTP ' . $response->status(),
                    'endpoint' => $endpoint,
                ];
            }
        } catch (\Exception $e) {
            return [
                'status' => 'offline',
                'error' => $e->getMessage(),
                'endpoint' => config('storage.minio.endpoint'),
            ];
        }
    }

    /**
     * Update environment file
     */
    private function updateEnvFile(array $data)
    {
        $envFile = base_path('.env');
        $envContent = File::get($envFile);

        foreach ($data as $key => $value) {
            $value = trim($value, '"\'');
            $value = '"' . $value . '"';
            
            if (str_contains($envContent, $key . '=')) {
                $envContent = preg_replace(
                    '/^' . preg_quote($key, '/') . '=.*$/m',
                    $key . '=' . $value,
                    $envContent
                );
            } else {
                $envContent .= "\n" . $key . '=' . $value;
            }
        }

        File::put($envFile, $envContent);
    }
}
