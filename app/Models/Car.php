<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\LaravelData\DataCollection;
use App\Data\AssetData;
use App\Data\Car\{
    DeliveryData,
    ModelData,
    VariantData,
    TechnicalSpecificationsData,
    DimensionsData,
    CampaignData,
    UrlsData,
    ChannelsData,
    PriceListData,
    InsuranceRateData,
    FileData,
    WarrantyData
};
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Scopes\OpenChannels;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class Car extends Model
{
    protected $appends = ['from_price', 'electric_range', 'consumption_range', 'co2_emission_range', 'ac_charging_time_range', 'dc_charging_time_range', 'ac_charging_speed_range', 'dc_charging_speed_range', 'owner_tax_range'];

    protected $fillable = [
        'struct_id',
        'web_id',
        'name',
        'year',
        'delivery',
        'custom_disclaimer',
        'campaign_disclaimer',
        'model',
        'variant',
        'primary_image',
        'technical_specifications',
        'dimensions',
        'campaign',
        'urls',
        'channels',
        'price_list',
        'files',
        'insurance_rates',
        'categories',
        'warranties',
        'synced_at'
    ];

    protected $casts = [
        'delivery' => DeliveryData::class,
        'model' => ModelData::class,
        'variant' => VariantData::class,
        'primary_image' => AssetData::class,
        'technical_specifications' => TechnicalSpecificationsData::class,
        'dimensions' => DimensionsData::class,
        'campaign' => CampaignData::class,
        'urls' => UrlsData::class,
        'channels' => ChannelsData::class,
        'price_list' => PriceListData::class,
        'files' => DataCollection::class . ':' . FileData::class,
        'insurance_rates' => DataCollection::class . ':' . InsuranceRateData::class,
        'categories' => 'array',
        'warranties' => DataCollection::class . ':' . WarrantyData::class,
    ];

    protected array $extraChannels = [];

    protected static function booted()
    {
        static::addGlobalScope(new OpenChannels());

        static::addGlobalScope('hasTrims', function ($builder) {
            $builder->has('trims');
        });

        static::saved(function() {
            Cache::forget('cars_all');
        });

        static::deleted(function() {
            Cache::forget('cars_all');
        });
    }

    public function scopeAvailableForTestDrive($query, $date = null)
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();

        /*return $query->whereNotNull('channels->test_drive_channel->test_start')
            ->where('channels->test_drive_channel->test_start', '<=', $date->toDateString());*/

        return $query
            ->whereNotNull('channels->test_drive_channel->booking_start')
            ->where('channels->test_drive_channel->booking_start', '<=', $date->toDateString())
            ->where(function ($query) use ($date) {
                $query->where('channels->test_drive_channel->booking_end', '>=', $date->toDateString())
                    ->orWhereNull('channels->test_drive_channel->booking_end');
            });
    }

    public function scopeOpenAt($query, CarbonInterface|string|null $date = null)
    {
        $date = $date instanceof CarbonInterface
            ? $date->toDateString()
            : ($date ? Carbon::parse($date)->toDateString() : Carbon::today()->toDateString());

        $channels = $this->getActiveChannels();

        return $query->where(function ($q) use ($channels, $date) {
            foreach ($channels as $channel) {
                $q->where(function ($subQuery) use ($channel, $date) {
                    $subQuery
                        ->where("{$channel}->open_from", '<=', $date)
                        ->where(function ($dateQuery) use ($channel, $date) {
                            $dateQuery
                                ->whereNull("{$channel}->open_to")
                                ->orWhere("{$channel}->open_to", '>=', $date);
                        });
                });
            }
        });
    }

    public function getActiveChannels(): array
    {
        return array_merge(['channels->master_channel'], $this->extraChannels);
    }

    public function scopeAddChannel($query, string $channel)
    {
        $this->extraChannels[] = "channels->{$channel}";
        return $query;
    }

    public function scopeAddChannels($query, array $channels)
    {
        foreach ($channels as $channel) {
            $this->extraChannels[] = "channels->{$channel}";
        }
        return $query;
    }

    public function getFromPriceAttribute()
    {
        if ($this->relationLoaded('trims')) {
            $prices = $this->trims->flatMap(function ($trim) {
                return $trim->powertrains->flatMap(function ($powertrain) {
                    return $powertrain->prices->pluck('suggested_retail_price');
                });
            });

            return $prices->min();
        }

        return null;
    }

    public function getElectricRangeAttribute()
    {
        if ($this->relationLoaded('trims')) {
            return $this->specRange('pure_electric_range');
        }

        return null;
    }

    public function getConsumptionRangeAttribute()
    {
        if ($this->relationLoaded('trims')) {
            return $this->specRange('consumption.number');
        }

        return null;
    }

    public function getCo2EmissionRangeAttribute()
    {
        if ($this->relationLoaded('trims')) {
            return $this->specRange('co2_emission');
        }

        return null;
    }

    public function getAcChargingTimeRangeAttribute()
    {
        if ($this->relationLoaded('trims')) {
            return $this->specRange('ac_charging_time');
        }

        return null;
    }

    public function getDcChargingTimeRangeAttribute()
    {
        if ($this->relationLoaded('trims')) {
            return $this->specRange('dc_charging_time');
        }

        return null;
    }

    public function getAcChargingSpeedRangeAttribute()
    {
        if ($this->relationLoaded('trims')) {
            return $this->specRange('ac_charging_speed');
        }

        return null;
    }

    public function getDcChargingSpeedRangeAttribute()
    {
        if ($this->relationLoaded('trims')) {
            return $this->specRange('dc_charging_speed');
        }

        return null;
    }

    public function getOwnerTaxRangeAttribute()
    {
        if ($this->relationLoaded('trims')) {
            return $this->specRange('owner_tax');
        }

        return null;
    }

    public function specRange(string $key): ?array
    {
        $values = $this->trims
            ->flatMap(fn ($trim) => $trim->powertrains)
            ->map(function ($powertrain) use ($key) {
                $configValue = data_get($powertrain, "configuration.technical_specifications.$key");

                return $configValue ?? data_get($powertrain->technical_specifications, $key);
            })
            ->filter(fn($v) => $v !== null)
            ->values();

        if ($values->isEmpty()) {
            return null;
        }

        return [
            'min' => $values->min(),
            'max' => $values->max(),
        ];
    }

    public function formattedSpecRange(string $key, int $decimals = 0, string $unit = ''): ?string
    {
        $range = $this->specRange($key);

        if (!$range) {
            return null;
        }

        $min = number_format($range['min'], $decimals, ',', '.');
        $max = number_format($range['max'], $decimals, ',', '.');

        if ($range['min'] == $range['max']) {
            return $min . ($unit ? ' ' . $unit : '');
        }

        return $min . ' - ' . $max . ($unit ? ' ' . $unit : '');
    }

    /**
     * @return HasMany<Trim, $this>
     */
    public function trims(): HasMany
    {
        return $this->hasMany(Trim::class);
    }


    /**
     * @return string|array
     */
    public static function index($filters = [])
    {
        $cars = Cache::remember('cars_all', 3600, fn() => Car::all()->sortBy('name'));

        if (!empty($filters['category'])) {
            $cars = $cars->filter(fn($car) =>
                collect($car->categories)->pluck('name')->contains($category)
            );
        }

        return $cars;
    }

    protected function isChannelOpen(string $channel): bool
    {
        $channelData = $this->channels[$channel] ?? null;

        if (!$channelData) {
            return false;
        }

        $now = now();

        $openFrom = isset($channelData['open_from']) ? Carbon::parse($channelData['open_from']) : null;
        $openTo   = isset($channelData['open_to']) ? Carbon::parse($channelData['open_to']) : null;

        return (! $openFrom || $now->gte($openFrom)) && (! $openTo || $now->lte($openTo));
    }

}
