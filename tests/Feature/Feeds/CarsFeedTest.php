<?php

use App\Models\Car;
use App\Models\Color;
use App\Models\ColorPrice;
use App\Models\Configuration;
use App\Models\Powertrain;
use App\Models\Price;
use App\Models\Trim;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders cars as a Meta vehicle XML feed', function () {
    $openChannels = [
        'master_channel' => ['open_from' => now()->subDay()->toDateString(), 'open_to' => null],
        'web_channel' => ['open_from' => now()->subDay()->toDateString(), 'open_to' => null],
        'price_channel' => ['open_from' => now()->subDay()->toDateString(), 'open_to' => null],
    ];

    $car = Car::create([
        'struct_id' => 123,
        'web_id' => 'ev3',
        'name' => 'EV3',
        'year' => '2026',
        'model' => ['brand' => 'Kia', 'name' => 'EV3'],
        'variant' => ['name' => 'GT-Line', 'code' => 'gt-line', 'b2b' => false],
        'primary_image' => [
            'struct_id' => 'image-123',
            'name' => 'EV3',
            'url' => 'https://example.com/images/ev3.jpg',
            'file_type' => 'image/jpeg',
            'type' => 'image',
        ],
        'urls' => ['website' => 'https://example.com/cars/ev3'],
        'channels' => $openChannels,
        'categories' => [['name' => 'SUV']],
    ]);

    $createTrim = function (string $name, int $structId, int $sortOrder) use ($car) {
        $trim = Trim::create([
            'struct_id' => $structId,
            'car_id' => $car->id,
            'name' => $name,
            'sort_order' => $sortOrder,
            'interior' => ['name' => 'Black', 'code' => 'black'],
            'channels' => [
                'master_channel' => ['open_from' => now()->subDay()->toDateString(), 'open_to' => null],
            ],
        ]);

        $color = Color::create([
            'trim_id' => $trim->id,
            'code' => 'snow-white',
            'primary_color' => 'Snow White',
        ]);

        ColorPrice::create([
            'color_id' => $color->id,
            'suggested_retail_price' => 0,
        ]);

        return $trim;
    };

    $createPowertrain = function (
        Trim $trim,
        string $name,
        int $configurationId,
        int $price,
        string $modelCode,
        string $grade,
        string $ocn
    ) use ($car) {
        $powertrain = Powertrain::create([
            'trim_id' => $trim->id,
            'configuration_id' => $configurationId,
            'ocn' => (string) $configurationId,
            'engine' => [
                'name' => $name,
                'code' => strtolower(str_replace(' ', '-', $name)),
                'drive' => 'FWD',
                'horse_power' => 204,
                'fuel_type' => 'Electric',
                'amount_of_cylinders' => null,
                'amount_of_valves' => null,
                'volume' => null,
                'horsepower_rev_range' => null,
            ],
            'transmission' => [
                'name' => 'Automatic',
                'code' => 'AT',
                'charge_plug_type' => 'CCS',
                'number_of_gears' => 1,
            ],
        ]);

        Configuration::create([
            'struct_id' => $configurationId,
            'car_id' => $car->id,
            'trim_id' => $trim->id,
            'powertrain_id' => $powertrain->id,
            'model_code' => $modelCode,
            'grade' => $grade,
            'ocn' => $ocn,
        ]);

        Price::create([
            'powertrain_id' => $powertrain->id,
            'suggested_retail_price' => $price,
        ]);
    };

    $access = $createTrim('Access', 456, 1);
    $prestige = $createTrim('Prestige', 457, 2);

    $createPowertrain($access, 'Standard Range', 789, 299900, 'H8W5K5G1U', 'E', 'E1ZE');
    $createPowertrain($access, 'Long Range', 790, 329900, 'H8W5K5G1U', 'E', 'E2ZE');
    $createPowertrain($prestige, 'Standard Range', 791, 349900, 'H8W5K5G1U', 'P', 'E1ZE');
    $createPowertrain($prestige, 'Long Range', 792, 379900, 'H8W5K5G1U', 'P', 'E2ZE');

    $response = $this->get(route('feeds.cars'));

    $response->assertOk();
    expect($response->headers->get('Content-Type'))->toContain('application/xml');

    $xml = simplexml_load_string($response->getContent());

    $listings = [];

    foreach ($xml->listing as $listing) {
        $listings[] = $listing;
    }

    $titles = array_map(fn ($listing) => (string) $listing->title, $listings);
    $vehicleIds = array_map(fn ($listing) => (string) $listing->vehicle_id, $listings);
    $accessStandardRange = collect($listings)
        ->first(fn ($listing) => (string) $listing->title === 'Kia EV3 Access Standard Range');

    expect($listings)->toHaveCount(4)
        ->and($titles)->toContain('Kia EV3 Access Standard Range')
        ->and($titles)->toContain('Kia EV3 Access Long Range')
        ->and($titles)->toContain('Kia EV3 Prestige Standard Range')
        ->and($titles)->toContain('Kia EV3 Prestige Long Range')
        ->and($vehicleIds)->toContain('H8W5K5G1U-E-E1ZE')
        ->and($vehicleIds)->toContain('H8W5K5G1U-P-E2ZE')
        ->and((string) $accessStandardRange->id)->toBe('H8W5K5G1U-E-E1ZE')
        ->and((string) $accessStandardRange->vehicle_id)->toBe('H8W5K5G1U-E-E1ZE')
        ->and((string) $accessStandardRange->price)->toBe('299900.00')
        ->and((string) $accessStandardRange->currency)->toBe('DKK')
        ->and((string) $accessStandardRange->image->url)->toBe('https://example.com/images/ev3.jpg')
        ->and((string) $accessStandardRange->trim)->toBe('Access')
        ->and((string) $accessStandardRange->mileage->unit)->toBe('KM')
        ->and((string) $accessStandardRange->state_of_vehicle)->toBe('new');
});
