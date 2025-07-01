<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CctvSeeder::class,
            CctvDetectionResultSeeder::class,
            DummyAccountSeeder::class,
            ScrapedDataSeeder::class,
            ScrapedResultSeeder::class,
            ScrapedDataResultSeeder::class,
            SocialDetectionResultSeeder::class,
            SuspectedAccountSeeder::class,
            SenderNumberSeeder::class,
            BroadcastRecipientSeeder::class,
            // Add more seeders as needed
        ]);
    }
}
