<?php

use App\Models\Car;
use App\Models\Dealer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('marks dates outside the car test drive booking window as closed', function () {
    Sanctum::actingAs(User::factory()->create());

    $dealer = Dealer::create([
        'dynamics_id' => 'dealer-1',
        'account_number' => 'account-1',
        'name' => 'Kia Test Dealer',
        'tools' => ['test_drive' => true],
        'types' => ['b2c' => true],
        'opening_hours' => [
            'sales' => [
                'monday' => '09.00-12.00',
                'tuesday' => '09.00-12.00',
                'wednesday' => '09.00-12.00',
                'thursday' => '09.00-12.00',
                'friday' => '09.00-12.00',
                'saturday' => '09.00-12.00',
                'sunday' => '09.00-12.00',
            ],
        ],
    ]);

    $car = Car::create([
        'struct_id' => 123,
        'web_id' => 'ev3',
        'name' => 'EV3',
        'channels' => [
            'test_drive_channel' => [
                'booking_start' => '2026-06-10',
                'booking_end' => '2026-06-20',
            ],
        ],
    ]);

    $response = $this->getJson(
        "/api/test-drive/dealers/{$dealer->id}/calendar-availability?month=2026-06-01&car_id={$car->id}"
    );

    $response
        ->assertOk()
        ->assertJsonPath('2026-06-09.status', 'closed')
        ->assertJsonPath('2026-06-09.available', 0)
        ->assertJsonPath('2026-06-10.status', 'available')
        ->assertJsonPath('2026-06-10.available', 3)
        ->assertJsonPath('2026-06-20.status', 'available')
        ->assertJsonPath('2026-06-20.available', 3)
        ->assertJsonPath('2026-06-21.status', 'closed')
        ->assertJsonPath('2026-06-21.available', 0);
});
