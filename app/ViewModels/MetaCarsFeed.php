<?php

namespace App\ViewModels;

use App\Models\Car;
use App\Models\Powertrain;
use App\Models\Trim;
use Illuminate\Support\Collection;

class MetaCarsFeed
{
    public function __construct(
        protected Collection $cars,
        protected string $currency = 'DKK',
    ) {}

    public function build(): Collection
    {
        return $this->cars
            ->flatMap(fn (Car $car) => $car->trims
                ->flatMap(fn (Trim $trim) => $trim->powertrains
                    ->map(fn (Powertrain $powertrain) => $this->listing($car, $trim, $powertrain))))
            ->values();
    }

    protected function listing(Car $car, Trim $trim, Powertrain $powertrain): array
    {
        $make = $this->clean(data_get($car, 'model.brand')) ?? 'Kia';
        $model = $this->clean($car->name) ?? $this->clean(data_get($car, 'model.name'));
        $trimName = $this->clean($trim->name);
        $powertrainName = $this->clean(data_get($powertrain, 'engine.name'));
        $price = $this->price($powertrain);
        $title = $this->title($make, $model, $trimName, $powertrainName);
        $id = $this->listingId($powertrain);

        return [
            'id' => $id,
            'vehicle_id' => $id,
            'title' => $title,
            'description' => $this->description($car, $title),
            'url' => $this->url($car),
            'make' => $make,
            'model' => $model,
            'trim' => $trimName,
            'year' => $this->clean($car->year),
            'mileage' => [
                'value' => 0,
                'unit' => 'KM',
            ],
            'image_url' => $this->imageUrl($car, $trim),
            'transmission' => $this->clean(data_get($powertrain, 'transmission.name')),
            'body_style' => $this->bodyStyle($car),
            'drivetrain' => $this->clean(data_get($powertrain, 'engine.drive')),
            'vin' => null,
            'price' => $price ? number_format($price, 2, '.', '') : null,
            'currency' => $this->currency,
            'exterior_color' => $this->exteriorColor($trim),
            'interior_color' => $this->interiorColor($trim),
            'state_of_vehicle' => 'new',
            'fuel_type' => $this->clean(data_get($powertrain, 'engine.fuel_type')),
            'condition' => 'new',
            'availability' => 'in stock',
            'vehicle_type' => 'car',
            'brand' => $make,
            'link' => $this->url($car),
            'image_link' => $this->imageUrl($car, $trim),
        ];
    }

    protected function listingId(Powertrain $powertrain): string
    {
        return collect([
            data_get($powertrain, 'configuration.model_code'),
            data_get($powertrain, 'configuration.grade'),
            data_get($powertrain, 'configuration.ocn'),
        ])
            ->map(fn ($value) => $this->clean($value))
            ->filter()
            ->implode('-');
    }

    protected function title(string $make, ?string $model, ?string $trim, ?string $powertrain): string
    {
        return collect([$make, $model, $trim, $powertrain])
            ->filter(fn ($value) => filled($value))
            ->map(fn ($value) => $this->clean((string) $value))
            ->filter()
            ->implode(' ');
    }

    protected function description(Car $car, string $fallback): string
    {
        return $this->clean($car->custom_disclaimer)
            ?? $this->clean($car->campaign_disclaimer)
            ?? $fallback;
    }

    protected function url(Car $car): ?string
    {
        return collect([
            data_get($car, 'urls.website'),
            data_get($car, 'urls.build_configurator'),
            data_get($car, 'urls.leasing_configurator'),
            data_get($car, 'urls.technical_specifications'),
            data_get($car, 'urls.test_drive'),
        ])
            ->map(fn ($url) => $this->clean($url))
            ->first(fn ($url) => filled($url));
    }

    protected function imageUrl(Car $car, Trim $trim): ?string
    {
        return collect([
            data_get($trim, 'primary_image.url'),
            data_get($car, 'primary_image.url'),
            data_get($car, 'price_list.primary_image.url'),
        ])
            ->map(fn ($url) => $this->clean($url))
            ->first(fn ($url) => filled($url));
    }

    protected function price(Powertrain $powertrain): ?float
    {
        return $powertrain->prices
            ->map(fn ($price) => $price->campaign_retail_price ?: $price->suggested_retail_price)
            ->filter(fn ($price) => is_numeric($price) && (float) $price > 0)
            ->map(fn ($price) => (float) $price)
            ->min();
    }

    protected function bodyStyle(Car $car): ?string
    {
        return collect($car->categories ?? [])
            ->map(fn ($category) => data_get($category, 'name') ?? data_get($category, 'Name') ?? (is_string($category) ? $category : null))
            ->map(fn ($category) => $this->clean($category))
            ->filter()
            ->first();
    }

    protected function exteriorColor(Trim $trim): ?string
    {
        return $trim->colors
            ->map(fn ($color) => trim(collect([$color->primary_color, $color->secondary_color])->filter()->implode(' / ')))
            ->map(fn ($color) => $this->clean($color))
            ->filter()
            ->first();
    }

    protected function interiorColor(Trim $trim): ?string
    {
        return $this->clean(data_get($trim, 'interior.name'));
    }

    protected function clean(mixed $value): ?string
    {
        if (! filled($value)) {
            return null;
        }

        $value = trim(preg_replace('/\s+/', ' ', strip_tags((string) $value)));

        return $value !== '' ? $value : null;
    }
}
