<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class OpenChannels implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $today = now()->toDateString();
        $channels = $model->getActiveChannels();

        $builder->where(function ($q) use ($channels, $today) {
            foreach ($channels as $channel) {
                $q->where(function ($subQuery) use ($channel, $today) {
                    $subQuery
                        ->where("{$channel}->open_from", '<=', $today)
                        ->where(function ($dateQuery) use ($channel, $today) {
                            $dateQuery->whereNull("{$channel}->open_to")
                                      ->orWhere("{$channel}->open_to", '>=', $today);
                        });
                });
            }
        });
    }
}