<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('user_id');
            $table->string('type'); // 'in' (ទិញចូល) ឬ 'out' (លក់ចេញ)
            $table->decimal('quantity', 10, 3); // ចំនួនដែលចូល ឬចេញ
            $table->string('reference_no')->nullable(); // លេខ Order ឬ Reference
            $table->text('note')->nullable();
            $table->timestamps();

            // Foreign Keys
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};