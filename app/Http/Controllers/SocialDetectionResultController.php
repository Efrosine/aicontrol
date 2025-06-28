<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SocialDetectionResult;

class SocialDetectionResultController extends Controller
{
    public function index()
    {
        $results = SocialDetectionResult::orderByDesc('created_at')->get();
        return view('social_detection_results.index', compact('results'));
    }

    public function analyze($id, $text)
    {
        $response = \Http::withBody(json_encode(['text' => $text]), 'application/json')
            ->get('http://192.168.8.11:19999/webhook/analyze');

        $result = $response->json();

        $saved = SocialDetectionResult::create([
            'scraped_data_id' => $id,
            'data' => $result,
        ]);

        return redirect()->route('admin.social_detection_results.index');
    }
}
