<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\LaravelData\DataCollection;
use App\Data\AssetData;
use App\Data\Car\{
    ModelData,
    VariantData,
    TechnicalSpecificationsData,
    DimensionsData,
    CampaignData,
    UrlsData,
    ChannelsData,
    PriceListData,
    InsuranceRateData,
    FileData
};
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Scopes\OpenChannels;

class Car extends Model
{
    protected $fillable = [
        'struct_id',
        'web_id',
        'name',
        'year',
        'delivery_year',
        'disclaimer',
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
    ];

    protected $casts = [
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
        'categories' => 'array'
    ];

  protected static function booted()
    {
        static::addGlobalScope(new OpenChannels(['channels->master_channel']));

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

    /**
     * @return HasMany<Trim, $this>
     */
    public function trims(): HasMany
    {
        return $this->hasMany(Trim::class);
    }

    public function specRange(string $key): ?array
    {
        $values = $this->trims
            ->flatMap(fn ($trim) => $trim->powertrains)
            ->map(function ($powertrain) use ($key) {
                $configValue = data_get($powertrain, "configuration.technical_specifications.$key");

                return $configValue ?? data_get($powertrain->technical_specifications, $key);
            })
            ->filter()
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

}
