<?php

namespace App\Http\Controllers;

use App\Services\CctvService;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class CctvSettingsController extends Controller
{
    protected CctvService $cctvService;

    public function __construct(CctvService $cctvService)
    {
        $this->cctvService = $cctvService;
    }

    /**
     * Display CCTV settings page
     */
    public function index()
    {
        $settings = [
            'base_url' => config('cctv.service.base_url'),
            'timeout' => config('cctv.service.timeout'),
            'retry_attempts' => config('cctv.service.retry_attempts'),
            'connect_timeout' => config('cctv.service.connect_timeout'),
        ];

        $serviceStatus = $this->cctvService->getServiceStatus();

        return view('settings.cctv', compact('settings', 'serviceStatus'));
    }

    /**
     * Update CCTV settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'base_url' => 'required|url',
            'timeout' => 'required|integer|min:5|max:300',
            'retry_attempts' => 'required|integer|min:1|max:10',
            'connect_timeout' => 'required|integer|min:5|max:60',
        ]);

        try {
            // Update .env file
            $this->updateEnvFile([
                'CCTV_SERVICE_BASE_URL' => $request->base_url,
                'CCTV_SERVICE_TIMEOUT' => $request->timeout,
                'CCTV_SERVICE_RETRY_ATTEMPTS' => $request->retry_attempts,
                'CCTV_SERVICE_CONNECT_TIMEOUT' => $request->connect_timeout,
            ]);

            // Clear config cache
            Artisan::call('config:clear');

            // Log the activity
            ActivityService::logCctvActivity(
                'service_configured',
                'CCTV Service',
                'System Configuration',
                "Service settings updated - Base URL: {$request->base_url}, Timeout: {$request->timeout}s",
                'success',
                auth()->user()
            );

            return redirect()->route('settings.cctv.index')
                ->with('success', 'CCTV settings updated successfully!');

        } catch (\Exception $e) {
            return redirect()->route('settings.cctv.index')
                ->with('error', 'Failed to update CCTV settings: ' . $e->getMessage());
        }
    }

    /**
     * Test CCTV service connection
     */
    public function testConnection()
    {
        $status = $this->cctvService->getServiceStatus();
        
        ActivityService::logCctvActivity(
            $status['status'] === 'online' ? 'service_connected' : 'service_disconnected',
            'CCTV Service',
            'Connection Test',
            "Connection test performed - Status: {$status['status']} at " . config('cctv.service.base_url'),
            $status['status'] === 'online' ? 'success' : 'error',
            auth()->user()
        );

        return response()->json($status);
    }

    /**
     * Update environment file with new values
     */
    private function updateEnvFile(array $data)
    {
        $envFile = base_path('.env');
        
        if (!File::exists($envFile)) {
            throw new \Exception('.env file not found');
        }

        $envContent = File::get($envFile);

        foreach ($data as $key => $value) {
            $pattern = "/^{$key}=.*/m";
            $replacement = "{$key}={$value}";
            
            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $replacement, $envContent);
            } else {
                $envContent .= "\n{$replacement}";
            }
        }

        File::put($envFile, $envContent);
    }
}
