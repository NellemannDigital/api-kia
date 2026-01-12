<?php

namespace App\Mappers\Trim;

use App\Data\Trim\ChannelsData;
use App\Data\Trim\Channels\{
    MasterChannelData
};
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use Carbon\Carbon;

class ChannelsMapper
{
    public static function map(array|Collection|null $attributes): ChannelsData
    {
        $data = $attributes instanceof Collection ? $attributes : collect($attributes);

        return new ChannelsData(
            master_channel: self::mapMasterChannel($data)
        );
    }

    protected static function mapMasterChannel(array|Collection|null $attributes): ?MasterChannelData
    {
        if (!$attributes) return null;

        $openFrom = self::normalizeDate(Arr::get($attributes, 'KiaChannelAll.AllChannelsFrom'));
        $openTo   = self::normalizeDate(Arr::get($attributes, 'KiaChannelAll.AllChannelsTo'));

        if (!$openFrom && !$openTo) return null;

        return new MasterChannelData(
            open_from: $openFrom,
            open_to: $openTo
        );
    }

    /**
     * Normalize a date string to Y-m-d format or return null.
     */
    protected static function normalizeDate(?string $date): ?string
    {
        if (!$date) return null;

        try {
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
