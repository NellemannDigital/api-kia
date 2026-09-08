<?php

use App\Mcp\Servers\KiaDataServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::local('kia-data', KiaDataServer::class);

Mcp::web('/mcp/kia-data', KiaDataServer::class)
    ->middleware(['auth:sanctum']);
