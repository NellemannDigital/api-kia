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

    public function sections()
    {
        $columns = $this->columns();

        return [
            [
                'title' => 'Pris',
                'show_header' => true,
                'columns' => $columns,
                'rows' => [
                    [
                        'label' => 'Kontantpris',
                        'class' => 'border-primary-low border-x border-t rounded-t',
                        'value' => fn ($col) => $col->powertrain->prices->first()?->suggested_retail_price,
                    ],
                    [
                        'label' => 'Månedlig ydelse',
                        'class' => 'border-primary-low border-x border-b rounded-b',
                        'value' => fn ($col) => $col->powertrain->prices->first()?->monthly_price,
                    ],
                ],
            ],
            [
                'title' => 'Motor',
                'show_header' => false,
                'columns' => $columns,
                'rows' => [
                    [
                        'label' => 'HK',
                        'class' => 'border-primary-low border-x border-t rounded-t',
                        'value' => fn ($col) => $col->powertrain->engine->horse_power,
                    ],
                    [
                        'label' => 'Brændstof',
                        'class' => 'border-primary-low border-x border-b rounded-b',
                        'value' => fn ($col) => $col->powertrain->engine->fuel_type,
                    ],
                ],
            ],
        ];
    }
}