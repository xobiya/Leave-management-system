<?php

namespace App\Console\Commands;

use App\Services\AccrualService;
use Illuminate\Console\Command;

class ProcessAccruals extends Command
{
    protected $signature = 'erp:accruals-process';
    protected $description = 'Process all pending accrual calculations for leave allocations';

    public function handle(AccrualService $service): int
    {
        $this->info('Processing accruals...');
        $processed = $service->processAllAccruals();
        $this->info("Processed {$processed} accrual allocations.");

        return Command::SUCCESS;
    }
}
