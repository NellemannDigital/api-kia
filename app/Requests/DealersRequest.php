<?php

namespace App\Requests;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class DealersRequest
{
    public function getDealers(): Collection
    {
        $response = Http::nellemannDynamics()
            ->timeout(300) // 5 minutes
            ->get("pin_forhandlers?\$filter=_pin_bilmaerke_value eq 'fc6d8b38-52e0-ed11-a7c6-6045bd886baf' and statuscode eq 892210001 and pin_aktivweb eq true and pin_dealerguid ne null&\$expand=pin_pin_forhandlerpostnumre_Forhandler_pin_fo,nel_pin_forhandlerpostnumre_ForhandlerErhverv_pin_forhandler,nel_accountid(\$select=accountnumber)");

        if ($response->failed()) {
            throw new \Exception("Dynamics request failed [{$response->status()}]");
        }

        $responseData = $response->json('value', []);

        return collect($responseData);
    }
}
