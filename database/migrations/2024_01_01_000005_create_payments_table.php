<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('method', ['wallet', 'wavepay', 'kpay', 'manual']);
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('screenshot')->nullable(); // For manual payments
            $table->text('rejection_reason')->nullable();
            $table->string('transaction_id')->nullable(); // For payment gateways
            $table->json('gateway_response')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            
            $table->index('order_id');
            $table->index('user_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
