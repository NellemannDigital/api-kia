<?php

namespace App\Data\Trim;

use Spatie\LaravelData\Data;
use App\Data\Trim\Channels\{
    MasterChannelData
};

class ChannelsData extends Data
{
    public function __construct(
        public ?MasterChannelData $master_channel = null,
    ) {}
}
