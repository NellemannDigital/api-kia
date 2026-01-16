<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

       /**
     * @return BelongsTo<Color, $this>
     */
    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }
}