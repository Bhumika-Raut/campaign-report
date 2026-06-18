<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        // Clear previous data
        DB::table('clients')->truncate();
        DB::table('campaigns')->truncate();
        DB::table('client_summaries')->truncate();

        // 1. Seed Excel 'client consumption' sample data
        DB::table('clients')->insert([
            [
                'id' => 1933,
                'email' => 'aaak1837@gmail.com',
                'company' => null,
                'account_created' => '2026-06-08',
                'last_created_post' => null,
                'plan' => 'Free',
                'status' => 'Not Live',
                'consumed' => 0,
                'remaining' => 100,
                'last_shared' => null,
                'clicks' => 0,
                'posts' => 0,
                'comments' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 1932,
                'email' => 'aditya.k@wozku.com',
                'company' => null,
                'account_created' => '2026-06-02',
                'last_created_post' => null,
                'plan' => 'Premium',
                'status' => 'Live',
                'consumed' => 0,
                'remaining' => 1000,
                'last_shared' => null,
                'clicks' => 0,
                'posts' => 0,
                'comments' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // 2. Generate 208 additional clients to match "210 Clients" card
        $companies = ['Wozku', 'Kalvium', 'Astra', 'Flower', 'Docker_prac', null];
        $plans = ['Free', 'Premium'];
        $statuses = ['Live', 'Not Live'];

        // Add specific screenshot row in background
        DB::table('clients')->insert([
            'id' => 1930,
            'email' => 'srikeerthi.k@kalvium.community',
            'company' => 'Wozku',
            'account_created' => '2026-06-02',
            'last_created_post' => '2026-06-03',
            'plan' => 'Premium',
            'status' => 'Live',
            'consumed' => 0,
            'remaining' => 500,
            'last_shared' => '2026-06-03',
            'clicks' => 10,
            'posts' => 5,
            'comments' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('clients')->insert([
            'id' => 1931,
            'email' => 'bhumikaraut59620@gmail.com',
            'company' => null,
            'account_created' => '2026-06-02',
            'last_created_post' => null,
            'plan' => 'Free',
            'status' => 'Not Live',
            'consumed' => 0,
            'remaining' => 100,
            'last_shared' => null,
            'clicks' => 0,
            'posts' => 0,
            'comments' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        for ($i = 1; $i <= 206; $i++) {
            $clientId = 1930 - $i;
            DB::table('clients')->insert([
                'id' => $clientId,
                'email' => "client_{$clientId}@example.com",
                'company' => $companies[array_rand($companies)],
                'account_created' => Carbon::parse('2026-06-08')->subDays(rand(0, 30))->toDateString(),
                'last_created_post' => rand(0, 1) ? Carbon::parse('2026-06-08')->subDays(rand(0, 10))->toDateString() : null,
                'plan' => $plans[array_rand($plans)],
                'status' => $statuses[array_rand($statuses)],
                'consumed' => 0,
                'remaining' => rand(50, 1000),
                'last_shared' => rand(0, 1) ? Carbon::parse('2026-06-08')->subDays(rand(0, 10))->toDateString() : null,
                'clicks' => rand(0, 100),
                'posts' => rand(0, 20),
                'comments' => rand(0, 50),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 3. Seed Excel 'campaign report' sample data
        DB::table('campaigns')->insert([
            [
                'id' => 1003,
                'client_name' => 'Wozku',
                'campaign_name' => 'bucket test',
                'date' => '2026-06-09',
                'potential_reach' => 6,
                'total_shares' => 7,
                'total_clicks' => 0,
                'total_comments' => 0,
                'total_likes' => 0,
                'total_posts' => 4,
                'ugc_enabled' => false,
                'form_submissions' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 1004,
                'client_name' => 'Wozku',
                'campaign_name' => 'final test bucket',
                'date' => '2026-06-09',
                'potential_reach' => 6,
                'total_shares' => 11,
                'total_clicks' => 0,
                'total_comments' => 0,
                'total_likes' => 0,
                'total_posts' => 8,
                'ugc_enabled' => false,
                'form_submissions' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // 4. Generate 5 additional campaigns to match "7 Total Campaigns" card
        // We want: Date: 2026-06-08 to 2026-06-09
        // Shares sum: 7 + 11 (from excel) + 36 (faked) = 54
        // Form submissions sum: 0 (from excel) + 1 (faked) = 1
        $fakeCampaigns = [
            [
                'id' => 1005,
                'client_name' => 'Wozku',
                'campaign_name' => 'Summer Booster Campaign',
                'date' => '2026-06-08',
                'potential_reach' => 150,
                'total_shares' => 20,
                'total_clicks' => 45,
                'total_comments' => 12,
                'total_likes' => 85,
                'total_posts' => 15,
                'ugc_enabled' => true,
                'form_submissions' => 1,
            ],
            [
                'id' => 1006,
                'client_name' => 'Astra',
                'campaign_name' => 'Product Launch Alpha',
                'date' => '2026-06-08',
                'potential_reach' => 80,
                'total_shares' => 10,
                'total_clicks' => 15,
                'total_comments' => 4,
                'total_likes' => 30,
                'total_posts' => 5,
                'ugc_enabled' => false,
                'form_submissions' => 0,
            ],
            [
                'id' => 1007,
                'client_name' => 'Jyoti GV',
                'campaign_name' => 'Client Feedback Drive',
                'date' => '2026-06-09',
                'potential_reach' => 12,
                'total_shares' => 4,
                'total_clicks' => 2,
                'total_comments' => 1,
                'total_likes' => 8,
                'total_posts' => 2,
                'ugc_enabled' => false,
                'form_submissions' => 0,
            ],
            [
                'id' => 1008,
                'client_name' => 'Kalvium',
                'campaign_name' => 'Community Engagement',
                'date' => '2026-06-09',
                'potential_reach' => 5,
                'total_shares' => 1,
                'total_clicks' => 0,
                'total_comments' => 0,
                'total_likes' => 3,
                'total_posts' => 1,
                'ugc_enabled' => false,
                'form_submissions' => 0,
            ],
            [
                'id' => 1009,
                'client_name' => 'Flower',
                'campaign_name' => 'Organic Growth Pilot',
                'date' => '2026-06-09',
                'potential_reach' => 10,
                'total_shares' => 1,
                'total_clicks' => 1,
                'total_comments' => 0,
                'total_likes' => 2,
                'total_posts' => 1,
                'ugc_enabled' => true,
                'form_submissions' => 0,
            ]
        ];

        foreach ($fakeCampaigns as $camp) {
            DB::table('campaigns')->insert(array_merge($camp, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // 5. Seed Excel 'client summary' sample data
        DB::table('client_summaries')->insert([
            [
                'client_name' => 'Wozku',
                'email' => 'aditya.shukla@wozku.com',
                'campaigns_count' => 3,
                'shares_count' => 48,
                'form_submissions_count' => 1,
                'credits_consumed' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'client_name' => 'wozku',
                'email' => 'selvam.p@wozku.com',
                'campaigns_count' => 0,
                'shares_count' => 4,
                'form_submissions_count' => 0,
                'credits_consumed' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'client_name' => 'Jyoti GV',
                'email' => 'jyoti.gv@wozku.com',
                'campaigns_count' => 1,
                'shares_count' => 1,
                'form_submissions_count' => 0,
                'credits_consumed' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
