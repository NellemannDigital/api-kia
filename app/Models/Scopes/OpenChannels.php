<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class OpenChannels implements Scope
{
    protected array $channels;

    public function __construct(array $channels = ['channels->master_channel'])
    {
        $this->channels = $channels;
    }

    public function apply(Builder $builder, Model $model)
    {
        $today = Carbon::parse(now())->toDateString();

        $builder->where(function ($q) use ($today) {
            foreach ($this->channels as $channel) {
                $this->applyChannelCondition($q, $channel, $today);
            }
        });
    }

    private function applyChannelCondition(Builder $query, string $channel, string $today)
    {
        $query->where(function ($subQuery) use ($channel, $today) {
            $subQuery->whereNull("{$channel}->open_to")
                     ->orWhere("{$channel}->open_to", '>=', $today);
        })
        ->where("{$channel}->open_from", '<=', $today);
    }
}
