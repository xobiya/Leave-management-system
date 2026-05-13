<?php

namespace Tests\Unit;

use App\Services\ExportService;
use Illuminate\Support\Collection;
use Tests\TestCase;

class ExportServiceTest extends TestCase
{
    public function test_to_csv_generates_streamed_response(): void
    {
        $service = app(ExportService::class);

        $data = new Collection([
            ['name' => 'John', 'email' => 'john@test.com', 'department' => 'Engineering'],
            ['name' => 'Jane', 'email' => 'jane@test.com', 'department' => 'Marketing'],
        ]);

        $headers = ['name', 'email', 'department'];
        $response = $service->toCsv($data, $headers, 'test-export.csv');

        $this->assertEquals(200, $response->getStatusCode());

        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        $this->assertStringContainsString('John', $content);
        $this->assertStringContainsString('jane@test.com', $content);
    }
}
