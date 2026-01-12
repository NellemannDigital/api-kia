<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeasingPrice extends Model
{
    protected $fillable = [
        'powertrain_id',
        'down_payment',
        'mp_10000',
        'mp_15000',
        'mp_20000',
        'mp_25000',
        'mp_30000',
        'mp_35000',
        'mp_40000',
        'valid_from',
        'valid_to',
    ];

    protected static function booted()
    {
        static::addGlobalScope('valid', function (Builder $builder) {
            $today = Carbon::today()->toDateString();

            $builder->where(function ($q) use ($today) {
                $q->whereNull('valid_from')->orWhere('valid_from', '<=', $today);
            })->where(function ($q) use ($today) {
                $q->whereNull('valid_to')->orWhere('valid_to', '>=', $today);
            });
        });
    }

    /**
     * @return BelongsTo<Powertrain, $this>
     */
    public function powertrain(): BelongsTo
    {
        return $this->belongsTo(Powertrain::class);
    }
}
