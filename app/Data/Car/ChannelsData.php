<?php

namespace App\Data\Car;

use Spatie\LaravelData\Data;
use App\Data\Car\Channels\{
    MasterChannelData,
    WebChannelData,
    DealerChannelData,
    PriceChannelData,
    TestDriveChannelData
};

class ChannelsData extends Data
{
    public function __construct(
        public ?MasterChannelData $master_channel = null,
        public ?WebChannelData $web_channel = null,
        public ?DealerChannelData $dealer_channel = null,
        public ?PriceChannelData $price_channel = null,
        public ?TestDriveChannelData $test_drive_channel = null,
    ) {}
}
