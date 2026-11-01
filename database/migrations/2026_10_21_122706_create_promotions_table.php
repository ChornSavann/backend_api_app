<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('promotion_code')->unique();
            $table->integer('quantity')->nullable();

            $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage'); 
            $table->decimal('discount_value', 10, 2);
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();

            $table->boolean('is_active')->default(true); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_promotions');
    }
};
