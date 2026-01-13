<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'recipient_type',
        'recipient_id',
        'title',
        'message',
        'status',
        'is_read',
        'channel',
        'metadata',
        'sent_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'sent_at' => 'datetime',
        'is_read' => 'boolean',
    ];

    // Relationships
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    // Helper Methods
    public function markAsSent()
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    public function markAsFailed()
    {
        $this->update([
            'status' => 'failed',
        ]);
    }
}
