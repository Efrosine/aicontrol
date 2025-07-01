<?php

namespace Database\Seeders;

use App\Models\ScrapedData;
use App\Models\SocialDetectionResult;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Log;

class SocialDetectionResultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SocialDetectionResult::create([
            'scraped_data_id' => 1,
            'data' => [
                'platform' => 'Instagram',
                'account_name' => '@sample_user1',
                'content' => 'This is a sample post with suspicious content #tag1 #tag2',
                'post_url' => 'https://www.instagram.com/p/sample1/',
                'post_date' => now()->subDays(1)->toDateTimeString(),
                'detection_type' => 'Suspicious Content',
                'confidence_score' => 0.87
            ]
        ]);

        SocialDetectionResult::create([
            'scraped_data_id' => 1,
            'data' => [
                'platform' => 'Twitter',
                'account_name' => '@sample_user2',
                'content' => 'Another example of detected content that requires attention',
                'post_url' => 'https://twitter.com/sample_user2/status/123456789',
                'post_date' => now()->subDays(2)->toDateTimeString(),
                'detection_type' => 'Flagged Keywords',
                'confidence_score' => 0.92
            ]
        ]);

        SocialDetectionResult::create([
            'scraped_data_id' => 1,
            'data' => [
                'platform' => 'Facebook',
                'account_name' => 'Sample User',
                'content' => 'This is a longer post with multiple sentences. It contains some content that was flagged by the AI detection system for review.',
                'post_url' => 'https://www.facebook.com/posts/123456789',
                'post_date' => now()->subDays(3)->toDateTimeString(),
                'detection_type' => 'Suspicious Activity',
                'confidence_score' => 0.78
            ]
        ]);
    }
}
