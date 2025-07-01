<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScrapedResultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('scraped_results')->insert([
            [
                'account' => 'user1',
                'data' => json_encode(['title' => 'Example Title 1', 'content' => 'Sample scraped content 1']),
                'url' => 'https://example.com/page1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'account' => 'user2',
                'data' => json_encode(['title' => 'Example Title 2', 'content' => 'Sample scraped content 2']),
                'url' => 'https://example.com/page2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'account' => 'user3',
                'data' => json_encode(['title' => 'Example Title 3', 'content' => 'Sample scraped content 3']),
                'url' => 'https://example.com/page3',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
