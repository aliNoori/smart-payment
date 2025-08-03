<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Creates the 'transactions' table to store payment transaction details
     * associated with orders and gateways.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id(); // Primary key

            // Foreign key to orders table (cascade on delete to clean up related transactions)
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();

            $table->string('gateway'); // Payment gateway name (e.g. 'zarinpal', 'idpay')
            $table->unsignedBigInteger('amount'); // Transaction amount

            $table->string('authority')->nullable(); // Gateway-specific authority/token
            $table->string('ref_id')->nullable(); // Reference ID returned after payment
            $table->string('card_pan')->nullable(); // Masked card number (e.g. **** **** **** 1234)
            $table->string('card_hash')->nullable(); // Hashed card identifier for security

            // Transaction status: pending, paid, failed
            $table->string('status')->default('pending');

            $table->timestamp('paid_at')->nullable(); // Timestamp when payment was completed

            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * Drops the 'transactions' table.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
