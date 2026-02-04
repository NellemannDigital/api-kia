<?php

namespace App\Console\Commands;

use App\Jobs\SyncCarsJob;
use App\Jobs\SyncConfigurationsJob;
use App\Jobs\SyncAccessoriesJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class SyncPimData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nellemann:sync-pim-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync data from PIM';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Syncing data from PIM');

        Bus::chain([
            new SyncCarsJob(),
            new SyncConfigurationsJob(),
            //new SyncAccessoriesJob(),
        ])
        ->onQueue('pim')
        ->dispatch();

    }
}
