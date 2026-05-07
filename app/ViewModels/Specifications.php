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
                'rows' => [
                    [
                        'label' => 'Motortype',
                        'resolve' => fn($col) => data_get($col, 'powertrain.engine.fuel_type'),
                    ],
                    [
                        'label' => 'Ydelse / Omdr.',
                        'resolve' => fn($col) => $this->specWithRange($col, 'horse_power', 'horsepower_rev_range', 'hk')
                            
                    ],
                    [
                        'label' => 'Drejningsmoment / Omdr.',
                        'resolve' => fn($col) => $this->specWithRange($col, 'torque', 'torque_rev_range', 'Nm')
                    ],
                    [
                        'label' => 'Drivaksel',
                        'resolve' => fn($col) => data_get($col, 'powertrain.engine.drive'),
                    ],
                    [
                        'label' => 'Gearkasse',
                        'resolve' => fn($col) => data_get($col, 'powertrain.transmission.name'),
                    ],
                ],
            ],

            [
                'title' => 'Batteri',
                'show_header' => false,
                'rows' => [
                    [
                        'label' => 'Batteristørrelse',
                        'resolve' => fn($col) => $this->spec($col, 'battery_size', 'kWh'),
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
                'title' => 'Opladning',
                'show_header' => false,
                'rows' => [
                    [
                        'label' => 'Normalopladning (AC) Ladehastighed',
                        'resolve' => fn($col) => $this->spec($col, 'ac_charging_speed', 'kW'),
                    ],
                    [
                        'label' => 'Normalopladning (AC) Ladetid (0-100%)',
                        'resolve' => fn($col) =>
                            formatTimeString(data_get($col, 'powertrain.technical_specifications.ac_charging_time')),
                    ],
                    [
                        'label' => 'Hurtigopladning (DC) Ladehastighed',
                        'resolve' => fn($col) => $this->spec($col, 'dc_charging_speed', 'kW'),
                    ],
                    [
                        'label' => 'Hurtigopladning (DC) Ladetid (10%-80%)',
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
                'title' => 'Præstationer',
                'show_header' => true,
                'rows' => [
                    [
                        'label' => 'Acceleration fra 0-100 km/t',
                        'resolve' => fn($col) => $this->spec($col, 'zero_to_hundred_time', 'sek', 1),
                    ],
                    [
                        'label' => 'Tophastighed',
                        'resolve' => fn($col) => $this->spec($col, 'topspeed', 'km/t'),
                    ],
                ],
            ],

            [
                'title' => 'Rækkevidde & energiforbrug',
                'show_header' => false,
                'rows' => [
                    [
                        'label' => 'Rækkevidde (WLTP)',
                        'resolve' => fn($col) => $this->spec($col, 'pure_electric_range', 'km'),
                    ],
                    [
                        'label' => 'Forbrug (WLTP)',
                        'resolve' => fn($col) => $this->spec($col, 'consumption.number', data_get($col, 'powertrain.configuration.technical_specifications.consumption.unit')),
                    ],
                ],
            ],

            [
                'title' => 'Vægt',
                'show_header' => false,
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
                ],
            ],

            [
                'title' => 'Udvendige mål',
                'show_header' => false,
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
                ],
            ],

            [
                'title' => 'Indvendige mål',
                'show_header' => false,
                'rows' => [
                    [
                        'label' => 'Bagagerumskapacitet',
                        'resolve' => fn($col) => $this->spec($col, 'trunk_volume', 'L'),
                    ],
                ],
            ],
        ];
    }

    protected function spec(array $col, string $path, ?string $unit = null, int $decimals = null): string
    {
        $value =
            data_get($col, "powertrain.technical_specifications.$path")
            ?? data_get($col, "powertrain.engine.$path")
            ?? data_get($col, "powertrain.transmission.$path")
            ?? data_get($col, "trim.technical_specifications.$path")
            ?? data_get($col, "powertrain.configuration.technical_specifications.$path");

        if (!filled($value)) {
            return '';
        }

        if ($decimals !== null) {
            $value = formatNumber($value, $decimals);
        }

        return $unit ? "{$value} {$unit}" : (string) $value;
    }

    protected function specWithRange($col, $valueKey, $rangeKey, $unit = null): string
    {
        $value = $this->spec($col, $valueKey, $unit);
        $range = $this->spec($col, $rangeKey);

        if (!filled($value)) {
            return '';
        }

        return $value . (filled($range) ? " / $range" : '');
    }

    protected function buildMatrix(array $columns, array $sections): array
    {
        foreach ($sections as &$section) {

            foreach ($section['rows'] as &$row) {

                $row['values'] = [];

                foreach ($columns as $col) {

                    $value = ($row['resolve'])($col);

                    $row['values'][] = $this->normalize($value);
                }

                unset($row['resolve']);
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