<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Services\ExcelImportService;
use App\Models\Client;
use App\Models\Campaign;
use App\Models\ClientSummary;

class ExcelImportTest extends TestCase {
    use RefreshDatabase;

    public function test_excel_import_service_populates_tables(): void {
        $filePath = base_path('Copy of Wozku All Campaigns Data File for GPT.xlsx');
        
        if (!file_exists($filePath)) {
            $this->markTestSkipped('Excel sample file not found at the expected path.');
            return;
        }

        $service = new ExcelImportService();
        $service->import($filePath);

        // Verify Removed Test Campaigns sheet details imported correctly
        $this->assertDatabaseHas('removed_campaigns', [
            'id' => 343,
            'client_name' => 'Abhishek Ahuja',
            'campaign_title' => 'Social Champ Contest',
            'potential_reach' => 90247,
            'total_shares' => 81,
            'total_clicks' => 150,
            'total_comments' => 6,
            'total_likes' => 623,
            'total_posts' => 8,
        ]);

        $this->assertDatabaseHas('removed_campaigns', [
            'id' => 231,
            'client_name' => 'Abhishek Sharma',
            'campaign_title' => 'MongoDB Local BLR',
            'potential_reach' => 916839,
            'total_shares' => 824,
            'total_clicks' => 4225,
            'total_comments' => 263,
            'total_likes' => 18752,
            'total_posts' => 5,
        ]);
    }
}
