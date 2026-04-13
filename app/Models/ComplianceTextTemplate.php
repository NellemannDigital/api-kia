<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplianceTextTemplate extends Model
{
    protected $fillable = [
        'variant',
        'template',
        'version',
        'valid_from',
        'valid_to',
        'show_in_generator'
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_to' => 'date'
    ];

    public function scopeValid($query)
    {
        return $query
            ->where(function ($q) {
                $q->whereNull('valid_from')
                  ->orWhere('valid_from', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('valid_to')
                  ->orWhere('valid_to', '>=', now());
            });
    }
}
