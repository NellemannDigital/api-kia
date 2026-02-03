<?php

namespace App\Console\Commands;

use App\Jobs\SyncStockCarsJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;

class SyncStockCars extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nellemann:sync-stock-cars';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync stock cars';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Syncing stock cars');

        Bus::chain([
            new SyncStockCarsJob('Klar,Igangsat,Produktion,Klar til vognmand,Afhentet,Forhandler')
        ])
        ->onQueue('azure-sync')
        ->dispatch();
    }
}
