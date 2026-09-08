<?php

namespace App\Mcp\Support;

use App\Models\Car;
use App\Models\ComplianceTextTemplate;
use App\Models\Configuration;
use App\Models\Dealer;
use App\Models\StockCar;
use App\Models\Trim;
use App\Models\UsedCar;
use BackedEnum;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use JsonSerializable;
use Stringable;
use Throwable;

class KiaDataSources
{
    /**
     * @var array<string, array{label: string, model: class-string<Model>, columns: array<int, string>}>
     */
    private const SOURCES = [
        'cars' => [
            'label' => 'Cars',
            'model' => Car::class,
            'columns' => [
                'id',
                'struct_id',
                'web_id',
                'name',
                'year',
                'custom_disclaimer',
                'campaign_disclaimer',
                'delivery',
                'model',
                'variant',
                'technical_specifications',
                'dimensions',
                'campaign',
                'urls',
                'price_list',
                'categories',
                'warranties',
            ],
        ],
        'trims' => [
            'label' => 'Trims',
            'model' => Trim::class,
            'columns' => [
                'id',
                'struct_id',
                'name',
                'interior',
                'technical_specifications',
                'campaign',
                'accessory_mapping',
                'featured_product_details',
            ],
        ],
        'configurations' => [
            'label' => 'Configurations',
            'model' => Configuration::class,
            'columns' => [
                'id',
                'struct_id',
                'model_code',
                'grade',
                'ocn',
                'model',
                'year',
                'variant',
                'trim',
                'engine',
                'transmission',
                'technical_specifications',
                'model_change_code',
                'original_model_change_code',
            ],
        ],
        'dealers' => [
            'label' => 'Dealers',
            'model' => Dealer::class,
            'columns' => [
                'id',
                'dynamics_id',
                'account_number',
                'company_id',
                'crm_id',
                'dealerbridge_id',
                'bilinfo_id',
                'autouncle_department_id',
                'rooftop_id',
                'dealer_guid',
                'owner_guid',
                'tools',
                'name',
                'display_name',
                'cvr_number',
                'group',
                'street_name',
                'street_number',
                'city',
                'zip_code',
                'country',
                'phone',
                'emails',
                'urls',
                'types',
                'opening_hours',
                'special_opening_hours',
                'postal_codes',
            ],
        ],
        'stock_cars' => [
            'label' => 'Stock Cars',
            'model' => StockCar::class,
            'columns' => [
                'id',
                'dynamics_id',
                'vehicle_number',
                'name',
                'struct_id',
                'vin',
                'model_code',
                'model_year',
                'exterior',
                'interior',
                'equipment',
            ],
        ],
        'used_cars' => [
            'label' => 'Used Cars',
            'model' => UsedCar::class,
            'columns' => [
                'id',
                'vehicle_id',
                'mileage',
                'year',
                'make',
                'model',
                'variant',
                'registration_date',
            ],
        ],
        'compliance_text_templates' => [
            'label' => 'Compliance Text Templates',
            'model' => ComplianceTextTemplate::class,
            'columns' => [
                'id',
                'variant',
                'template',
                'version',
                'valid_from',
                'valid_to',
            ],
        ],
    ];

    /**
     * @return array<int, string>
     */
    public static function entityNames(): array
    {
        return array_keys(self::SOURCES);
    }

    public static function labelFor(string $entity): string
    {
        return self::sourceFor($entity)['label'];
    }

    /**
     * @return class-string<Model>
     */
    public static function modelFor(string $entity): string
    {
        return self::sourceFor($entity)['model'];
    }

    /**
     * @return array<int, string>
     */
    public static function columnsFor(string $entity): array
    {
        return self::sourceFor($entity)['columns'];
    }

    public static function queryFor(string $entity, bool $includeHidden = false): Builder
    {
        $model = self::modelFor($entity);
        $query = $model::query();

        if ($includeHidden) {
            $query->withoutGlobalScopes();
        }

        return $query->with(self::relationsFor($entity, $includeHidden));
    }

    public static function applySearch(Builder $query, string $entity, string $search): Builder
    {
        $needles = self::searchNeedles($search);
        $columns = self::columnsFor($entity);

        return $query->where(function (Builder $outerQuery) use ($columns, $needles): void {
            foreach ($needles as $needle) {
                $outerQuery->orWhere(function (Builder $innerQuery) use ($columns, $needle): void {
                    foreach ($columns as $column) {
                        $innerQuery->orWhere($column, 'like', "%{$needle}%");
                    }
                });
            }
        });
    }

    /**
     * @return array<string, mixed>
     */
    public static function searchResult(Model $record, string $entity, string $search): array
    {
        $matchedFields = self::matchedFields($record, $entity, $search);
        $title = self::titleFor($record, $entity);

        return [
            'entity' => $entity,
            'entity_label' => self::labelFor($entity),
            'id' => (string) $record->getKey(),
            'title' => $title,
            'summary' => self::summaryFor($record, $entity),
            'matched_fields' => $matchedFields,
            'score' => self::scoreFor($title, $matchedFields, $record, $entity, $search),
            'updated_at' => optional($record->updated_at)->toISOString(),
            'preview' => self::previewFor($record, $entity),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function serializeRecord(Model $record, string $entity): array
    {
        return [
            '_mcp' => [
                'entity' => $entity,
                'entity_label' => self::labelFor($entity),
                'id' => $record->getKey(),
                'title' => self::titleFor($record, $entity),
                'summary' => self::summaryFor($record, $entity),
            ],
            'data' => $record->toArray(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function sourceFor(string $entity): array
    {
        if (! array_key_exists($entity, self::SOURCES)) {
            throw new \InvalidArgumentException("Unknown Kia data source [{$entity}].");
        }

        return self::SOURCES[$entity];
    }

    /**
     * @return array<int|string, mixed>
     */
    private static function relationsFor(string $entity, bool $includeHidden): array
    {
        return match ($entity) {
            'cars' => [
                'trims' => fn (Builder|Relation $query) => self::optionallyIncludeHidden($query, $includeHidden),
                'trims.powertrains' => fn (Builder|Relation $query) => self::optionallyIncludeHidden($query, $includeHidden),
                'trims.powertrains.prices',
                'trims.powertrains.leasingPrices',
            ],
            'trims' => [
                'car' => fn (Builder|Relation $query) => self::optionallyIncludeHidden($query, $includeHidden),
                'powertrains' => fn (Builder|Relation $query) => self::optionallyIncludeHidden($query, $includeHidden),
                'powertrains.prices',
                'powertrains.leasingPrices',
            ],
            'configurations' => [
                'car' => fn (Builder|Relation $query) => self::optionallyIncludeHidden($query, $includeHidden),
                'trim' => fn (Builder|Relation $query) => self::optionallyIncludeHidden($query, $includeHidden),
                'powertrain' => fn (Builder|Relation $query) => self::optionallyIncludeHidden($query, $includeHidden),
                'powertrain.prices',
                'powertrain.leasingPrices',
            ],
            'stock_cars' => [
                'dealer',
                'configuration',
                'configuration.car' => fn (Builder|Relation $query) => self::optionallyIncludeHidden($query, $includeHidden),
                'configuration.trim' => fn (Builder|Relation $query) => self::optionallyIncludeHidden($query, $includeHidden),
                'configuration.powertrain' => fn (Builder|Relation $query) => self::optionallyIncludeHidden($query, $includeHidden),
                'configuration.powertrain.prices',
            ],
            default => [],
        };
    }

    private static function optionallyIncludeHidden(Builder|Relation $query, bool $includeHidden): void
    {
        if ($includeHidden) {
            $query instanceof Relation
                ? $query->getQuery()->withoutGlobalScopes()
                : $query->withoutGlobalScopes();
        }
    }

    /**
     * @return array<int, string>
     */
    private static function searchNeedles(string $search): array
    {
        $tokens = preg_split('/\s+/', trim($search)) ?: [];

        return collect([$search, ...$tokens])
            ->map(fn (string $needle): string => trim($needle))
            ->filter(fn (string $needle): bool => mb_strlen($needle) >= 2)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private static function matchedFields(Model $record, string $entity, string $search): array
    {
        $needles = collect(self::searchNeedles(Str::lower($search)));

        return collect(self::columnsFor($entity))
            ->filter(function (string $field) use ($needles, $record): bool {
                $haystack = Str::lower(self::stringify($record->getAttribute($field)));

                return $needles->contains(fn (string $needle): bool => str_contains($haystack, $needle));
            })
            ->values()
            ->all();
    }

    /**
     * @param  array<int, string>  $matchedFields
     */
    private static function scoreFor(string $title, array $matchedFields, Model $record, string $entity, string $search): int
    {
        $score = count($matchedFields) * 10;
        $normalizedSearch = Str::lower(trim($search));
        $normalizedTitle = Str::lower(trim($title));

        if ($normalizedTitle === $normalizedSearch) {
            $score += 100;
        } elseif (str_starts_with($normalizedTitle, $normalizedSearch)) {
            $score += 70;
        } elseif (str_contains($normalizedTitle, $normalizedSearch)) {
            $score += 50;
        }

        foreach (self::columnsFor($entity) as $field) {
            $value = Str::lower(self::stringify($record->getAttribute($field)));

            if (str_contains($value, $normalizedSearch)) {
                $score += 5;
            }
        }

        return $score;
    }

    private static function titleFor(Model $record, string $entity): string
    {
        return match ($entity) {
            'cars' => (string) $record->getAttribute('name'),
            'trims' => self::compactText([
                $record->getAttribute('name'),
                optional($record->getRelationValue('car'))->name,
            ]),
            'configurations' => self::compactText([
                $record->getAttribute('model_code'),
                $record->getAttribute('grade'),
                $record->getAttribute('trim'),
                $record->getAttribute('year'),
            ]) ?: 'Configuration #'.$record->getKey(),
            'dealers' => (string) ($record->getAttribute('display_name') ?: $record->getAttribute('name')),
            'stock_cars' => self::compactText([
                $record->getAttribute('name'),
                $record->getAttribute('vehicle_number'),
            ]),
            'used_cars' => self::compactText([
                $record->getAttribute('make'),
                $record->getAttribute('model'),
                $record->getAttribute('variant'),
                $record->getAttribute('year'),
                $record->getAttribute('vehicle_id'),
            ]),
            'compliance_text_templates' => self::compactText([
                $record->getAttribute('variant'),
                'v'.$record->getAttribute('version'),
            ]),
            default => class_basename($record).' #'.$record->getKey(),
        } ?: class_basename($record).' #'.$record->getKey();
    }

    private static function summaryFor(Model $record, string $entity): string
    {
        return match ($entity) {
            'cars' => self::compactText([
                $record->getAttribute('year'),
                self::labelValue('web_id', $record->getAttribute('web_id')),
                self::labelValue('struct_id', $record->getAttribute('struct_id')),
                self::money($record->getAttribute('from_price')),
            ]),
            'trims' => self::compactText([
                self::labelValue('trim_id', $record->getKey()),
                self::labelValue('struct_id', $record->getAttribute('struct_id')),
                self::money(self::minimumRetailPrice($record->getRelationValue('powertrains'))),
            ]),
            'configurations' => self::compactText([
                self::labelValue('struct_id', $record->getAttribute('struct_id')),
                self::labelValue('ocn', $record->getAttribute('ocn')),
                optional($record->getRelationValue('car'))->name,
                optional($record->getRelationValue('powertrain'))->ocn,
            ]),
            'dealers' => self::compactText([
                $record->getAttribute('city'),
                $record->getAttribute('zip_code'),
                $record->getAttribute('street_name').' '.$record->getAttribute('street_number'),
                $record->getAttribute('phone'),
            ]),
            'stock_cars' => self::compactText([
                self::labelValue('VIN', $record->getAttribute('vin')),
                self::labelValue('model_code', $record->getAttribute('model_code')),
                optional($record->getRelationValue('dealer'))->display_name ?: optional($record->getRelationValue('dealer'))->name,
                self::money(self::minimumRetailPrice(collect([optional($record->getRelationValue('configuration'))->powertrain]))),
            ]),
            'used_cars' => self::compactText([
                self::labelValue('vehicle_id', $record->getAttribute('vehicle_id')),
                $record->getAttribute('mileage') ? $record->getAttribute('mileage').' km' : null,
                $record->getAttribute('registration_date'),
            ]),
            'compliance_text_templates' => Str::limit((string) $record->getAttribute('template'), 180),
            default => class_basename($record).' #'.$record->getKey(),
        };
    }

    /**
     * @return array<string, mixed>
     */
    private static function previewFor(Model $record, string $entity): array
    {
        $preview = match ($entity) {
            'cars' => [
                'id' => $record->getKey(),
                'name' => $record->getAttribute('name'),
                'web_id' => $record->getAttribute('web_id'),
                'struct_id' => $record->getAttribute('struct_id'),
                'year' => $record->getAttribute('year'),
                'from_price' => $record->getAttribute('from_price'),
            ],
            'trims' => [
                'id' => $record->getKey(),
                'name' => $record->getAttribute('name'),
                'car' => optional($record->getRelationValue('car'))->name,
                'struct_id' => $record->getAttribute('struct_id'),
                'from_price' => self::minimumRetailPrice($record->getRelationValue('powertrains')),
            ],
            'configurations' => [
                'id' => $record->getKey(),
                'model_code' => $record->getAttribute('model_code'),
                'grade' => $record->getAttribute('grade'),
                'ocn' => $record->getAttribute('ocn'),
                'year' => $record->getAttribute('year'),
                'car' => optional($record->getRelationValue('car'))->name,
                'trim' => $record->getAttribute('trim'),
            ],
            'dealers' => [
                'id' => $record->getKey(),
                'name' => $record->getAttribute('name'),
                'display_name' => $record->getAttribute('display_name'),
                'dealer_guid' => $record->getAttribute('dealer_guid'),
                'city' => $record->getAttribute('city'),
                'zip_code' => $record->getAttribute('zip_code'),
                'phone' => $record->getAttribute('phone'),
            ],
            'stock_cars' => [
                'id' => $record->getKey(),
                'name' => $record->getAttribute('name'),
                'vehicle_number' => $record->getAttribute('vehicle_number'),
                'vin' => $record->getAttribute('vin'),
                'model_code' => $record->getAttribute('model_code'),
                'dealer' => optional($record->getRelationValue('dealer'))->display_name ?: optional($record->getRelationValue('dealer'))->name,
            ],
            'used_cars' => [
                'id' => $record->getKey(),
                'vehicle_id' => $record->getAttribute('vehicle_id'),
                'mileage' => $record->getAttribute('mileage'),
                'year' => $record->getAttribute('year'),
                'make' => $record->getAttribute('make'),
                'model' => $record->getAttribute('model'),
                'variant' => $record->getAttribute('variant'),
            ],
            'compliance_text_templates' => [
                'id' => $record->getKey(),
                'variant' => $record->getAttribute('variant'),
                'version' => $record->getAttribute('version'),
                'valid_from' => optional($record->getAttribute('valid_from'))->toDateString(),
                'valid_to' => optional($record->getAttribute('valid_to'))->toDateString(),
                'template_excerpt' => Str::limit((string) $record->getAttribute('template'), 120),
            ],
            default => [
                'id' => $record->getKey(),
            ],
        };

        return Arr::where($preview, fn (mixed $value): bool => ! blank($value));
    }

    private static function stringify(mixed $value): string
    {
        if ($value instanceof BackedEnum) {
            return (string) $value->value;
        }

        if ($value instanceof Arrayable) {
            $value = $value->toArray();
        } elseif ($value instanceof JsonSerializable) {
            $value = $value->jsonSerialize();
        }

        if ($value instanceof Stringable || is_scalar($value)) {
            return (string) $value;
        }

        if (is_array($value) || is_object($value)) {
            try {
                return json_encode($value, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '';
            } catch (Throwable) {
                return '';
            }
        }

        return '';
    }

    private static function compactText(array $parts): string
    {
        return collect($parts)
            ->filter(fn (mixed $part): bool => ! blank($part))
            ->implode(' | ');
    }

    private static function labelValue(string $label, mixed $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        return "{$label} {$value}";
    }

    private static function money(mixed $amount): ?string
    {
        if (! is_numeric($amount)) {
            return null;
        }

        return number_format((float) $amount, 0, ',', '.').' DKK';
    }

    private static function minimumRetailPrice(mixed $powertrains): mixed
    {
        if ($powertrains instanceof Model) {
            $powertrains = collect([$powertrains]);
        }

        if (! $powertrains instanceof Collection) {
            return null;
        }

        return $powertrains
            ->filter()
            ->flatMap(fn (Model $powertrain): mixed => $powertrain->getRelationValue('prices') ?? [])
            ->pluck('suggested_retail_price')
            ->filter()
            ->min();
    }
}
