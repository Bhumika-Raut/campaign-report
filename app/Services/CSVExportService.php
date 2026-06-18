<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\StreamedResponse;

class CSVExportService {
    public function export(string $filename, array $headers, $queryBuilder, callable $rowCallback): StreamedResponse {
        $response = new StreamedResponse(function () use ($queryBuilder, $headers, $rowCallback) {
            $handle = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($handle, $headers);

            $queryBuilder->chunk(500, function ($rows) use ($handle, $rowCallback) {
                foreach ($rows as $row) {
                    fputcsv($handle, $rowCallback($row));
                }
            });

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);

        return $response;
    }
}
