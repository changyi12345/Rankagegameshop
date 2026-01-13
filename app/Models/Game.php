<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'icon',
        'currency_name',
        'requires_server',
        'profit_margin',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'requires_server' => 'boolean',
        'is_active' => 'boolean',
        'profit_margin' => 'decimal:2',
    ];

    // Relationships
    public function packages()
    {
        return $this->hasMany(Package::class)->where('is_active', true)->orderBy('sort_order');
    }

    public function allPackages()
    {
        return $this->hasMany(Package::class)->orderBy('sort_order');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Helper Methods
    public function getMinPriceAttribute()
    {
        return $this->packages()->min('price') ?? 0;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
