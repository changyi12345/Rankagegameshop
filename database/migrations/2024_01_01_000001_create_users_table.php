<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('phone')->unique();
            $table->string('email')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->decimal('balance', 15, 2)->default(0);
            $table->string('telegram_id')->nullable()->unique();
            $table->string('telegram_username')->nullable();
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_blocked')->default(false);
            $table->string('otp_code')->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            
            $table->index('phone');
            $table->index('telegram_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
