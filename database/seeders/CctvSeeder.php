<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CctvSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Cctv::create([
            'name' => 'Cam1',
            'location' => 'Kiri Bus',
            'origin_url' => 'http://192.168.8.109:8000/origin/1002',
            'stream_url' => 'http://192.168.8.109:8000/stream/1002',
        ]);

        \App\Models\Cctv::create([
            'name' => 'Cam2',
            'location' => 'Kanan Bus',
            'origin_url' => 'http://192.168.8.109:8000/origin/1002',
            'stream_url' => 'http://192.168.8.109:8000/stream/1002',
        ]);
    }
}
