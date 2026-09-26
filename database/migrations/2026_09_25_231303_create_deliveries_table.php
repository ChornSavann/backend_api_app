<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id'); // 🔗 តភ្ជាប់ទៅកាន់ Table orders
            $table->text('pickup_address');         // 📍 ទីតាំងហាង
            $table->text('delivery_address');       // 🏠 អាសយដ្ឋានអ្នកទទួល
            $table->decimal('delivery_fee', 12, 2)->default(0.00); // 💵 ថ្លៃសេវាដឹកជញ្ជូន
            $table->string('delivery_partner');     // 🛵 ក្រុមហ៊ុនដឹកជញ្ជូន (Instant Delivery, Same Day...)
            $table->string('receiver_name');        // 👤 ឈ្មោះអ្នកទទួល
            $table->string('receiver_phone');       // ☎️ លេខទូរស័ព្ទអ្នកទទួល
            $table->text('note')->nullable();       // 📝 កំណត់សម្គាល់បន្ថែម
            $table->string('status')->default('pending'); // 🔄 ស្ថានភាពដឹកជញ្ជូន (pending, shipping, delivered, cancelled)
            $table->timestamps();

            // 🔗 Foreign Key Constraint (ប្រសិនបើ Table orders ប្រើ id ធម្មតា)
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};