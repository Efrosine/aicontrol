<?php

namespace Database\Seeders;

use App\Models\SuspectedAccount;
use Illuminate\Database\Seeder;

class SuspectedAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some sample suspected accounts
        $accounts = [
            ['data' => 'suspicious_user123', 'platform' => 'ig'],
            ['data' => 'hacker@example.com', 'platform' => 'twitter'],
            ['data' => 'spam_account', 'platform' => 'ig'],
            ['data' => 'phishing_link.co', 'platform' => 'x'],
            ['data' => '+1234567890', 'platform' => 'twitter'],
            ['data' => 'fake_profile', 'platform' => 'ig'],
            ['data' => 'scammer123', 'platform' => 'x'],
        ];

        foreach ($accounts as $account) {
            SuspectedAccount::create($account);
        }
    }
}
