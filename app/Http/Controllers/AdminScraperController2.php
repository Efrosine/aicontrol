<?php

namespace App\Http\Controllers;

use App\Models\ScrapedData;
use App\Models\ScrapedResult;
use App\Models\ScrapedDataResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\DummyAccount;
use Illuminate\Support\Facades\Log;

class AdminScraperController2 extends Controller
{
    public function showForm()
    {
        $dummyAccounts = DummyAccount::all(['id', 'username', 'platform']);
        $platforms = [
            'ig' => 'Instagram',
            'x' => 'X (Twitter)',
            'twitter' => 'Twitter',
        ];
        return view('admin.scraper_form', compact('dummyAccounts', 'platforms'));
    }

    public function submit(Request $request)
    {
        // Log request data for debugging
        Log::info('Request data: ' . json_encode($request->all()));

        $validated = $request->validate([
            'platform' => 'required|in:ig,x,twitter',
            'accounts' => 'required|string|exists:dummy_accounts,username',
            'suspected_account' => 'required|string',
            'post_count' => 'required|integer|min:1',
            'comment_count' => 'required|integer|min:1',
        ]);

        // Fetch username/password for the single account
        $account = DummyAccount::where('username', $validated['accounts'])
            ->first(['username', 'password']);

        if (!$account) {
            Log::error('Account not found: ' . $validated['accounts']);
            return back()->withErrors(['accounts' => 'Selected account not found.']);
        }

        // Prepare payload with accounts as array containing one object
        $payload = [
            'suspected_account' => $validated['suspected_account'],
            'post_count' => (int) $validated['post_count'],
            'comment_count' => (int) $validated['comment_count'],
        ];

        // Convert payload to JSON string
        $payloadJson = json_encode($payload);


        Log::info('Payload to ig-scraper: ' . $payloadJson);

        // Make HTTP POST to ig-scraper with raw JSON body
       $response = Http::acceptJson()
       ->timeout(0) // Set a timeout for the request
    ->post('http://ig-scraper:5000/scrape', $payload);


        // Log response for debugging
        Log::info('Response from ig-scraper: ' . $response->body());

        // Check if response is successful
        if ($response->failed()) {
            Log::error('ig-scraper request failed: ' . $response->body());
            return back()->withErrors(['scraper' => 'Failed to fetch data from scraper.']);
        }

$results = $response->json('results');
      // Filter results to include only desired fields
$filteredResults = array_map(function ($result) {
    return [
        'caption' => $result['caption'],
        'comments' => array_values($result['comments']), // Convert comments object to array
    ];
}, $results);

// Save input parameters
$scrapedData = ScrapedData::create([
    'input_query' => $payloadJson,
]);

// Save filtered results
$scrapedResult = ScrapedResult::create([
    'account' => $validated['suspected_account'],
    'data' => json_encode($filteredResults),
    'url' => $results[0]['url'] ?? '', // Assuming the first result has the URL
]);

        // Link the records
        ScrapedDataResult::create([
            'scraped_data_id' => $scrapedData->id,
            'scraped_result_id' => $scrapedResult->id,
        ]);

        return redirect()->route('admin.scraper.results', ['id' => $scrapedResult->id]);
    }

    public function showResults($id)
    {
        $result = ScrapedResult::findOrFail($id);
        Log::info('Showing results for text: ' . $result->data);
        $decoded = json_decode($result->data, true);
        $text = '';
        foreach ($decoded as $item) {
            $text .= ($item['caption'] ?? '') . "\n";
            if (!empty($item['comments']) && is_array($item['comments'])) {
            foreach ($item['comments'] as $comment) {
                $text .= ($comment ?? '') . "\n";
            }
            }
        }
        $text = trim($text);
        return view('admin.scraper_results', ['result' => $result, 'text' => $text]);
    }

    public function index()
    {
        $results = ScrapedResult::orderByDesc('created_at')->get();
        return view('admin.scraper_results_list', compact('results'));
    }

    public function userIndex()
    {
        $results = ScrapedResult::orderByDesc('created_at')->get();
        return view('scraper_results_list', compact('results'));
    }

    public function userShow($id)
    {
        $result = ScrapedResult::findOrFail($id);
        Log::info('Showing results for text: ' . $result->data);
          $decoded = json_decode($result->data, true);
        $text = '';
        foreach ($decoded as $item) {
            $text .= ($item['caption'] ?? '') . "\n";
            if (!empty($item['comments']) && is_array($item['comments'])) {
            foreach ($item['comments'] as $comment) {
                $text .= ($comment ?? '') . "\n";
            }
            }
        }
        $text = trim($text);
        return view('scraper_results', compact('result', 'text'));
    }
}