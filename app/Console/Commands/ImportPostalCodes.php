<?php

namespace App\Console\Commands;

use App\Models\PostalCode;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImportPostalCodes extends Command
{
    protected $signature = 'postal-codes:import';

    protected $description = 'Import Danish postal codes from Dataforsyningen';

    public function handle(): int
    {
        $this->info('Fetching postal codes...');

        $response = Http::timeout(60)
            ->get('https://api.dataforsyningen.dk/postnumre');

        if (! $response->successful()) {
            $this->error('Failed to fetch postal codes.');

            return self::FAILURE;
        }

        $postalCodes = $response->json();

        $bar = $this->output->createProgressBar(count($postalCodes));

        $bar->start();

        foreach ($postalCodes as $item) {

            PostalCode::updateOrCreate(
                [
                    'postal_code' => $item['nr'],
                ],
                [
                    'city' => $item['navn'],
                ]
            );

            $bar->advance();
        }

        $bar->finish();

        $this->newLine(2);

        $this->info('Postal codes imported successfully.');

        return self::SUCCESS;
    }
}