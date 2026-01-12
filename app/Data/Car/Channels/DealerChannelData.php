<?php
namespace App\Data\Car\Channels;

use Spatie\LaravelData\Data;

class DealerChannelData extends Data
{
    public function __construct(
        public ?string $open_from = null,
        public ?string $open_to = null,
        public bool $open_internal,
    ) {}
}
