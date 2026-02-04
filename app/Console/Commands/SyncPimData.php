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
     * --jobs=* : cars, configurations, accessories
     *
     * @var string
     */
    protected $signature = 'nellemann:sync-pim-data {--jobs=* : Which jobs to run (cars, configurations, accessories)}';

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

        $jobsToRun = $this->option('jobs'); // fx ['cars','configurations']

        if (empty($jobsToRun)) {
            $jobsToRun = ['cars','configurations','accessories'];
        }

        $chain = [];

        if (in_array('cars', $jobsToRun)) {
            $chain[] = new SyncCarsJob();
        }

        if (in_array('configurations', $jobsToRun)) {
            $chain[] = new SyncConfigurationsJob();
        }

        if (in_array('accessories', $jobsToRun)) {
            $chain[] = new SyncAccessoriesJob();
        }

        if (empty($chain)) {
            $this->info('No valid jobs selected. Exiting.');
            return;
        }

        Bus::chain($chain)
            ->onQueue('pim')
            ->dispatch();

        $this->info('Jobs dispatched: ' . implode(', ', $jobsToRun));
    }
}
