<?php

namespace App\Console\Commands;

use App\Jobs\GeneratePriceListsJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;

class GeneratePriceLists extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nellemann:generate-price-lists';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate price lists';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Generating price lists');

        Bus::chain([
            new GeneratePriceListsJob()
        ])
            ->onQueue('price-lists')
            ->dispatch();
    }
}
