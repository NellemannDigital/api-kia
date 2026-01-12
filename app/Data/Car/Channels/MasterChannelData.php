<?php
namespace App\Data\Car\Channels;

use Spatie\LaravelData\Data;

class MasterChannelData extends Data
{
    public function __construct(
        public ?string $open_from = null,
        public ?string $open_to = null,
    ) {}
}
