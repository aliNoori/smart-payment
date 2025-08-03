<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Creates the 'orders' table to store payment-related order data.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id(); // Primary key

            // Foreign key to users table (nullable, sets to null on user deletion)
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->unsignedBigInteger('amount'); // Total amount of the order
            $table->string('currency')->default('IRR'); // Currency code (default: Iranian Rial)

            // Order status: pending, paid, failed, canceled
            $table->string('status')->default('pending');

            $table->string('description')->nullable(); // Optional description or notes

            $table->timestamps(); // Created_at and updated_at timestamps
        });
    }

    /**
     * Reverse the migrations.
     *
     * Drops the 'orders' table.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
