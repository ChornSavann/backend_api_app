<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // public function up(): void
    // {
    //     Schema::create('products', function (Blueprint $table) {
    //         $table->id();
    //         // បង្កើត Foreign Key តភ្ជាប់ទៅកាន់ Table categories
    //         $table->foreignId('category_id')->constrained()->onDelete('cascade');
    //         $table->string('name');
    //         $table->string('sku')->unique()->nullable(); // លេខកូដទំនិញ
    //         $table->text('description')->nullable();
    //         $table->decimal('price', 10, 2)->default(0.00); // តម្លៃទំនិញ
    //         $table->integer('stock')->default(0); // ចំនួនក្នុងស្តុក
    //         $table->string('image_url')->nullable(); // រូបភាពទំនិញ
    //         $table->timestamps();
    //     });
    // }
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            
            // Foreign Keys (កែតម្រូវឈ្មោះតារាងទៅតាម Project របស់អ្នក ឧទាហរណ៍ categories, brands, units)
            $table->foreignId('category_id')->constrained('category')->onDelete('cascade');
            $table->foreignId('brand_id')->constrained('brands')->onDelete('cascade');
            $table->foreignId('unit_id')->constrained('units')->onDelete('cascade');
            
            $table->string('sku')->unique();
            $table->string('barcode')->unique()->nullable();
            
            $table->decimal('cost_price', 10, 2);
            $table->decimal('selling_price', 10, 2);
            $table->decimal('stock_quantity', 10, 2)->default(0);
            $table->decimal('alert_quantity', 10, 2)->default(5);
            
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
