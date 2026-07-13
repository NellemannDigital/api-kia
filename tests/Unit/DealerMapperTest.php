<?php

use App\Mappers\DealerMapper;

it('maps special opening hours from dynamics dealer data', function () {
    $dealer = [
        'pin_forhandlerid' => 'dealer-dynamics-id',
        'nel_accountid' => [
            'accountnumber' => '12345',
        ],
        'pin_navn' => 'Kia Test Dealer',
        'pin_openinghoursmonday' => '09:00-17:30',
        'pin_workshopopeninghoursmonday' => '08:00 - 16:00',
        'pin_pin_forhandlereabnelukkedage_Forhandler_p' => [
            [
                'pin_dato' => '2026-07-15T22:00:00Z',
                'pin_abningstidspunkt' => '0',
                'pin_lukketidspunkt' => null,
                'pin_dealerclosed' => true,
                'pin_specialdayname' => 'Dealer closed day',
            ],
            [
                'pin_dato' => '2026-07-16T22:00:00Z',
                'pin_abningstidspunkt' => '08:00',
                'pin_lukketidspunkt' => '16:00',
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
                'pin_dato' => '2026-07-14T22:00:00Z',
                'pin_abningstidspunkt' => '10:00',
                'pin_lukketidspunkt' => '14:00',
                'pin_dealersclosed' => false,
                'pin_specialdayname' => 'Skaertorsdag',
            ],
        ],
    ];

    $dealerData = DealerMapper::map($dealer);

    expect($dealerData->special_opening_hours)->toHaveCount(3)
        ->and($dealerData->opening_hours->sales->monday)->toBe('09.00-17.30')
        ->and($dealerData->opening_hours->workshop->monday)->toBe('08.00-16.00')
        ->and($dealerData->special_opening_hours[0]->date)->toBe('2026-07-16')
        ->and($dealerData->special_opening_hours[0]->opening_time)->toBe('0')
        ->and($dealerData->special_opening_hours[0]->closing_time)->toBeNull()
        ->and($dealerData->special_opening_hours[0]->closed)->toBeTrue()
        ->and($dealerData->special_opening_hours[0]->display_name)->toBe('Dealer closed day')
        ->and($dealerData->special_opening_hours[1]->date)->toBe('2026-07-17')
        ->and($dealerData->special_opening_hours[1]->opening_time)->toBe('08.00')
        ->and($dealerData->special_opening_hours[1]->closing_time)->toBe('16.00')
        ->and($dealerData->special_opening_hours[1]->closed)->toBeFalse()
        ->and($dealerData->special_opening_hours[1]->display_name)->toBe('Dealer special hours')
        ->and($dealerData->special_opening_hours[2]->date)->toBe('2026-07-15')
        ->and($dealerData->special_opening_hours[2]->opening_time)->toBe('10.00')
        ->and($dealerData->special_opening_hours[2]->closing_time)->toBe('14.00')
        ->and($dealerData->special_opening_hours[2]->closed)->toBeFalse()
        ->and($dealerData->special_opening_hours[2]->display_name)->toBe('Skaertorsdag');
});
