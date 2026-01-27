<?php

namespace App\Data\Trim;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use App\Data\AssetData;
use App\Data\Casts\EscapeQuotes;
use App\Data\EquipmentData;
use App\Data\Trim\ExtraEquipmentPackage\{
    RequiresData,
    ExcludesData,
    ExcludesStandardEquipmentData,
    InteriorOverrideToData,
    ModelChangeCodeData,
    EngineRequiredData,
    TransmissionRequiredData,
    ColorRequiredData,
    PriceData
};


class ExtraEquipmentPackageData extends Data
{
    public function __construct(
        public string $code,
        public string $name,
        public ?string $category,
        public ?AssetData $image = null,
        public ?InteriorOverrideToData $interior_override_to = null,
        public ?ModelChangeCodeData $model_change_code = null,
        public ?TransmissionRequiredData $transmission_required = null,
        public array $requires = [],

        #[DataCollectionOf(ColorRequiredData::class)]
        public array $color_required = [],

        #[DataCollectionOf(EquipmentData::class)]
        public array $equipment = [],

        #[DataCollectionOf(ExcludesData::class)]
        public array $excludes = [],

        #[DataCollectionOf(ExcludesStandardEquipmentData::class)]
        public array $excludes_standard_equipment = [],

        #[DataCollectionOf(EngineRequiredData::class)]
        public array $engine_required = [],

        #[DataCollectionOf(PriceData::class)]
        public array $prices = [],
    ) {}
}