<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\RequestException;
use Exception;

class CctvService
{
    protected string $baseUrl;
    protected int $timeout;
    protected int $retryAttempts;
    protected int $connectTimeout;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('cctv.service.base_url', 'http://127.0.0.1:8000'), '/');
        $this->timeout = config('cctv.service.timeout', 30);
        $this->retryAttempts = config('cctv.service.retry_attempts', 3);
        $this->connectTimeout = config('cctv.service.connect_timeout', 10);
    }

    /**
     * Get all cameras from the external service
     */
    public function getAllCameras()
    {
        try {
            $response = Http::timeout($this->timeout)
                ->connectTimeout($this->connectTimeout)
                ->retry($this->retryAttempts, 1000)
                ->get($this->baseUrl . config('cctv.endpoints.cameras'));

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('CCTV Service: Failed to get cameras', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return null;
        } catch (RequestException $e) {
            Log::error('CCTV Service: Request exception getting cameras', [
                'message' => $e->getMessage(),
                'url' => $this->baseUrl . config('cctv.endpoints.cameras')
            ]);
            return null;
        }
    }

    /**
     * Get a specific camera by ID
     */
    public function getCamera($id)
    {
        try {
            $response = Http::timeout($this->timeout)
                ->connectTimeout($this->connectTimeout)
                ->retry($this->retryAttempts, 1000)
                ->get($this->baseUrl . config('cctv.endpoints.cameras') . '/' . $id);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('CCTV Service: Failed to get camera', [
                'camera_id' => $id,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return null;
        } catch (RequestException $e) {
            Log::error('CCTV Service: Request exception getting camera', [
                'camera_id' => $id,
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Create a new camera
     */
    public function createCamera($data)
    {
        try {
            $response = Http::timeout($this->timeout)
                ->connectTimeout($this->connectTimeout)
                ->retry($this->retryAttempts, 1000)
                ->post($this->baseUrl . config('cctv.endpoints.cameras'), $data);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('CCTV Service: Failed to create camera', [
                'data' => $data,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return null;
        } catch (RequestException $e) {
            Log::error('CCTV Service: Request exception creating camera', [
                'data' => $data,
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Update an existing camera
     */
    public function updateCamera($id, $data)
    {
        try {
            $response = Http::timeout($this->timeout)
                ->connectTimeout($this->connectTimeout)
                ->retry($this->retryAttempts, 1000)
                ->put($this->baseUrl . config('cctv.endpoints.cameras') . '/' . $id, $data);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('CCTV Service: Failed to update camera', [
                'camera_id' => $id,
                'data' => $data,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return null;
        } catch (RequestException $e) {
            Log::error('CCTV Service: Request exception updating camera', [
                'camera_id' => $id,
                'data' => $data,
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Delete a camera
     */
    public function deleteCamera($id)
    {
        try {
            $response = Http::timeout($this->timeout)
                ->connectTimeout($this->connectTimeout)
                ->retry($this->retryAttempts, 1000)
                ->delete($this->baseUrl . config('cctv.endpoints.cameras') . '/' . $id);

            if ($response->successful()) {
                return true;
            }

            Log::error('CCTV Service: Failed to delete camera', [
                'camera_id' => $id,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return false;
        } catch (RequestException $e) {
            Log::error('CCTV Service: Request exception deleting camera', [
                'camera_id' => $id,
                'message' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get stream URL for a camera
     */
    public function getStreamUrl($cameraId)
    {
        return $this->baseUrl . config('cctv.endpoints.stream') . '/' . $cameraId;
    }

    /**
     * Get detection configuration
     */
    public function getDetectionConfig()
    {
        try {
            $response = Http::timeout($this->timeout)
                ->connectTimeout($this->connectTimeout)
                ->retry($this->retryAttempts, 1000)
                ->get($this->baseUrl . config('cctv.endpoints.detection_config'));

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('CCTV Service: Failed to get detection config', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return null;
        } catch (RequestException $e) {
            Log::error('CCTV Service: Request exception getting detection config', [
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Update detection configuration
     */
    public function updateDetectionConfig($config)
    {
        try {
            $response = Http::timeout($this->timeout)
                ->connectTimeout($this->connectTimeout)
                ->retry($this->retryAttempts, 1000)
                ->put($this->baseUrl . config('cctv.endpoints.detection_config'), $config);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('CCTV Service: Failed to update detection config', [
                'config' => $config,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return null;
        } catch (RequestException $e) {
            Log::error('CCTV Service: Request exception updating detection config', [
                'config' => $config,
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Test connection to the CCTV service
     */
    public function testConnection()
    {
        try {
            $response = Http::timeout($this->connectTimeout)
                ->get($this->baseUrl . '/health');

            return $response->successful();
        } catch (Exception $e) {
            Log::error('CCTV Service: Connection test failed', [
                'message' => $e->getMessage(),
                'url' => $this->baseUrl
            ]);
            return false;
        }
    }

    /**
     * Get service status and health information
     */
    public function getServiceStatus()
    {
        try {
            $response = Http::timeout($this->connectTimeout)
                ->get($this->baseUrl . '/health');

            if ($response->successful()) {
                return [
                    'status' => 'online',
                    'response_time' => $response->transferStats->getTransferTime(),
                    'data' => $response->json()
                ];
            }

            return [
                'status' => 'error',
                'error' => 'HTTP ' . $response->status(),
                'response' => $response->body()
            ];
        } catch (Exception $e) {
            return [
                'status' => 'offline',
                'error' => $e->getMessage()
            ];
        }
    }
}
