<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained('purchases')->onDelete('cascade'); // ភ្ជាប់ទៅតារាង purchases
            $table->unsignedBigInteger('product_id'); // ID ផលិតផល
            $table->string('product_name'); // ឈ្មោះផលិតផលពេលទិញចូល
            $table->decimal('unit_cost', 10, 2); // ថ្លៃដើមទិញចូលក្នុងមួយឯកតា
            $table->integer('quantity'); // ចំនួនដែលបានទិញចូល
            $table->decimal('total_price', 10, 2); // តម្លៃសរុបតាមមុខទំនិញនីមួយៗ
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
    }
};