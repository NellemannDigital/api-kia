<?php
namespace App\Data\Car\Channels;

use Spatie\LaravelData\Data;

class WebChannelData extends Data
{
    public function __construct(
        public ?string $open_from = null,
        public ?string $open_to = null,
    ) {}
}
