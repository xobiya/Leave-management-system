<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Response;

class ExportService
{
    public function toCsv(Collection $data, array $headers, string $filename): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $callback = function () use ($data, $headers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);

            foreach ($data as $row) {
                $values = [];
                foreach ($headers as $header) {
                    $values[] = $row[$header] ?? '';
                }
                fputcsv($handle, $values);
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
