<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class AccessoryPrice extends Model
{
    protected $fillable = [
        'accesssory_id',
        'price',
        'price_ex_vat',
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
     * @return BelongsTo<Accessory, $this>
     */
    public function accessory(): BelongsTo
    {
        return $this->belongsTo(Accessory::class);
    }
}