<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplianceTextTemplate extends Model
{
    protected $fillable = [
        'scope',
        'scope_id',
        'variant',
        'template',
        'version',
        'valid_from',
        'valid_to',
        'active',
    ];

    public function scopeActive($query)
    {
        return $query
            ->where('active', true)
            ->where(function ($q) {
                $q->whereNull('valid_from')->orWhere('valid_from', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('valid_to')->orWhere('valid_to', '>=', now());
            });
    }
}
