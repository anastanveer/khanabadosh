<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shopify_id')->unique();
            $table->string('title');
            $table->string('handle')->unique();
            $table->longText('body_html')->nullable();
            $table->string('product_type')->nullable();
            $table->string('vendor')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('compare_at_price', 10, 2)->nullable();
            $table->boolean('available')->default(true);
            $table->timestampTz('source_created_at')->nullable();
            $table->timestampTz('source_updated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
