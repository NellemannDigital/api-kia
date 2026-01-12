<?php

namespace App\Requests;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class ProductsSearchRequest extends PimRequest
{
    public function getProductIds(string $structureId): Collection
    {
       $response = Http::nellemannPIM()
            ->post('products/search', $this->getProductIdsSearchQuery($structureId));

        if ($response->failed()) {
            throw new \Exception("PIM request failed [{$response->status()}]");
        }

        $responseData = $response->json();

        return collect($responseData);
    }

    protected function getProductIdsSearchQuery(string $structureId): array
    {
        return [
            'IncludeArchived' => true,
            'QueryModel' => [
                'SubQueries' => [
                    [
                        'Filters' => [
                            [
                                'FieldUid' => 'PIM_ProductStructureUid',
                                'QueryOperator' => 0,
                                'FilterValue' => $structureId,
                            ]
                        ],
                        'BooleanOperator' => 0,
                        'QueryModelType' => 'SimpleQueryModel',
                    ],
                ],
                'BooleanOperator' => 0,
                'QueryModelType' => 'BooleanQueryModel',
            ],
        ];
    }

    public function getAccessoryIds(string $structureId): Collection
    {
       $response = Http::nellemannPIM()
            ->post('products/search', $this->getAccessoryIdsSearchQuery($structureId));

        if ($response->failed()) {
            throw new \Exception("PIM request failed [{$response->status()}]");
        }

        $responseData = $response->json();

        return collect($responseData);
    }

    protected function getAccessoryIdsSearchQuery(string $structureId): array
    {
        return [
            'IncludeArchived' => true,
            'QueryModel' => [
                'SubQueries' => [
                    [
                        'Filters' => [
                            [
                                'FieldUid' => 'PIM_ProductStructureUid',
                                'QueryOperator' => 0,
                                'FilterValue' => $structureId,
                            ],
                            [
                                'FieldUid' => 'CategoryNEW.Id_NA_NA',
                                'QueryOperator' => 0,
                                'FilterValue' => '5637150586',
                            ],
                            [
                                'FieldUid' => 'KiaAccessoriesModels_NA_NA',
                                'QueryOperator' => 1,
                                'FilterValue' => '',
                            ],
                            [
                                'FieldUid' => 'SharedAccessoryRetailPrice_NA_NA',
                                'QueryOperator' => 1,
                                'FilterValue' => '',
                            ]
                        ],
                        'BooleanOperator' => 0,
                        'QueryModelType' => 'SimpleQueryModel',
                    ],
                ],
                'BooleanOperator' => 0,
                'QueryModelType' => 'BooleanQueryModel',
            ],
        ];
    }
}
