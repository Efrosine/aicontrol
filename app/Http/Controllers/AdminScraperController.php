<?php

namespace App\Http\Controllers;

use App\Models\ScrapedData;
use App\Models\ScrapedResult;
use App\Models\ScrapedDataResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class AdminScraperController extends Controller
{
    public function showForm()
    {
        $dummyAccounts = \App\Models\DummyAccount::all(['id', 'username', 'platform']);
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
            'accounts' => 'required|string',
            'suspected_account' => 'required|string',
            'post_count' => 'required|integer|min:1',
            'comment_count' => 'required|integer|min:1',
        ]);

        // Save input parameters
        $scrapedData = ScrapedData::create([
            'input_query' => json_encode($validated),
        ]);

        // Make HTTP POST to ig-scraper
        $response = Http::timeout(60)->post('http://ig-scraper:5000/scrape', $validated);
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

        // return redirect()->route('admin.scraper.results', ['id' => $scrapedResult->id]);
    }

    public function showResults($id)
    {
        $result = ScrapedResult::findOrFail($id);
        return view('admin.scraper_results', ['result' => $result]);
    }

    public function index()
    {
        $results = \App\Models\ScrapedResult::orderByDesc('created_at')->get();
        return view('admin.scraper_results_list', compact('results'));
    }

    // User-facing: list all scraped results
    public function userIndex()
    {
        $results = \App\Models\ScrapedResult::orderByDesc('created_at')->get();
        return view('scraper_results_list', compact('results'));
    }

    // User-facing: show a single scraped result
    public function userShow($id)
    {
        $result = \App\Models\ScrapedResult::findOrFail($id);
        return view('scraper_results', compact('result'));
    }
}