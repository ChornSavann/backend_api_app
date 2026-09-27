<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->decimal('store_lat', 10, 8)->nullable();
            $table->decimal('store_lng', 11, 8)->nullable();
            $table->decimal('customer_lat', 10, 8)->nullable();
            $table->decimal('customer_lng', 11, 8)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropColumn(['store_lat', 'store_lng', 'customer_lat', 'customer_lng']);
        });
    }
};