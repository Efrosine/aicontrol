<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SenderNumberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample sender numbers
        \App\Models\SenderNumber::create([
            'name' => 'AIControl Primary',
            'api_key' => 'sample_api_key_123456',
            'number_key' => '628123456789',
        ]);

        \App\Models\SenderNumber::create([
            'name' => 'AIControl Secondary',
            'api_key' => 'sample_api_key_789012',
            'number_key' => '628987654321',
        ]);
    }
}
