<?php

use App\Mappers\DealerMapper;

it('maps special opening hours from dynamics dealer data', function () {
    $dealer = [
        'pin_forhandlerid' => 'dealer-dynamics-id',
        'nel_accountid' => [
            'accountnumber' => '12345',
        ],
        'pin_navn' => 'Kia Test Dealer',
        'pin_pin_forhandlereabnelukkedage_Forhandler_p' => [
            [
                'pin_dato' => '2026-07-10T22:00:00Z',
                'pin_abningstidspunkt' => '0',
                'pin_lukketidspunkt' => null,
                'pin_dealerclosed' => true,
                'pin_specialdayname' => 'Dealer closed day',
            ],
            [
                'pin_dato' => '2026-07-11T22:00:00Z',
                'pin_abningstidspunkt' => '08.00',
                'pin_lukketidspunkt' => '16.00',
                'pin_dealerclosed' => false,
                'pin_specialdayname' => 'Dealer special hours',
            ],
            [
                'pin_dato' => null,
                'pin_abningstidspunkt' => null,
                'pin_lukketidspunkt' => null,
                'pin_dealerclosed' => null,
            ],
        ],
        'general_special_opening_hours' => [
            [
                'pin_dato' => '2026-07-09T22:00:00Z',
                'pin_abningstidspunkt' => '10.00',
                'pin_lukketidspunkt' => '14.00',
                'pin_dealersclosed' => false,
                'pin_specialdayname' => 'Skaertorsdag',
            ],
        ],
    ];

    $dealerData = DealerMapper::map($dealer);

    expect($dealerData->special_opening_hours)->toHaveCount(3)
        ->and($dealerData->special_opening_hours[0]->toArray())->toBe([
            'date' => '2026-07-10',
            'opening_time' => '0',
            'closing_time' => null,
            'closed' => true,
            'display_name' => 'Dealer closed day',
        ])
        ->and($dealerData->special_opening_hours[1]->toArray())->toBe([
            'date' => '2026-07-11',
            'opening_time' => '08.00',
            'closing_time' => '16.00',
            'closed' => false,
            'display_name' => 'Dealer special hours',
        ])
        ->and($dealerData->special_opening_hours[2]->toArray())->toBe([
            'date' => '2026-07-09',
            'opening_time' => '10.00',
            'closing_time' => '14.00',
            'closed' => false,
            'display_name' => 'Skaertorsdag',
        ]);
});
