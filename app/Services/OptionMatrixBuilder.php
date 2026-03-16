<?php

namespace App\Services;

use Illuminate\Support\Collection;

class OptionMatrixBuilder
{
    public static function build(
        Collection $trims, 
        string $relation, 
        string $optionIdentifier = 'id', 
        string $priceRelation = 'prices', 
        string $priceField = 'suggested_retail_price'
    ): Collection {
        $flatOptions = $trims->flatMap(function ($trim) use ($relation, $optionIdentifier, $priceRelation, $priceField) {
            if (!isset($trim->$relation)) {
                return collect();
            }

            return $trim->$relation->map(function ($option) use ($trim, $optionIdentifier, $priceRelation, $priceField) {
                $lastPrice = $option->$priceRelation->last()?->$priceField;

                return [
                    'option_id' => $option->$optionIdentifier,
                    'option_obj' => $option,
                    'trim_id'   => $trim->id,
                    'price'     => $lastPrice,
                ];
            });
        });

        $grouped = $flatOptions->groupBy('option_id');

        return $grouped->map(function ($rows) use ($trims, $relation) {
            $prices = [];

            foreach ($trims as $trim) {
                $prices[$trim->id] = $rows->firstWhere('trim_id', $trim->id)['price'] ?? null;
            }

            return [
                $relation => $rows->first()['option_obj'], 
                'prices' => $prices,
            ];
        })->values();
    }
}