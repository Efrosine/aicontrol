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
        
        // Get stream URL
        $streamUrl = $this->cctvService->getStreamUrl($id);

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
        
        // Redirect to the external stream URL
        return redirect($streamUrl);
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
            'stream_url' => $this->cctvService->getStreamUrl($id),
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
