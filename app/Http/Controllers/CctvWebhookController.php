<?php

// namespace App\Http\Controllers;

// use App\Services\ActivityService;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Facades\Validator;

// class CctvWebhookController extends Controller
// {
//     /**
//      * Handle camera status change webhook from external CCTV service
//      */
//     public function cameraStatusChange(Request $request)
//     {
//         $validator = Validator::make($request->all(), [
//             'camera_id' => 'required|string',
//             'camera_name' => 'required|string',
//             'location' => 'required|string', 
//             'old_status' => 'required|string',
//             'new_status' => 'required|string',
//             'timestamp' => 'nullable|string',
//             'details' => 'nullable|string',
//         ]);

//         if ($validator->fails()) {
//             Log::warning('CCTV Webhook: Invalid camera status change payload', [
//                 'errors' => $validator->errors(),
//                 'payload' => $request->all()
//             ]);
            
//             return response()->json(['error' => 'Invalid payload'], 400);
//         }

//         try {
//             ActivityService::logCctvStatusChange(
//                 $request->camera_id,
//                 $request->camera_name,
//                 $request->location,
//                 $request->old_status,
//                 $request->new_status,
//                 'external_service',
//                 [
//                     'timestamp' => $request->timestamp,
//                     'details' => $request->details,
//                     'webhook_received_at' => now()->toISOString()
//                 ]
//             );

//             Log::info('CCTV Webhook: Camera status change logged', [
//                 'camera_id' => $request->camera_id,
//                 'camera_name' => $request->camera_name,
//                 'status_change' => $request->old_status . ' -> ' . $request->new_status
//             ]);

//             return response()->json(['status' => 'received'], 200);

//         } catch (\Exception $e) {
//             Log::error('CCTV Webhook: Failed to process camera status change', [
//                 'error' => $e->getMessage(),
//                 'payload' => $request->all()
//             ]);

//             return response()->json(['error' => 'Processing failed'], 500);
//         }
//     }

//     /**
//      * Handle service status change webhook from external CCTV service
//      */
//     public function serviceStatusChange(Request $request)
//     {
//         $validator = Validator::make($request->all(), [
//             'service_status' => 'required|string|in:online,offline,maintenance',
//             'timestamp' => 'nullable|string',
//             'details' => 'nullable|string',
//             'response_time' => 'nullable|numeric',
//         ]);

//         if ($validator->fails()) {
//             Log::warning('CCTV Webhook: Invalid service status change payload', [
//                 'errors' => $validator->errors(),
//                 'payload' => $request->all()
//             ]);
            
//             return response()->json(['error' => 'Invalid payload'], 400);
//         }

//         try {
//             ActivityService::logCctvServiceStatus(
//                 $request->service_status,
//                 $request->details ?? '',
//                 [
//                     'timestamp' => $request->timestamp,
//                     'response_time' => $request->response_time,
//                     'webhook_received_at' => now()->toISOString()
//                 ]
//             );

//             Log::info('CCTV Webhook: Service status change logged', [
//                 'service_status' => $request->service_status,
//                 'details' => $request->details
//             ]);

//             return response()->json(['status' => 'received'], 200);

//         } catch (\Exception $e) {
//             Log::error('CCTV Webhook: Failed to process service status change', [
//                 'error' => $e->getMessage(),
//                 'payload' => $request->all()
//             ]);

//             return response()->json(['error' => 'Processing failed'], 500);
//         }
//     }

//     /**
//      * Handle detection events webhook from external CCTV service
//      */
//     public function detectionEvent(Request $request)
//     {
//         $validator = Validator::make($request->all(), [
//             'camera_id' => 'required|string',
//             'camera_name' => 'required|string',
//             'location' => 'required|string',
//             'event_type' => 'required|string',
//             'confidence' => 'nullable|numeric|between:0,1',
//             'timestamp' => 'nullable|string',
//             'details' => 'nullable|string',
//         ]);

//         if ($validator->fails()) {
//             Log::warning('CCTV Webhook: Invalid detection event payload', [
//                 'errors' => $validator->errors(),
//                 'payload' => $request->all()
//             ]);
            
//             return response()->json(['error' => 'Invalid payload'], 400);
//         }

//         try {
//             ActivityService::logSecurityActivity(
//                 'detected',
//                 $request->location,
//                 "Detection event '{$request->event_type}' on camera '{$request->camera_name}'" . 
//                 ($request->confidence ? " (confidence: " . round($request->confidence * 100, 1) . "%)" : "") .
//                 ($request->details ? " - {$request->details}" : ""),
//                 'warning'
//             );

//             Log::info('CCTV Webhook: Detection event logged', [
//                 'camera_id' => $request->camera_id,
//                 'event_type' => $request->event_type,
//                 'confidence' => $request->confidence
//             ]);

//             return response()->json(['status' => 'received'], 200);

//         } catch (\Exception $e) {
//             Log::error('CCTV Webhook: Failed to process detection event', [
//                 'error' => $e->getMessage(),
//                 'payload' => $request->all()
//             ]);

//             return response()->json(['error' => 'Processing failed'], 500);
//         }
//     }
// }
