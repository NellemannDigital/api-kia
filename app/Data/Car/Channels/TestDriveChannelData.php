<?php
namespace App\Data\Car\Channels;

use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Data;

class TestDriveChannelData extends Data
{
    public function __construct(
        public ?string $booking_start = null,
        public ?string $booking_end = null,
        public ?string $test_start = null,
    ) {}
}
