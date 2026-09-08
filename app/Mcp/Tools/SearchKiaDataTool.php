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

#[Name('search-kia-data')]
#[Title('Search Kia Data')]
#[Description('Searches across Kia cars, trims, configurations, dealers, stock cars, used cars, and compliance text templates.')]
#[IsReadOnly]
#[IsIdempotent]
#[IsOpenWorld(false)]
class SearchKiaDataTool extends Tool
{
    /**
     * Handle the tool request.
     */
    public function handle(Request $request): ResponseFactory
    {
        $validated = $request->validate([
            'query' => ['required', 'string', 'min:2', 'max:160'],
            'entities' => ['sometimes', 'array'],
            'entities.*' => ['string', 'in:'.implode(',', KiaDataSources::entityNames())],
            'limit' => ['sometimes', 'integer', 'min:1', 'max:50'],
            'include_hidden' => ['sometimes', 'boolean'],
        ]);

        $search = trim($validated['query']);
        $limit = (int) ($validated['limit'] ?? 10);
        $includeHidden = (bool) ($validated['include_hidden'] ?? false);
        $entities = $validated['entities'] ?? KiaDataSources::entityNames();

        $results = collect($entities)
            ->flatMap(function (string $entity) use ($includeHidden, $limit, $search) {
                $query = KiaDataSources::queryFor($entity, $includeHidden);

                return KiaDataSources::applySearch($query, $entity, $search)
                    ->limit($limit)
                    ->get()
                    ->map(fn ($record): array => KiaDataSources::searchResult($record, $entity, $search));
            })
            ->sortByDesc('score')
            ->take($limit)
            ->values();

        return Response::structured([
            'query' => $search,
            'count' => $results->count(),
            'searched_entities' => array_values($entities),
            'include_hidden' => $includeHidden,
            'results' => $results->all(),
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
            'query' => $schema->string()
                ->description('Search text. Examples: EV3, Odense, GT-Line, a VIN, a dealer GUID, or a compliance variant.')
                ->required(),
            'entities' => $schema->array()
                ->items($schema->string()->enum(KiaDataSources::entityNames()))
                ->description('Optional list of data sources to search. Omit to search all sources.'),
            'limit' => $schema->integer()
                ->min(1)
                ->max(50)
                ->default(10)
                ->description('Maximum total number of results to return.'),
            'include_hidden' => $schema->boolean()
                ->default(false)
                ->description('When true, searches without Eloquent global scopes so hidden or unpublished catalog data can also match.'),
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
            'query' => $schema->string()->required(),
            'count' => $schema->integer()->required(),
            'searched_entities' => $schema->array()->items($schema->string())->required(),
            'include_hidden' => $schema->boolean()->required(),
            'results' => $schema->array()->items($schema->object([
                'entity' => $schema->string()->required(),
                'entity_label' => $schema->string()->required(),
                'id' => $schema->string()->required(),
                'title' => $schema->string()->required(),
                'summary' => $schema->string(),
                'matched_fields' => $schema->array()->items($schema->string())->required(),
                'score' => $schema->integer()->required(),
                'updated_at' => $schema->string()->nullable(),
                'preview' => $schema->object(),
            ]))->required(),
        ];
    }
}
