<?php

namespace App\Http\Controllers;

use App\Services\CctvService;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CctvController extends Controller
{
    protected CctvService $cctvService;

    public function __construct(CctvService $cctvService)
    {
        $this->cctvService = $cctvService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cameras = $this->cctvService->getAllCameras();
        
        if ($cameras === null) {
            return view('cctvs.index', [
                'cameras' => [],
                'error' => 'Unable to connect to CCTV service. Please check your settings.'
            ]);
        }

        return view('cctvs.index', compact('cameras'));
    }

    /**
     * Display a list of CCTV cameras for non-admin users.
     */
    public function userView()
    {
        $cameras = $this->cctvService->getAllCameras();
        
        if ($cameras === null) {
            return view('cctvs.user-view', [
                'cameras' => [],
                'error' => 'Unable to connect to CCTV service.'
            ]);
        }

        return view('cctvs.user-view', compact('cameras'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('cctvs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'ip_address' => 'required|string|max:255', // Allow URLs and IPs
            'location' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('cctvs.create')
                ->withErrors($validator)
                ->withInput();
        }

        // Only send the fields required by the external API
        $cameraData = [
            'name' => $request->name,
            'ip_address' => $request->ip_address,
            'location' => $request->location,
            'status' => 'active', // Always set to active as required by API
        ];

        $result = $this->cctvService->createCamera($cameraData);

        if ($result === null) {
            return redirect()
                ->route('cctvs.create')
                ->withInput()
                ->with('error', 'Failed to create camera. Please check the CCTV service connection.');
        }

        ActivityService::logCctvActivity(
            'created',
            $request->name,
            $request->location,
            "Camera added via web interface with IP: {$request->ip_address}",
            'success',
            auth()->user()
        );

        return redirect()
            ->route('cctvs.index')
            ->with('success', 'CCTV camera added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $camera = $this->cctvService->getCamera($id);
        
        if ($camera === null) {
            return redirect()
                ->route('cctvs.index')
                ->with('error', 'Camera not found or service unavailable.');
        }

        // Get detection configuration
        $detectionConfig = $this->cctvService->getDetectionConfig();
        
        // Get public stream URL (proxied through Laravel app)
        $streamUrl = $this->cctvService->getPublicStreamUrl($id);

        return view('cctvs.show', compact('camera', 'detectionConfig', 'streamUrl'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $camera = $this->cctvService->getCamera($id);
        
        if ($camera === null) {
            return redirect()
                ->route('cctvs.index')
                ->with('error', 'Camera not found or service unavailable.');
        }

        return view('cctvs.edit', compact('camera'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'ip_address' => 'required|string|max:255', // Allow URLs and IPs
            'location' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('cctvs.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }

        // Only send the fields required by the external API
        $cameraData = [
            'name' => $request->name,
            'ip_address' => $request->ip_address,
            'location' => $request->location,
            'status' => 'active', // Always set to active as required by API
        ];

        $result = $this->cctvService->updateCamera($id, $cameraData);

        if ($result === null) {
            return redirect()
                ->route('cctvs.edit', $id)
                ->withInput()
                ->with('error', 'Failed to update camera. Please check the CCTV service connection.');
        }

        ActivityService::logCctvActivity(
            'updated',
            $request->name,
            $request->location,
            "Camera configuration updated via web interface",
            'info',
            auth()->user()
        );

        return redirect()
            ->route('cctvs.index')
            ->with('success', 'CCTV camera updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Get camera info for logging before deletion
        $camera = $this->cctvService->getCamera($id);
        
        $result = $this->cctvService->deleteCamera($id);

        if (!$result) {
            return redirect()
                ->route('cctvs.index')
                ->with('error', 'Failed to delete camera. Please check the CCTV service connection.');
        }

        if ($camera) {
            ActivityService::logCctvActivity(
                'deleted',
                $camera['name'] ?? 'Unknown Camera',
                $camera['location'] ?? 'Unknown location',
                "Camera removed via web interface",
                'warning',
                auth()->user()
            );
        }

        return redirect()
            ->route('cctvs.index')
            ->with('success', 'CCTV camera deleted successfully.');
    }

    /**
     * Update detection configuration
     */
    public function updateDetectionConfig(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'record_duration' => 'integer|min:10|max:300',
            'enable_video' => 'boolean',
            'enable_screenshot' => 'boolean',
            'external_endpoint' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $config = [
            'record_duration' => $request->integer('record_duration', 30),
            'enable_video' => $request->boolean('enable_video'),
            'enable_screenshot' => $request->boolean('enable_screenshot'),
            'external_endpoint' => $request->external_endpoint,
        ];

        $result = $this->cctvService->updateDetectionConfig($config);

        if ($result === null) {
            return response()->json(['error' => 'Failed to update detection configuration'], 500);
        }

        ActivityService::logCctvActivity(
            'detection_updated',
            'System',
            'Global Settings',
            "Detection configuration updated: record_duration={$config['record_duration']}s, video=" . ($config['enable_video'] ? 'enabled' : 'disabled') . ", screenshot=" . ($config['enable_screenshot'] ? 'enabled' : 'disabled'),
            'info',
            auth()->user()
        );

        return response()->json(['success' => true, 'config' => $result]);
    }

    /**
     * Get live stream for a camera
     */
    public function stream($id)
    {
        $streamUrl = $this->cctvService->getStreamUrl($id);

        if (!$streamUrl) {
            abort(404, 'Camera stream not available');
        }

        // Redirect to the external stream URL
        return redirect($streamUrl);
    }

    /**
     * Proxy MJPEG stream from local CCTV service to make it publicly accessible
     */
    public function streamProxy($id)
    {
        // Verify camera exists
        $camera = $this->cctvService->getCamera($id);
        if ($camera === null) {
            abort(404, 'Camera not found');
        }

        // Check if camera is active
        if (!isset($camera['status']) || $camera['status'] !== 'active') {
            abort(503, 'Camera is offline or inactive');
        }

        // Get the local stream URL
        $localStreamUrl = $this->cctvService->getStreamUrl($id);
        
        try {
            \Log::info('Starting MJPEG stream proxy', [
                'camera_id' => $id,
                'camera_name' => $camera['name'] ?? 'Unknown',
                'local_stream_url' => $localStreamUrl
            ]);

            // Create a streaming response that proxies the MJPEG stream
            return response()->stream(function() use ($localStreamUrl, $id) {
                // Set up stream context with appropriate settings for MJPEG
                $context = stream_context_create([
                    'http' => [
                        'timeout' => 300, // 5 minutes timeout for streaming
                        'method' => 'GET',
                        'header' => [
                            'User-Agent: Laravel-CCTV-Proxy/1.0',
                            'Accept: multipart/x-mixed-replace,*/*',
                            'Connection: keep-alive'
                        ],
                        'ignore_errors' => false
                    ]
                ]);

                // Open the stream from the local CCTV service
                $stream = @fopen($localStreamUrl, 'r', false, $context);
                
                if (!$stream) {
                    \Log::error('Failed to open MJPEG stream', [
                        'camera_id' => $id,
                        'stream_url' => $localStreamUrl,
                        'error' => error_get_last()
                    ]);
                    
                    // Send a simple error message in MJPEG format
                    echo "--frame\r\n";
                    echo "Content-Type: text/plain\r\n\r\n";
                    echo "Camera stream unavailable\r\n";
                    echo "--frame--\r\n";
                    return;
                }

                // Stream the data in chunks
                while (!feof($stream)) {
                    $chunk = fread($stream, 8192); // Read 8KB chunks
                    if ($chunk !== false && strlen($chunk) > 0) {
                        echo $chunk;
                        
                        // Flush output buffers
                        if (ob_get_level()) {
                            ob_flush();
                        }
                        flush();
                    }
                    
                    // Check if client disconnected
                    if (connection_aborted()) {
                        \Log::info('MJPEG stream client disconnected', ['camera_id' => $id]);
                        break;
                    }
                    
                    // Small delay to prevent excessive CPU usage
                    usleep(1000); // 1ms delay
                }
                
                fclose($stream);
                \Log::info('MJPEG stream ended', ['camera_id' => $id]);
                
            }, 200, [
                'Content-Type' => 'multipart/x-mixed-replace; boundary=frame',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
                'Connection' => 'keep-alive',
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'GET',
                'Access-Control-Allow-Headers' => 'Content-Type'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('CCTV Stream Proxy Error', [
                'camera_id' => $id,
                'stream_url' => $localStreamUrl,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            abort(503, 'Camera stream temporarily unavailable');
        }
    }

    /**
     * Get camera status and health information
     */
    public function status($id)
    {
        $camera = $this->cctvService->getCamera($id);
        
        if ($camera === null) {
            return response()->json(['error' => 'Camera not found'], 404);
        }

        return response()->json([
            'camera' => $camera,
            'stream_url' => $this->cctvService->getPublicStreamUrl($id),
            'detection_config' => $this->cctvService->getDetectionConfig(),
        ]);
    }

    /**
     * Display CCTV settings page
     */
    public function settings()
    {
        return app(CctvSettingsController::class)->index();
    }
}
