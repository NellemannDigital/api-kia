<?php

namespace App\Console\Commands;

use App\Jobs\GeneratePdfsJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;

class GeneratePdfs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nellemann:generate-pdfs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate PDFs';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Generating PDFs');

        Bus::chain([
            new GeneratePdfsJob()
        ])
            ->onQueue('pdfs')
            ->dispatch();
    }
}
