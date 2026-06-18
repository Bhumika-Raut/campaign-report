<?php
include 'vendor/autoload.php';
$s = PhpOffice\PhpSpreadsheet\IOFactory::load('Copy of Wozku All Campaigns Data File for GPT.xlsx');
$sheet = $s->getSheetByName('Removed Test Campaigns');
if ($sheet) {
    $rows = $sheet->toArray(null, true, false);
    // Skip first 6 rows
    for ($i = 0; $i < 6; $i++) {
        array_shift($rows);
    }
    
    $totalCampaigns = 0;
    $sumReach = 0;
    $sumShares = 0;
    $sumClicks = 0;
    $sumLikes = 0;
    $sumForms = 0;
    
    foreach ($rows as $row) {
        if (!empty($row[3])) { // campaign_id
            $totalCampaigns++;
            $sumReach += intval($row[6]);
            $sumShares += intval($row[7]);
            $sumClicks += intval($row[9]);
            $sumLikes += intval($row[13]);
            $sumForms += intval($row[17]);
        }
    }
    echo "Excel Sheet Sums:\n";
    echo " - Total Campaigns: $totalCampaigns\n";
    echo " - Reach: $sumReach\n";
    echo " - Shares: $sumShares\n";
    echo " - Clicks: $sumClicks\n";
    echo " - Likes: $sumLikes\n";
    echo " - Forms (Registrations): $sumForms\n";
}
