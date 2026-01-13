<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'name',
        'currency_amount',
        'price',
        'bonus',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'currency_amount' => 'integer',
        'bonus' => 'integer',
        'price' => 'decimal:2',
    ];

    // Relationships
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Helper Methods
    public function getTotalCurrencyAttribute()
    {
        return $this->currency_amount + $this->bonus;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
