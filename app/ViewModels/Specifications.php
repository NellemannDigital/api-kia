<?php

namespace App\ViewModels;

class Specifications
{
    public function __construct(public $trims) {}

    public function build(): array
    {
        $columns = $this->buildColumns();
        $sections = $this->getSections();

        $matrix = $this->buildMatrix($columns, $sections);

        $collapsedColumns = $this->collapseColumns($columns, $matrix);

        return [
            'columns' => $collapsedColumns,
            'sections' => $matrix,
        ];
    }

    protected function buildColumns(): array
    {
        $columns = [];

        foreach ($this->trims as $trim) {
            foreach ($trim->powertrains as $powertrain) {

                $columns[] = [
                    'key' => $trim->id . '-' . $powertrain->id,
                    'car' => $trim->car,
                    'trim' => $trim,
                    'powertrain' => $powertrain,
                ];
            }
        }

        return $columns;
    }

    protected function getSections(): array
    {
        return [
            [
                'title' => 'Motor',
                'show_header' => true,
                'page_break' => false,
                'b2b_only' => false,
                'rows' => [
                    [
                        'label' => 'Drivmiddel',
                        'resolve' => fn($col) => data_get($col, 'powertrain.engine.fuel_type'),
                    ],
                    [
                        'label' => 'Gearkasse',
                        'resolve' => fn($col) => data_get($col, 'powertrain.transmission.name'),
                    ],
                    [
                        'label' => 'Drivaksel',
                        'resolve' => fn($col) => data_get($col, 'powertrain.engine.drive'),
                    ],
                    [
                        'label' => 'Acceleration fra 0-100 km/t',
                        'resolve' => fn($col) => $this->spec($col, 'zero_to_hundred_time', 'sek', 1),
                    ],
                    [
                        'label' => 'Tophastighed',
                        'resolve' => fn($col) => $this->spec($col, 'topspeed', 'km/t'),
                    ],
                    [
                        'label' => 'Ydelse / Omdr.',
                        'resolve' => fn($col) => $this->specMultiple($col, 'horse_power', 'hk', 'horsepower_rev_range', '')
                            
                    ],
                    [
                        'label' => 'Drejningsmoment / Omdr.',
                        'resolve' => fn($col) => $this->specMultiple($col, 'torque', 'Nm', 'torque_rev_range', '')
                    ]
                ],
            ],

            [
                'title' => 'Rækkevidde & energiforbrug',
                'show_header' => false,
                'page_break' => false,
                'b2b_only' => false,
                'rows' => [
                    [
                        'label' => 'Rækkevidde (WLTP)',
                        'resolve' => fn($col) => $this->spec($col, 'pure_electric_range', 'km'),
                    ],
                    [
                        'label' => fn($col) => 'Forbrug (' . data_get($col, 'powertrain.configuration.technical_specifications.consumption.unit') . ')',
                        'resolve' => fn($col) => $this->spec($col, 'consumption.number', data_get($col, 'powertrain.configuration.technical_specifications.consumption.unit')),
                    ],
                ],
            ],

            [
                'title' => 'Opladning',
                'show_header' => false,
                'page_break' => false,
                'b2b_only' => false,
                'rows' => [
                    [
                        'label' => 'Normalopladning (AC) <br> Ladehastighed',
                        'resolve' => fn($col) => $this->spec($col, 'ac_charging_speed', 'kW'),
                    ],
                    [
                        'label' => fn($col) => 'Normalopladning  (AC) <br> Ladetid (' . data_get($col, 'powertrain.technical_specifications.ac_charging_percentage', '0-100') . '%)',
                        'resolve' => fn($col) =>
                            formatTimeString(data_get($col, 'powertrain.technical_specifications.ac_charging_time')),
                    ],
                    [
                        'label' => 'Hurtigopladning (DC) <br> Ladehastighed',
                        'resolve' => fn($col) => $this->spec($col, 'dc_charging_speed', 'kW'),
                    ],
                    [
                        'label' => fn($col) => 'Hurtigopladning (DC) <br> Ladetid (' . data_get($col, 'powertrain.technical_specifications.dc_charging_percentage', '10-80') . '%)',
                        'resolve' => fn($col) =>
                            formatTimeString(data_get($col, 'powertrain.technical_specifications.dc_charging_time')),
                    ],
                    [
                        'label' => 'Ladetype',
                        'resolve' => fn($col) => data_get($col, 'powertrain.transmission.charge_plug_type'),
                    ],
                ],
            ],

            [
                'title' => 'Batteri',
                'show_header' => true,
                'page_break' => true,
                'b2b_only' => false,
                'rows' => [
                    [
                        'label' => 'Batteristørrelse',
                        'resolve' => fn($col) => $this->spec($col, 'battery_size', 'kWh', 1),
                    ],
                    [
                        'label' => 'Batteritype',
                        'resolve' => fn($col) => $this->spec($col, 'battery_type'),
                    ],
                    [
                        'label' => 'Batterispænding',
                        'resolve' => fn($col) => $this->spec($col, 'battery_voltage', 'V'),
                    ],
                    [
                        'label' => 'Batterivægt',
                        'resolve' => fn($col) => $this->spec($col, 'battery_weight', 'kg', 1),
                    ],
                ],
            ],

            [
                'title' => 'Vægt',
                'show_header' => false,
                'page_break' => false,
                'b2b_only' => false,
                'rows' => [
                    [
                        'label' => 'Egenvægt',
                        'resolve' => fn($col) => $this->spec($col, 'net_weight', 'kg'),
                    ],
                    [
                        'label' => 'Køreklar vægt',
                        'resolve' => fn($col) => $this->spec($col, 'driving_ready_weight', 'kg'),
                    ],
                    [
                        'label' => 'Totalvægt',
                        'resolve' => fn($col) => $this->spec($col, 'maximum_total_weight', 'kg'),
                    ],
                    [
                        'label' => 'Påhængsvægt med bremser',
                        'resolve' => fn($col) => $this->spec($col, 'towing_capacity_braked', 'kg'),
                    ],
                    [
                        'label' => 'Påhængsvægt uden bremser',
                        'resolve' => fn($col) => $this->spec($col, 'towing_capacity_unbraked', 'kg'),
                    ],
                    [
                        'label' => 'Taglast: Dynamisk / Statisk',
                        'resolve' => fn($col) => $this->specMultiple($col, 'dynamic_roof_load', 'kg', 'static_roof_load', 'kg')
                    ],
                ],
            ],

            [
                'title' => 'Dimensioner',
                'show_header' => false,
                'page_break' => false,
                'b2b_only' => false,
                'rows' => [
                    [
                        'label' => 'Længde',
                        'resolve' => fn($col) => $this->spec($col, 'length', 'mm'),
                    ],
                    [
                        'label' => 'Bredde',
                        'resolve' => fn($col) => $this->spec($col, 'width', 'mm'),
                    ],
                    [
                        'label' => 'Højde',
                        'resolve' => fn($col) => $this->spec($col, 'height', 'mm'),
                    ],
                    [
                        'label' => 'Frihøjde',
                        'resolve' => fn($col) => $this->spec($col, 'minimum_groundclearance', 'mm'),
                    ],
                    [
                        'label' => 'Akselafstand',
                        'resolve' => fn($col) => $this->spec($col, 'wheelbase', 'mm'),
                    ],
                    [
                        'label' => 'Bagagerumskapacitet',
                        'resolve' => fn($col) => $this->spec($col, 'trunk_volume', 'L'),
                    ],
                    [
                        'label' => 'Frunk',
                        'resolve' => fn($col) => $this->spec($col, 'frunk_volume', 'L'),
                    ],
                ],
            ],

            [
                'title' => 'Varerumsdimensioner',
                'show_header' => true,
                'page_break' => true,
                'b2b_only' => true,
                'rows' => [
                    [
                        'label' => 'Varerumsvolumen',
                        'resolve' => fn($col) => $this->spec($col, 'cargo_volume', 'm3', 1),
                    ],
                    [
                        'label' => 'Varerumshøjde',
                        'resolve' => fn($col) => $this->spec($col, 'cargo_height', 'cm'),
                    ],
                    [
                        'label' => 'Varerumslængde',
                        'resolve' => fn($col) => $this->spec($col, 'cargo_length', 'cm'),
                    ],
                    [
                        'label' => 'Varerumsbredde, maks.',
                        'resolve' => fn($col) => $this->spec($col, 'cargo_width_max', 'cm'),
                    ],
                    [
                        'label' => 'Varerumsbredde, min.',
                        'resolve' => fn($col) => $this->spec($col, 'cargo_width_min', 'cm'),
                    ],
                    [
                        'label' => 'Læssehøjde, bag',
                        'resolve' => fn($col) => $this->spec($col, 'loading_height_rear', 'cm', 1),
                    ],
                    [
                        'label' => 'Læssehøjde, side',
                        'resolve' => fn($col) => $this->spec($col, 'loading_height_side', 'cm', 1),
                    ],
                    [
                        'label' => 'Afstand mellem hjulkasser',
                        'resolve' => fn($col) => $this->spec($col, 'cargo_width_wheel_housing', 'cm'),
                    ],
                ],
            ],

            [
                'title' => 'Chassisdimensioner',
                'show_header' => false,
                'page_break' => false,
                'b2b_only' => true,
                'rows' => [
                    [
                        'label' => 'Chassislængde',
                        'resolve' => fn($col) => $this->spec($col, 'chassis_length', 'cm'),
                    ],
                    [
                        'label' => 'Maksimalt udhæng',
                        'resolve' => fn($col) => $this->spec($col, 'chassis_max_overhang', 'cm'),
                    ],
                ],
            ],
        ];
    }

    protected function spec(array $col, string $path, ?string $unit = null, $decimals = 0): string
    {
        $value =
            data_get($col, "powertrain.technical_specifications.$path")
            ?? data_get($col, "powertrain.engine.$path")
            ?? data_get($col, "powertrain.transmission.$path")
            ?? data_get($col, "trim.technical_specifications.$path")
            ?? data_get($col, "powertrain.configuration.technical_specifications.$path")
            ?? data_get($col, "car.dimensions.$path");

        if (!filled($value)) {
            return '';
        }

        if ($decimals !== null) {
            $value = formatNumber($value, $decimals);
        }

        return $unit ? "{$value} {$unit}" : (string) $value;
    }

    protected function specMultiple($col, $value1, $unit1 = null, $value2, $unit2 = null): string
    {
        $value1 = $this->spec($col, $value1, $unit1, null);
        $value2 = $this->spec($col, $value2, $unit2, null);

        return $value1 . (filled($value2) ? " / $value2" : '');
    }

    protected function buildMatrix(array $columns, array $sections): array
    {
        foreach ($sections as $sectionIndex => &$section) {

            foreach ($section['rows'] as $rowIndex => &$row) {

                $row['values'] = [];
                $row['labels'] = [];

                foreach ($columns as $col) {

                    $value = ($row['resolve'])($col);
                    $row['values'][] = $this->normalize($value);

                    $label = is_callable($row['label'] ?? null)
                        ? ($row['label'])($col)
                        : $row['label'] ?? null;

                    $row['labels'][] = $label;

                }

                unset($row['resolve']);

                $hasAnyValue = collect($row['values'])
                    ->filter(fn ($v) => $v !== '-' && filled($v))
                    ->isNotEmpty();

                if (! $hasAnyValue) {
                    unset($section['rows'][$rowIndex]);
                }
            }

            if (empty($section['rows'])) {
                unset($sections[$sectionIndex]);
            }
        }

        return $sections;
    }

    protected function collapseColumns(array $columns, array $sections): array
    {
        $columnValues = [];

        foreach ($columns as $colIndex => $col) {

            $values = [];

            foreach ($sections as $section) {
                foreach ($section['rows'] as $row) {
                    $values[] = $row['values'][$colIndex] ?? null;
                }
            }

            $columnValues[$colIndex] = $values;
        }

        $groups = [];

        foreach ($columns as $index => $col) {

            $signature = sha1(implode('|', $columnValues[$index]));

            if (!isset($groups[$signature])) {
                $groups[$signature] = [
                    'signature' => $signature,
                    'columns' => [],
                    'display_index' => $index,
                ];
            }

            $groups[$signature]['columns'][] = $col;
        }

        return array_values($groups);
    }

    protected function normalize($value): string
    {
        return match (true) {
            is_null($value) => '-',
            is_bool($value) => $value ? '1' : '0',
            default => trim((string) $value),
        };
    }
}