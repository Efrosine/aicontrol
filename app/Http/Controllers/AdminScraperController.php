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

class AdminScraperController extends Controller
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
            return back()->withErrors(['accounts' => 'Selected account not found.']);
        }

        // Prepare payload with accounts as array containing one object
        $payload = [
            'platform' => $validated['platform'],
            'accounts' => [
                [
                    'username' => $account->username,
                    'password' => $account->password,
                ]
            ],
            'suspected_account' => $validated['suspected_account'],
            'post_count' => (int) $validated['post_count'],
            'comment_count' => (int) $validated['comment_count'],
        ];

        // Log the payload for debugging
        Log::info('Payload to ig-scraper: ' . json_encode($payload));

        // Save input parameters
        $scrapedData = ScrapedData::create([
            'input_query' => json_encode($payload),
        ]);

        dd($payload);

        // Make HTTP POST to ig-scraper with explicit JSON header
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->timeout(60)->post('http://ig-scraper:5000/scrape', $payload);

        // Check if response is successful
        if ($response->failed()) {
            Log::error('ig-scraper request failed: ' . $response->body());
            return back()->withErrors(['scraper' => 'Failed to fetch data from scraper.']);
        }

        $results = $response->json('results');

        // Save results
        $scrapedResult = ScrapedResult::create([
            'account' => $validated['suspected_account'],
            'data' => json_encode($results),
            'url' => $results['url'] ?? '',
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
        return view('admin.scraper_results', ['result' => $result]);
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
        return view('scraper_results', compact('result'));
    }
}