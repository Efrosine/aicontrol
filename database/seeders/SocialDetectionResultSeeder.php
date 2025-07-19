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

                'output' => 'Teks mengandung konten yang mencurigakan dengan hashtag yang sering digunakan untuk aktivitas mencurigakan.'
            ]
        ]);
        SocialDetectionResult::create([
            'scraped_data_id' => 1,
            'data' => [

                'output' => 'Teks mengandung konten yang mencurigakan dengan hashtag yang sering digunakan untuk aktivitas mencurigakan.'
            ]
        ]);
        SocialDetectionResult::create([
            'scraped_data_id' => 1,
            'data' => [

                'output' => 'Teks mengandung konten yang mencurigakan dengan hashtag yang sering digunakan untuk aktivitas mencurigakan.'
            ]
        ]);

    }
}
