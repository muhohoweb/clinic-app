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
            $table->unsignedBigInteger('visit_id');
            $table->foreign('visit_id')->references('id')->on('visits')->onDelete('cascade');
            $table->decimal('amount', 10, 2); // Single amount field is cleaner
            $table->enum('payment_method', ['cash', 'mpesa', 'bank_transfer', 'insurance']);
            $table->string('reference_number')->nullable(); // For mpesa/bank transactions
            $table->enum('status', ['paid', 'partial', 'pending'])->default('paid');
            $table->text('notes')->nullable(); // For any additional info
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};