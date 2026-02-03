<?php

namespace App\Console\Commands;

use App\Jobs\SyncDealersJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;

class SyncDealers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nellemann:sync-dealers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync dealers';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Syncing dealers');

        Bus::chain([
            new SyncDealersJob()
        ])
            ->onQueue('dynamics')
            ->dispatch();
    }
}
