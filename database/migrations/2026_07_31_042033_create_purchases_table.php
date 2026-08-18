<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_number')->unique(); // លេខកូដវិក្កយបត្រទិញចូល (Purchase Invoice Number)
            $table->unsignedBigInteger('supplier_id')->nullable(); // អ្នកផ្គត់ផ្គង់ (Supplier)
            $table->unsignedBigInteger('user_id'); // អ្នកគិតលុយ ឬបុគ្គលិកដែលបានទិញ
            $table->decimal('subtotal', 10, 2); // តម្លៃសរុបមុនការបញ្ចុះតម្លៃ
            $table->decimal('discount', 10, 2)->default(0.00); // បញ្ចុះតម្លៃ
            $table->decimal('tax', 10, 2)->default(0.00); // ពន្ធ
            $table->decimal('total', 10, 2); // តម្លៃសរុបចុងក្រោយ
            $table->string('payment_method')->default('cash'); // ប្រភេទនៃការទូទាត់ (Cash, Bank, etc.)
            $table->string('status')->default('completed'); // ស្ថានភាព (Completed, Pending, Cancelled)
            $table->text('notes')->nullable(); // မှတ်ချက်ផ្សេងៗ
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};