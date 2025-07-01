<?php

namespace App\Http\Controllers;

use App\Enums\BroadcastDataType;
use App\Models\CctvDetectionResult;
use App\Models\SenderNumber;
use App\Models\SocialDetectionResult;
use App\Services\BroadcastService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;



class BroadcastController extends Controller
{
    protected $broadcastService;

    public function __construct(BroadcastService $broadcastService)
    {
        $this->broadcastService = $broadcastService;
    }

    /**
     * Show the form for sending a broadcast
     */
    public function showSendForm()
    {
        $senders = SenderNumber::all();
        $dataTypes = [
            BroadcastDataType::CCTV->value => 'CCTV Detection',
            BroadcastDataType::SOCIAL_MEDIA_SCRAPER->value => 'Social Media Scraper'
        ];

        return view('broadcast.send', compact('senders', 'dataTypes'));
    }

    /**
     * Get detection results based on data type
     */
    public function getDetectionResults(Request $request)
    {
        \Log::info('getDetectionResults called with data:', $request->all());

        $validator = Validator::make($request->all(), [
            'data_type' => 'required|string|in:' . implode(',', [
                BroadcastDataType::CCTV->value,
                BroadcastDataType::SOCIAL_MEDIA_SCRAPER->value
            ])
        ]);

        if ($validator->fails()) {
            \Log::error('Validation failed', ['errors' => $validator->errors()]);
            return response()->json(['error' => 'Invalid data type', 'details' => $validator->errors()], 400);
        }

        $dataType = $request->input('data_type');
        \Log::info('Processing data type: ' . $dataType);

        try {
            $formattedResults = [];

            if ($dataType === BroadcastDataType::CCTV->value) {
                $results = CctvDetectionResult::with('cctv')->orderBy('created_at', 'desc')->limit(50)->get();
                \Log::info('CCTV results found: ' . count($results));

                foreach ($results as $result) {
                    $data = is_array($result->data) ? $result->data : json_decode($result->data, true);
                    $detectionType = $data['detection_type'] ?? 'Unknown';
                    $confidence = isset($data['confidence']) ? round($data['confidence'] * 100, 1) : 0;
                    $cameraName = $result->cctv->name ?? 'Unknown Camera';

                    $formattedResults[] = [
                        'id' => $result->id,
                        'created_at' => $result->created_at->format('Y-m-d H:i:s'),
                        'summary' => "Camera: {$cameraName}, Detection: {$detectionType} ({$confidence}%)"
                    ];
                }
            } else if ($dataType === BroadcastDataType::SOCIAL_MEDIA_SCRAPER->value) {
                $results = SocialDetectionResult::orderBy('created_at', 'desc')->limit(50)->get();
                \Log::info('Social media results found: ' . count($results));

                foreach ($results as $result) {
                    $data = is_array($result->data) ? $result->data : json_decode($result->data, true);
                    $platform = $data['platform'] ?? 'Unknown Platform';
                    $accountName = $data['account_name'] ?? $data['username'] ?? 'Unknown Account';
                    $content = isset($data['text']) ? \Illuminate\Support\Str::limit($data['text'], 30) : 'No content';

                    $formattedResults[] = [
                        'id' => $result->id,
                        'created_at' => $result->created_at->format('Y-m-d H:i:s'),
                        'summary' => "{$platform}: {$accountName} - {$content}"
                    ];
                }
            }

            \Log::info('Formatted detection results', [
                'data_type' => $dataType,
                'count' => count($formattedResults),
                'sample' => array_slice($formattedResults, 0, 1)
            ]);

            return response()->json([
                'success' => true,
                'data_type' => $dataType,
                'results' => $formattedResults
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching detection results', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data_type' => $dataType
            ]);

            return response()->json([
                'error' => 'Failed to fetch detection results',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send a broadcast message
     */
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sender_id' => 'required|exists:sender_numbers,id',
            'data_type' => 'required|string|in:' . implode(',', [
                BroadcastDataType::CCTV->value,
                BroadcastDataType::SOCIAL_MEDIA_SCRAPER->value
            ]),
            'result_id' => 'required|integer|min:1',
        ]);
        \Log::info($request);

        if ($validator->fails()) {
            \Log::error('Validation failed', ['errors' => $validator->errors()]);
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please correct the errors below.');
        }

        $sender = SenderNumber::findOrFail($request->input('sender_id'));
        $dataType = BroadcastDataType::from($request->input('data_type'));
        $resultId = $request->input('result_id');

        $result = $this->broadcastService->sendBroadcast($sender, $dataType, $resultId);

        if ($result['success']) {
            return redirect()->route('broadcast.send')
                ->with('success', $result['message']);
        } else {
            return redirect()->back()
                ->with('error', $result['message'])
                ->withInput();
        }
    }
}
