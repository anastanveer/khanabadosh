<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('discount_type')->nullable()->after('compare_at_price');
            $table->decimal('discount_value', 10, 2)->nullable()->after('discount_type');
            $table->timestamp('discount_starts_at')->nullable()->after('discount_value');
            $table->timestamp('discount_ends_at')->nullable()->after('discount_starts_at');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'discount_type',
                'discount_value',
                'discount_starts_at',
                'discount_ends_at',
            ]);
        });
    }
};
