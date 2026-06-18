<?php
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\RemovedCampaign;

echo "Importing...\n";
(new App\Services\ExcelImportService)->import('Copy of Wozku All Campaigns Data File for GPT.xlsx');
echo "Import complete.\n";

echo "Total rows: " . RemovedCampaign::count() . "\n";
echo "Distinct clients: " . RemovedCampaign::distinct('client_name')->count('client_name') . "\n";
echo "Sum potential reach: " . RemovedCampaign::sum('potential_reach') . "\n";
echo "Sum total shares: " . RemovedCampaign::sum('total_shares') . "\n";
echo "Sum registrations (Form Submissions): " . RemovedCampaign::sum('registrations') . "\n";
echo "Sum total clicks: " . RemovedCampaign::sum('total_clicks') . "\n";
echo "Sum total likes: " . RemovedCampaign::sum('total_likes') . "\n";
echo "Sum total comments: " . RemovedCampaign::sum('total_comments') . "\n";
echo "Sum direct savings: " . RemovedCampaign::sum('direct_savings') . "\n";
echo "Sum total return: " . RemovedCampaign::sum('total_return') . "\n";
echo "Sum emv: " . RemovedCampaign::sum('emv') . "\n";
echo "Sum roi: " . RemovedCampaign::sum('roi') . "\n";
