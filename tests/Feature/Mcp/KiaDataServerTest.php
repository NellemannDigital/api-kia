<?php

use App\Mcp\Servers\KiaDataServer;
use App\Mcp\Tools\GetKiaDataRecordTool;
use App\Mcp\Tools\SearchKiaDataTool;
use App\Models\ComplianceTextTemplate;
use App\Models\Dealer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;

uses(RefreshDatabase::class);

test('it searches configured kia data sources', function () {
    Dealer::create([
        'dynamics_id' => 'dyn-odense',
        'account_number' => '10001',
        'dealer_guid' => 'dealer-odense-guid',
        'name' => 'Kia Odense',
        'display_name' => 'Kia Odense C',
        'city' => 'Odense',
        'zip_code' => 5000,
        'country' => 'DK',
        'phone' => '+45 12 34 56 78',
    ]);

    ComplianceTextTemplate::create([
        'variant' => 'private-leasing',
        'template' => 'Compliance text for private leasing.',
        'version' => '1',
        'valid_from' => now()->toDateString(),
        'valid_to' => null,
        'show_in_generator' => true,
    ]);

    $response = KiaDataServer::tool(SearchKiaDataTool::class, [
        'query' => 'Odense',
        'entities' => ['dealers'],
        'limit' => 5,
    ]);

    $response
        ->assertOk()
        ->assertSee('Kia Odense')
        ->assertStructuredContent(fn (AssertableJson $json) => $json
            ->where('query', 'Odense')
            ->where('count', 1)
            ->where('results.0.entity', 'dealers')
            ->where('results.0.title', 'Kia Odense C')
            ->etc());
});

test('it fetches a full kia data record by entity and id', function () {
    $dealer = Dealer::create([
        'dynamics_id' => 'dyn-aarhus',
        'account_number' => '10002',
        'dealer_guid' => 'dealer-aarhus-guid',
        'name' => 'Kia Aarhus',
        'display_name' => 'Kia Aarhus Syd',
        'city' => 'Aarhus',
        'zip_code' => 8000,
        'country' => 'DK',
        'phone' => '+45 87 65 43 21',
    ]);

    $response = KiaDataServer::tool(GetKiaDataRecordTool::class, [
        'entity' => 'dealers',
        'id' => (string) $dealer->id,
    ]);

    $response
        ->assertOk()
        ->assertSee('dealer-aarhus-guid')
        ->assertStructuredContent(fn (AssertableJson $json) => $json
            ->where('entity', 'dealers')
            ->where('record._mcp.title', 'Kia Aarhus Syd')
            ->where('record.data.name', 'Kia Aarhus')
            ->etc());
});
