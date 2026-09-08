<?php

namespace App\Mcp\Tools;

use App\Mcp\Support\KiaDataSources;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Title;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;
use Laravel\Mcp\Server\Tools\Annotations\IsOpenWorld;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Name('get-kia-data-record')]
#[Title('Get Kia Data Record')]
#[Description('Fetches one full Kia database record by entity name and primary key id. Use this after search-kia-data returns a relevant result.')]
#[IsReadOnly]
#[IsIdempotent]
#[IsOpenWorld(false)]
class GetKiaDataRecordTool extends Tool
{
    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response|ResponseFactory
    {
        $validated = $request->validate([
            'entity' => ['required', 'string', 'in:'.implode(',', KiaDataSources::entityNames())],
            'id' => ['required'],
            'include_hidden' => ['sometimes', 'boolean'],
        ]);

        $entity = $validated['entity'];
        $includeHidden = (bool) ($validated['include_hidden'] ?? false);
        $record = KiaDataSources::queryFor($entity, $includeHidden)
            ->whereKey($validated['id'])
            ->first();

        if (! $record) {
            return Response::error("No {$entity} record found with id [{$validated['id']}].");
        }

        return Response::structured([
            'entity' => $entity,
            'include_hidden' => $includeHidden,
            'record' => KiaDataSources::serializeRecord($record, $entity),
        ]);
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'entity' => $schema->string()
                ->enum(KiaDataSources::entityNames())
                ->description('The entity value returned from search-kia-data.')
                ->required(),
            'id' => $schema->string()
                ->description('The primary key id returned from search-kia-data.')
                ->required(),
            'include_hidden' => $schema->boolean()
                ->default(false)
                ->description('When true, fetches without Eloquent global scopes. Use the same value that was used for search-kia-data.'),
        ];
    }

    /**
     * Define the output schema for this tool's results.
     *
     * @return array<string, Type>
     */
    public function outputSchema(JsonSchema $schema): array
    {
        return [
            'entity' => $schema->string()->required(),
            'include_hidden' => $schema->boolean()->required(),
            'record' => $schema->object()->required(),
        ];
    }
}
