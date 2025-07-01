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
            if ($dataType === BroadcastDataType::CCTV->value) {
                $results = CctvDetectionResult::orderBy('created_at', 'desc')->limit(50)->get();
                \Log::info('CCTV results found: ' . count($results));

                // Debug the first result to see its structure
                if ($results->isNotEmpty()) {
                    \Log::info('Sample CCTV result:', [
                        'id' => $results->first()->id,
                        'data_type' => gettype($results->first()->data),
                        'data' => $results->first()->data,
                    ]);
                }
            } else if ($dataType === BroadcastDataType::SOCIAL_MEDIA_SCRAPER->value) {
                $results = SocialDetectionResult::orderBy('created_at', 'desc')->limit(50)->get();
                \Log::info('Social media results found: ' . count($results));

                // Debug the first result to see its structure
                if ($results->isNotEmpty()) {
                    \Log::info('Sample social result:', [
                        'id' => $results->first()->id,
                        'data_type' => gettype($results->first()->data),
                        'data' => $results->first()->data,
                    ]);
                }
            } else {
                $results = collect([]);
                \Log::warning('Unknown data type: ' . $dataType);
            }

            \Log::info('Fetched detection results', [
                'data_type' => $dataType,
                'count' => count($results),
                'sample' => $results->take(1)
            ]);

            return response()->json(['results' => $results]);
        } catch (\Exception $e) {
            \Log::error('Error fetching detection results', [
                'error' => $e->getMessage(),
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

        if ($validator->fails()) {
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

    /**
     * Test endpoint to check detection results
     */
    public function testDetectionResults(string $type)
    {
        \Log::info('Test detection results for: ' . $type);

        $results = [];
        $message = '';

        try {
            if ($type === 'cctv') {
                // Always provide dummy CCTV detection results for testing
                $results = [
                    [
                        'id' => 1,
                        'created_at' => now()->toDateTimeString(),
                        'data' => [
                            'detection_type' => 'Person',
                            'confidence' => 0.95
                        ]
                    ],
                    [
                        'id' => 2,
                        'created_at' => now()->subMinutes(5)->toDateTimeString(),
                        'data' => [
                            'detection_type' => 'Vehicle',
                            'confidence' => 0.87
                        ]
                    ],
                    [
                        'id' => 3,
                        'created_at' => now()->subMinutes(10)->toDateTimeString(),
                        'data' => [
                            'detection_type' => 'Animal',
                            'confidence' => 0.76
                        ]
                    ]
                ];

                $message = 'Found ' . count($results) . ' CCTV detection results';
                \Log::info('Returning CCTV test results: ' . count($results));

            } else if ($type === 'social') {
                // Always provide dummy social media detection results for testing
                $results = [
                    [
                        'id' => 1,
                        'created_at' => now()->toDateTimeString(),
                        'data' => [
                            'platform' => 'Instagram',
                            'account_name' => 'test_account'
                        ]
                    ],
                    [
                        'id' => 2,
                        'created_at' => now()->subHours(1)->toDateTimeString(),
                        'data' => [
                            'platform' => 'Twitter',
                            'account_name' => 'another_account'
                        ]
                    ],
                    [
                        'id' => 3,
                        'created_at' => now()->subHours(2)->toDateTimeString(),
                        'data' => [
                            'platform' => 'Facebook',
                            'account_name' => 'fb_test_user'
                        ]
                    ]
                ];

                $message = 'Found ' . count($results) . ' social media detection results';
                \Log::info('Returning social media test results: ' . count($results));
            } else {
                return response()->json(['error' => 'Invalid type'], 400);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'results' => $results
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in test endpoint', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
