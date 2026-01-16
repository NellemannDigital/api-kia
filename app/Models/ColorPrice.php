<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ColorPrice extends Model
{
    protected $fillable = [
        'color_id',
        'dealer_net_price',
        'dealer_profit',
        'suggested_retail_price',
        'campaign_retail_price',
        'retail_price_ex_vat',
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
     * @return BelongsTo<Color, $this>
     */
    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }
}