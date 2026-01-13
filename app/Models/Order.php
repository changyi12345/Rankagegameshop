<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'game_id',
        'package_id',
        'user_game_id',
        'server_id',
        'amount',
        'status',
        'payment_method',
        'api_response',
        'error_message',
        'retry_count',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'api_response' => 'array',
        'processed_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    // Helper Methods
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (!$order->order_id) {
                $order->order_id = 'RK' . strtoupper(Str::random(8)) . time();
            }
        });
    }

    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'processed_at' => now(),
        ]);
    }

    public function markAsFailed($errorMessage = null)
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
        ]);
    }

    public function incrementRetry()
    {
        $this->increment('retry_count');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}
