<?php

namespace App\Mappers\Car;

use App\Data\Car\ChannelsData;
use App\Data\Car\Channels\{
    MasterChannelData,
    WebChannelData,
    DealerChannelData,
    PriceChannelData,
    TestDriveChannelData
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
            master_channel: self::mapMasterChannel($data),
            web_channel: self::mapWebChannel($data),
            dealer_channel: self::mapDealerChannel($data),
            price_channel: self::mapPriceChannel($data),
            test_drive_channel: self::mapTestDriveChannel($data),
        );
    }

    protected static function mapMasterChannel(array|Collection|null $attributes): ?MasterChannelData
    {
        if (!$attributes) return null;

        $openFrom = self::normalizeDate(Arr::get($attributes, 'KiaModelYear.ModelFromDate'));
        $openTo   = self::normalizeDate(Arr::get($attributes, 'KiaModelYear.ModelToDate'));

        if (!$openFrom && !$openTo) return null;

        return new MasterChannelData(
            open_from: $openFrom,
            open_to: $openTo
        );
    }

    protected static function mapWebChannel(array|Collection|null $attributes): ?WebChannelData
    {
        if (!$attributes) return null;

        $openFrom = self::normalizeDate(Arr::get($attributes, 'KiaChannelWeb.WebOpenFrom'));
        $openTo   = self::normalizeDate(Arr::get($attributes, 'KiaChannelWeb.WebOpenTo'));

        if (!$openFrom && !$openTo) return null;

        return new WebChannelData(
            open_from: $openFrom,
            open_to: $openTo
        );
    }

    protected static function mapDealerChannel(array|Collection|null $attributes): ?DealerChannelData
    {
        if (!$attributes) return null;

        $openFrom = self::normalizeDate(Arr::get($attributes, 'KiaChannelDealer.DealerOpenFrom'));
        $openTo   = self::normalizeDate(Arr::get($attributes, 'KiaChannelDealer.DealerOpenTo'));
        $openInternalRaw = Arr::get($attributes, 'KiaChannelDealer.OpenInternalOrder');
        $openInternal = $openInternalRaw ? true : false;

        if (!$openFrom && !$openTo && !$openInternal) return null;

        return new DealerChannelData(
            open_from: $openFrom,
            open_to: $openTo,
            open_internal: $openInternal
        );
    }

    protected static function mapPriceChannel(array|Collection|null $attributes): ?PriceChannelData
    {
        if (!$attributes) return null;

        $openFrom = self::normalizeDate(Arr::get($attributes, 'KiaChannelPrice.PriceOpenFrom'));
        $openTo   = self::normalizeDate(Arr::get($attributes, 'KiaChannelPrice.PriceOpenTo'));

        if (!$openFrom && !$openTo) return null;

        return new PriceChannelData(
            open_from: $openFrom,
            open_to: $openTo
        );
    }

    protected static function mapTestDriveChannel(array|Collection|null $attributes): ?TestDriveChannelData
    {
        if (!$attributes) return null;

        $bookingStart = self::normalizeDate(Arr::get($attributes, 'TestDrivesKia.BookingStart'));
        $testStart    = self::normalizeDate(Arr::get($attributes, 'TestDrivesKia.TestStart'));

        if (!$bookingStart && !$testStart) return null;

        return new TestDriveChannelData(
            booking_start: $bookingStart,
            test_start: $testStart
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
