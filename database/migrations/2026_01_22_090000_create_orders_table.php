<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 40)->unique();
            $table->string('status', 30)->default('pending');
            $table->string('customer_name', 120);
            $table->string('email', 120);
            $table->string('phone', 40);
            $table->string('city', 80);
            $table->string('address', 200);
            $table->string('postal_code', 20)->nullable();
            $table->string('delivery_method', 40);
            $table->string('payment_method', 40);
            $table->json('payment_details')->nullable();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->string('currency', 10)->default('PKR');
            $table->unsignedInteger('items_count')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
