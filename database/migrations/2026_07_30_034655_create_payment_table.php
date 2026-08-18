<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->string('payment_method'); // cash, aba, visa, mastercard
            $table->decimal('amount', 12, 2); // ទឹកប្រាក់ដែលអតិថិជនបានប្រគល់ឲ្យ
            $table->decimal('change_amount', 12, 2)->default(0.00); // ប្រាក់អាប់
            $table->string('status')->default('success'); // success, pending, failed
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};