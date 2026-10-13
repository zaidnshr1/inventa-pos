<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sales_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 60)->unique();
            $table->foreignId('outlet_id')->constrained('outlets')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('customer_name', 125)->default('umum');
            $table->string('customer_phone', 20)->nullable();

            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);

            $table->string('payment_method')->default('CASH');
            $table->string('payment_status')->default('PAID');
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('change_amount', 15, 2)->default(0);

            $table->text('note')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('sales_transactions_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_transaction_id')->constrained('sales_transactions')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();

            $table->decimal('unit_price', 15, 2);
            $table->decimal('cost_price', 15, 2);
            $table->integer('quantity');
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_transactions_items');
        Schema::dropIfExists('sales_transactions');
    }
};
