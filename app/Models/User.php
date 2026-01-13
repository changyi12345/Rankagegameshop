<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'password',
        'balance',
        'telegram_id',
        'telegram_username',
        'is_admin',
        'is_blocked',
        'otp_code',
        'otp_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp_code',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'otp_expires_at' => 'datetime',
        'is_admin' => 'boolean',
        'is_blocked' => 'boolean',
        'balance' => 'decimal:2',
    ];

    // Relationships
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class)->orderBy('created_at', 'desc');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'recipient_id')->where('recipient_type', 'user');
    }

    // Helper Methods
    public function hasEnoughBalance($amount): bool
    {
        return $this->balance >= $amount;
    }

    public function deductBalance($amount, $description, $referenceType = null, $referenceId = null): Transaction
    {
        $this->balance -= $amount;
        $this->save();

        return Transaction::create([
            'user_id' => $this->id,
            'type' => 'debit',
            'amount' => $amount,
            'balance_after' => $this->balance,
            'description' => $description,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
        ]);
    }

    public function addBalance($amount, $description, $referenceType = null, $referenceId = null): Transaction
    {
        $this->balance += $amount;
        $this->save();

        return Transaction::create([
            'user_id' => $this->id,
            'type' => 'credit',
            'amount' => $amount,
            'balance_after' => $this->balance,
            'description' => $description,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
        ]);
    }

    public function generateOTP(): string
    {
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->update([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(config('app.otp_expiry', 5)),
        ]);
        return $otp;
    }

    public function verifyOTP($otp): bool
    {
        if ($this->otp_code === $otp && $this->otp_expires_at && $this->otp_expires_at->isFuture()) {
            $this->update([
                'otp_code' => null,
                'otp_expires_at' => null,
            ]);
            return true;
        }
        return false;
    }
}
