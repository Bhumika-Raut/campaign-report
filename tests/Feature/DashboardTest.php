<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Client;
use App\Models\Campaign;
use App\Models\ClientSummary;
use App\Models\RemovedCampaign;

class DashboardTest extends TestCase {
    use RefreshDatabase;

    protected function setUp(): void {
        parent::setUp();
        
        // Populate Database with key test rows matching default range
        Client::create([
            'id' => 1933,
            'email' => 'aaak1837@gmail.com',
            'company' => null,
            'account_created' => '2026-06-08',
            'last_created_post' => null,
            'plan' => 'Premium', // premium to match clientConsumption default filter
            'status' => 'Not Live',
            'consumed' => 0,
            'remaining' => 100,
            'last_shared' => null,
            'clicks' => 0,
            'posts' => 0,
            'comments' => 0,
        ]);

        Campaign::create([
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
        ]);

        RemovedCampaign::create([
            'id' => 1003,
            'client_name' => 'Wozku',
            'campaign_title' => 'bucket test',
            'company' => 'Wozku Corp',
            'agency' => 'Creative Agency',
            'paid_or_not' => 'Paid',
            'potential_reach' => 6,
            'total_shares' => 7,
            'reach_per_share' => 0.85,
            'total_clicks' => 0,
            'clicks_per_share' => 0.0,
            'total_comments' => 0,
            'comments_per_share' => 0.0,
            'total_likes' => 0,
            'likes_per_share' => 0.0,
            'total_posts' => 4,
            'reshare' => 0,
            'registrations' => 0,
            'emv' => 0.0,
            'direct_savings' => 0.0,
            'total_return' => 0.0,
            'roi' => 0.0,
            'registration_per_share' => 0.0
        ]);

        ClientSummary::create([
            'client_name' => 'Wozku',
            'email' => 'aditya.shukla@wozku.com',
            'campaigns_count' => 3,
            'shares_count' => 48,
            'form_submissions_count' => 1,
            'credits_consumed' => 0,
        ]);
    }

    public function test_sync_page_loads_with_correct_options(): void {
        $response = $this->get('/sync');

        $response->assertStatus(200);
        $response->assertSee('Google Sheets Live Sync');
        $response->assertSee('Upload Excel Workbook');
    }

    public function test_client_consumption_page_filters(): void {
        $response = $this->get('/client-consumption?status=Not+Live');

        $response->assertStatus(200);
        $response->assertSee('aaak1837@gmail.com');
    }

    public function test_campaign_report_page_loads(): void {
        $response = $this->get('/campaign-report');

        $response->assertStatus(200);
        $response->assertSee('bucket test');
    }

    public function test_csv_export_endpoints(): void {
        $response1 = $this->get('/export/consumption');
        $response1->assertStatus(200);
        $response1->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $response2 = $this->get('/export/campaigns');
        $response2->assertStatus(200);
        $response2->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_google_sheet_sync_endpoint(): void {
        \Illuminate\Support\Facades\Http::fake([
            'docs.google.com/*' => \Illuminate\Support\Facades\Http::response(
                file_get_contents(base_path('Copy of Wozku All Campaigns Data File for GPT.xlsx')),
                200
            ),
        ]);

        $response = $this->post('/sync-google-sheet', [
            'sheet_url' => 'https://docs.google.com/spreadsheets/d/1zGND31Why2-EqHTZHg5xuOOFdlS9HpO_lpuKfGzuEMI/edit?usp=sharing'
        ]);

        $response->assertRedirect('/sync');
        $response->assertSessionHas('success', 'Google Sheet successfully synchronized with database!');

        $this->assertDatabaseHas('removed_campaigns', [
            'id' => 343,
            'client_name' => 'Abhishek Ahuja',
        ]);
    }
}
