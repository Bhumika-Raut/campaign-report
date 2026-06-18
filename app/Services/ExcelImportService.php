<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Campaign;
use App\Models\ClientSummary;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExcelImportService {
    public function import(string $filePath): void {
        $spreadsheet = IOFactory::load($filePath);

        DB::transaction(function () use ($spreadsheet) {
            // Sheet 1: client consumption
            $sheet1 = $spreadsheet->getSheetByName('client consumption');
            if ($sheet1) {
                $rows = $sheet1->toArray(null, true, false);
                $headers = array_shift($rows); // Remove header row
                foreach ($rows as $row) {
                    if (empty($row[0])) continue;
                    Client::updateOrCreate(
                        ['id' => intval($row[0])],
                        [
                            'email' => $row[1],
                            'company' => $row[2] === 'N/A' || empty($row[2]) ? null : $row[2],
                            'account_created' => $this->parseDate($row[3]),
                            'last_created_post' => $row[4] === 'N/A' || empty($row[4]) ? null : $this->parseDate($row[4]),
                            'plan' => $row[5],
                            'status' => $row[6],
                            'consumed' => intval($row[7]),
                            'remaining' => intval($row[8]),
                            'last_shared' => $row[9] === 'N/A' || empty($row[9]) ? null : $this->parseDate($row[9]),
                            'clicks' => intval($row[10]),
                            'posts' => intval($row[11]),
                            'comments' => intval($row[12]),
                        ]
                    );
                }
            }

            // Sheet 2: campaign report
            $sheet2 = $spreadsheet->getSheetByName('campaign report');
            if ($sheet2) {
                $rows = $sheet2->toArray(null, true, false);
                $headers = array_shift($rows);
                foreach ($rows as $row) {
                    if (empty($row[2])) continue;
                    Campaign::updateOrCreate(
                        ['id' => intval($row[2])], // Campaign ID
                        [
                            'client_name' => $row[0],
                            'campaign_name' => $row[1],
                            'date' => $this->parseDate($row[3]),
                            'potential_reach' => intval($row[4]),
                            'total_shares' => intval($row[5]),
                            'total_clicks' => intval($row[6]),
                            'total_comments' => intval($row[7]),
                            'total_likes' => intval($row[8]),
                            'total_posts' => intval($row[9]),
                            'ugc_enabled' => strtolower($row[10] ?? '') === 'yes',
                            'form_submissions' => intval($row[11]),
                        ]
                    );
                }
            }

            // Sheet 3: client summary
            $sheet3 = $spreadsheet->getSheetByName('client summary');
            if ($sheet3) {
                $rows = $sheet3->toArray(null, true, false);
                $headers = array_shift($rows);
                foreach ($rows as $row) {
                    if (empty($row[1])) continue;
                    ClientSummary::updateOrCreate(
                        ['email' => $row[1]],
                        [
                            'client_name' => $row[0],
                            'campaigns_count' => intval($row[2]),
                            'shares_count' => intval($row[3]),
                            'form_submissions_count' => intval($row[4]),
                            'credits_consumed' => intval($row[5]),
                        ]
                    );
                }
            }

            // Sheet 4: Removed Test Campaigns
            $sheet4 = $spreadsheet->getSheetByName('Removed Test Campaigns');
            if ($sheet4) {
                $rows = $sheet4->toArray(null, true, false);
                // Skip the first 6 rows (indexes 0 to 5) because row 6 (index 5) is the headers.
                // Row 7 (index 6) starts the actual data.
                for ($i = 0; $i < 6; $i++) {
                    array_shift($rows);
                }
                foreach ($rows as $row) {
                    if (empty($row[3])) continue; // Skip if campaign_id is empty
                    \App\Models\RemovedCampaign::updateOrCreate(
                        ['id' => intval($row[3])],
                        [
                            'client_name' => $row[0],
                            'company' => $row[1] === 'N/A' || empty($row[1]) ? null : $row[1],
                            'agency' => $row[2] === 'N/A' || empty($row[2]) ? null : $row[2],
                            'campaign_title' => $row[4],
                            'paid_or_not' => $row[5] === 'N/A' || empty($row[5]) ? null : $row[5],
                            'potential_reach' => intval($row[6]),
                            'total_shares' => intval($row[7]),
                            'reach_per_share' => floatval($row[8]),
                            'total_clicks' => intval($row[9]),
                            'clicks_per_share' => floatval($row[10]),
                            'total_comments' => intval($row[11]),
                            'comments_per_share' => floatval($row[12]),
                            'total_likes' => intval($row[13]),
                            'likes_per_share' => floatval($row[14]),
                            'total_posts' => intval($row[15]),
                            'reshare' => intval($row[16]),
                            'registrations' => intval($row[17]),
                            'emv' => floatval($row[18]),
                            'direct_savings' => floatval($row[19]),
                            'total_return' => floatval($row[20]),
                            'roi' => floatval($row[21]),
                            'registration_per_share' => floatval($row[22]),
                        ]
                    );
                }
            }
        });
    }

    private function parseDate($value): ?string {
        if (empty($value) || $value === 'N/A') return null;
        try {
            // Handle numeric Excel timestamps vs raw text dates
            if (is_numeric($value)) {
                return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value))->toDateString();
            }
            return Carbon::parse(str_replace('-', '/', $value))->toDateString();
        } catch (\Exception $e) {
            return null;
        }
    }
}
