<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'api_name',
        'endpoint',
        'method',
        'request_data',
        'response_data',
        'status_code',
        'error_type',
        'error_message',
        'order_id',
    ];

    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
