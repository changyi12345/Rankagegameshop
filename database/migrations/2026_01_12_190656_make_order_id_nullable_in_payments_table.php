<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['order_id']);
            
            // Make order_id nullable
            $table->unsignedBigInteger('order_id')->nullable()->change();
            
            // Re-add the foreign key constraint with nullable
            $table->foreign('order_id')
                  ->references('id')
                  ->on('orders')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['order_id']);
            
            // Make order_id not nullable again
            $table->unsignedBigInteger('order_id')->nullable(false)->change();
            
            // Re-add the foreign key constraint
            $table->foreign('order_id')
                  ->references('id')
                  ->on('orders')
                  ->onDelete('cascade');
        });
    }
};
