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
                                'FieldUid' => '32a9ce20-4ebd-4c9d-89c4-085077a1d162_6a5e47cd-38aa-4764-bb99-16269e6ebe5b_NA_NA',
                                'QueryOperator' => 0,
                                'FilterValue' => '5637150586',
                            ],
                            [
                                'FieldUid' => '843467ee-dfbd-4155-a6f3-647de7ce7367_NA_NA',
                                'QueryOperator' => 1,
                                'FilterValue' => '',
                            ],
                            [
                                'FieldUid' => 'c64b73de-fefa-4e95-92a3-34533f97867e_NA_NA',
                                'QueryOperator' => 1,
                                'FilterValue' => '',
                            ]
                        ],
                        'BooleanOperator' => 0,
                        'QueryModelType' => 'SimpleQueryModel',
                    ],
                    [
                        'Filters' => [
                            [
                                'FieldUid' => 'PIM_ProductStructureUid',
                                'QueryOperator' => 0,
                                'FilterValue' => $structureId,
                            ],
                            [
                                'FieldUid' => '32a9ce20-4ebd-4c9d-89c4-085077a1d162_6a5e47cd-38aa-4764-bb99-16269e6ebe5b_NA_NA',
                                'QueryOperator' => 0,
                                'FilterValue' => '5637155076',
                            ],
                            [
                                'FieldUid' => '843467ee-dfbd-4155-a6f3-647de7ce7367_NA_NA',
                                'QueryOperator' => 1,
                                'FilterValue' => '',
                            ],
                            [
                                'FieldUid' => 'c64b73de-fefa-4e95-92a3-34533f97867e_NA_NA',
                                'QueryOperator' => 1,
                                'FilterValue' => '',
                            ]
                        ],
                        'BooleanOperator' => 0,
                        'QueryModelType' => 'SimpleQueryModel',
                    ],
                ],
                'BooleanOperator' => 1, 
                'QueryModelType' => 'BooleanQueryModel',
            ],
        ];
    }


    public function getSparePartId(string $structureId, string $partNumber): ?int
    {

        $response = Http::nellemannPIM()
            ->post('products/search', $this->getSparePartsSearchQuery($structureId, $partNumber));

        if ($response->failed()) {
            throw new \Exception("PIM request failed [{$response->status()}]");
        }

        return $response->json()[0] ?? null;
    }

    protected function getSparePartsSearchQuery(string $structureId, string $partNumber): array
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
                                'FieldUid' => 'f7c905b8-d5bc-40a2-b0ea-19f37e2b8a75_NA_NA',
                                'QueryOperator' => 0,
                                'FilterValue' => $partNumber,
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
