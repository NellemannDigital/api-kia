<?php

namespace App\Console\Commands;

use App\Jobs\SyncUsedCarsJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;

class SyncUsedCars extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nellemann:sync-used-cars';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync used cars';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Syncing used cars');

        Bus::chain([
            new SyncUsedCarsJob()
        ])
            ->dispatch();
    }
}
