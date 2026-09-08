<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\GetKiaDataRecordTool;
use App\Mcp\Tools\SearchKiaDataTool;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

#[Name('Kia Data Server')]
#[Version('0.1.0')]
#[Instructions('Use this server to search and inspect Kia application data. Start with search-kia-data for broad questions, then call get-kia-data-record with the returned entity and id when you need the full database record. The tools are read-only.')]
class KiaDataServer extends Server
{
    protected array $tools = [
        SearchKiaDataTool::class,
        GetKiaDataRecordTool::class,
    ];

    protected array $resources = [
        //
    ];

    protected array $prompts = [
        //
    ];
}
