<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BroadcastRecipientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample broadcast recipients
        \App\Models\BroadcastRecipient::create([
            'name' => 'Security Team Lead',
            'phone_no' => '628111222333',
            'receive_cctv' => true,
            'receive_social' => true,
        ]);

        \App\Models\BroadcastRecipient::create([
            'name' => 'CCTV Monitoring Staff',
            'phone_no' => '628222333444',
            'receive_cctv' => true,
            'receive_social' => false,
        ]);

        \App\Models\BroadcastRecipient::create([
            'name' => 'Social Media Analyst',
            'phone_no' => '628333444555',
            'receive_cctv' => false,
            'receive_social' => true,
        ]);
    }
}
