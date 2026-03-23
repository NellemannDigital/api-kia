<?php

namespace App\ViewModels;

class Specifications
{
    public function __construct(public $trims) {}

    public function columns()
    {
        return $this->trims->flatMap(function ($trim) {
            return $trim->powertrains->map(function ($powertrain) use ($trim) {
                return (object) [
                    'trim' => $trim,
                    'powertrain' => $powertrain,
                ];
            });
        });
    }

    protected function groupedColumnsGlobally(array $sections, $columns)
    {

        $columnsWithSignature = collect($columns)->map(function ($col) use ($sections) {
            $allValues = collect($sections)
                ->flatMap(fn($section) => $section['rows'])
                ->map(fn($row) => trim((string) $row['value']($col)))
                ->implode('|');

            return (object) [
                'col' => $col,
                'signature' => $allValues,
            ];
        });

        $grouped = $columnsWithSignature->groupBy('signature')->map(function ($group) {
            $first = $group->first()->col;

            $trimNames = $group->map(fn($item) => $item->col->trim->name)->unique()->implode(' & ');
            $engineName = $first->powertrain->engine->name;

            return (object) [
                'columns' => $group->pluck('col'),
                'label' => "<span class='font-bold'>{$trimNames}</span> <br> <span>{$engineName}</span>",
            ];
        });

        return $grouped->values();
    }

    public function sections()
    {
        $columns = $this->columns();

        $sections = [
            [
                'title' => 'Motor',
                'show_header' => true,
                'page_break' => false,
                'columns' => $columns,
                'rows' => [
                    [
                        'label' => 'Motortype',  
                        'value' => fn($col) => $col->powertrain->engine->fuel_type
                    ],
                    [
                        'label' => 'Ydelse / Omdr.', 
                        'value' => fn($col) => "{$col->powertrain->engine->horse_power} hk / {$col->powertrain->engine->horsepower_rev_range}"
                    ],
                    [
                        'label' => 'Drejgningsmomemt / Omdr.',
                        'value' => fn($col) => "{$col->powertrain->technical_specifications->torque} Nm / {$col->powertrain->technical_specifications->torque_rev_range}"
                    ],
                    [
                        'label' => 'Drivaksel',
                        'value' => fn($col) => '-'
                    ],
                    [
                        'label' => 'Gearkasse',
                        'value' => fn($col) => $col->powertrain->transmission->name
                    ],
                ],
            ],
            [
                'title' => 'Batteri',
                'show_header' => false,
                'page_break' => false,
                'columns' => $columns,
                'rows' => [
                    [
                        'label' => 'Batteristørrelse', 
                        'value' => fn($col) => "{$col->powertrain->technical_specifications->battery_size} kWh"
                    ],
                    [
                        'label' => 'Batteritype', 
                        'value' => fn($col) => $col->powertrain->technical_specifications->battery_type
                    ],
                    [
                        'label' => 'Batterispænding', 
                        'value' => fn($col) => "{$col->powertrain->technical_specifications->battery_voltage} V"
                    ],
                    [
                        'label' => 'Batterivægt', 
                        'value' => fn($col) => formatNumber($col->powertrain->technical_specifications->battery_weight, 1) . ' kg'
                    ]
                ],
            ],
            [
                'title' => 'Opladning',
                'show_header' => false,
                'page_break' => true,
                'columns' => $columns,
                'rows' => [
                    [
                        'label' => 'Normalopladning (AC) <br> Ladehastighed', 
                        'value' => fn($col) => "{$col->powertrain->technical_specifications->ac_charging_speed} kW"
                    ],
                    [
                        'label' => 'Normalopladning (AC) <br> Ladetid (0-100%)', 
                        'value' => fn($col) => formatTimeString($col->powertrain->technical_specifications->ac_charging_time)
                    ],
                    [
                        'label' => 'Hurtigopladning (DC) <br> Ladehastighed', 
                         'value' => fn($col) => "{$col->powertrain->technical_specifications->dc_charging_speed} kW"
                    ],
                    [
                        'label' => 'Hurtigopladning (DC) <br> Ladetid (10%-80%)', 
                        'value' => fn($col) => formatTimeString($col->powertrain->technical_specifications->dc_charging_time)
                    ],
                    [
                        'label' => 'Ladetype',
                        'value' => fn($col) => $col->powertrain->transmission->charge_plug_type
                    ],
                ],
            ],
            [
                'title' => 'Præstationer',
                'show_header' => false,
                'page_break' => false,
                'columns' => $columns,
                'rows' => [
                    [
                        'label' => 'Acceleration fra 0-100 km/t', 
                        'value' => fn($col) => formatNumber($col->powertrain->technical_specifications->zero_to_hundred_time, 1) . ' sek'
                    ],
                    [
                        'label' => 'Tophastighed', 
                        'value' => fn($col) => "{$col->powertrain->technical_specifications->topspeed} km/t"
                    ],
                ],
            ],
            [
                'title' => 'Rækkevidde & energiforbrug',
                'show_header' => false,
                'page_break' => false,
                'columns' => $columns,
                'rows' => [
                    [
                        'label' => 'Rækkevidde (WLTP)', 
                        'value' => fn($col) => "{$col->powertrain->configuration?->pure_electric_range} km"
                    ],
                    [
                        'label' => 'Forbrug (WLTP)', 
                        'value' => fn($col) => "{$col->powertrain->configuration?->consumption} Wh/km"
                    ],
                ],
            ],
            [
                'title' => 'Vægt',
                'show_header' => false,
                'page_break' => false,
                'columns' => $columns,
                'rows' => [
                    [
                        'label' => 'Egenvægt', 
                        'value' => fn($col) => formatNumber($col->powertrain->technical_specifications->net_weight) . ' kg'
                    ],
                    [
                        'label' => 'Køreklar vægt', 
                        'value' => fn($col) => formatNumber($col->powertrain->technical_specifications->driving_ready_weight) . ' kg'
                    ],
                    [
                        'label' => 'Totalvægt', 
                        'value' => fn($col) => formatNumber($col->powertrain->technical_specifications->maximum_total_weight) . ' kg'
                    ],
                    [
                        'label' => 'Påhængsvægt med bremser', 
                        'value' => fn($col) => formatNumber($col->powertrain->technical_specifications->towing_capacity_braked) . ' kg'
                    ],
                    [
                        'label' => 'Påhængsvægt uden bremser', 
                        'value' => fn($col) => formatNumber($col->powertrain->technical_specifications->towing_capacity_unbraked) . ' kg'
                    ],
                ],
            ],
            [
                'title' => 'Udvendige mål',
                'show_header' => false,
                'page_break' => false,
                'columns' => $columns,
                'rows' => [
                    [
                        'label' => 'Længde', 
                        'value' => fn($col) => formatNumber($col->trim->technical_specifications->length) . ' mm'
                    ],
                    [
                        'label' => 'Bredde', 
                        'value' => fn($col) => formatNumber($col->trim->technical_specifications->width) . ' mm'
                    ],
                    [
                        'label' => 'Højde', 
                        'value' => fn($col) => formatNumber($col->trim->technical_specifications->height) . ' mm'
                    ],
                    [
                        'label' => 'Frihøjde', 
                        'value' => fn($col) => formatNumber($col->trim->technical_specifications->minimum_groundclearance) . ' mm'
                    ],
                    [
                        'label' => 'Akselafstand', 
                        'value' => fn($col) => formatNumber($col->trim->technical_specifications->wheelbase) . ' mm'
                    ],
                ],
            ],
            [
                'title' => 'Indvendige mål',
                'show_header' => false,
                'page_break' => false,
                'columns' => $columns,
                'rows' => [
                    [
                        'label' => 'Bagagerumskapacitet', 
                        'value' => fn($col) => "{$col->powertrain->technical_specifications->trunk_volume} L"
                    ],
                ],
            ],
        ];
        $groupedColumns = $this->groupedColumnsGlobally($sections, $columns);

        foreach ($sections as &$section) {
            $section['columns'] = $groupedColumns;
        }

        return $sections;
    }
}