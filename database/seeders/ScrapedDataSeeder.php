<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScrapedDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('scraped_data')->insert([
            [
                'input_query' => 'example query 1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'input_query' => 'example query 2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'input_query' => 'example query 3',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
