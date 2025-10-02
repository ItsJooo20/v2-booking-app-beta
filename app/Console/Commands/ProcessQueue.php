<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ProcessQueue extends Command
{
    protected $signature = 'queue:process';
    protected $description = 'Process queued jobs (for shared hosting)';

    public function handle()
    {
        // Process jobs for 50 seconds (safe for cron)
        Artisan::call('queue:work', [
            '--stop-when-empty' => true,
            '--max-jobs' => 1, // Add this parameter
            '--tries' => 3,
            '--timeout' => 60,
        ]);

        $this->info('Queue processed successfully');
    }
}