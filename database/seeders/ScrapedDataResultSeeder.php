<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScrapedDataResultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('scraped_data_results')->insert([
            [
                'scraped_result_id' => 1,
                'scraped_data_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'scraped_result_id' => 1,
                'scraped_data_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'scraped_result_id' => 2,
                'scraped_data_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'scraped_result_id' => 2,
                'scraped_data_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
