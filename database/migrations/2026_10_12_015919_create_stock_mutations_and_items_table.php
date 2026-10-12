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
        Schema::create('stock_mutations', function (Blueprint $table) {
            $table->id();
            $table->string('mutation_number', 50)->unique();
            $table->string('type', 30)->default('TRANSFER');
            $table->foreignId('source_outlet_id')->nullable()->constrained('outlets')->nullOnDelete();
            $table->foreignId('target_outlet_id')->nullable()->constrained('outlets')->nullOnDelete();
            $table->string('status', 25)->default('PENDING');
            $table->text('note')->nullable();
            $table->foreignId('requested_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('stock_mutations_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_mutation_id')->constrained('stock_mutations')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->strictOnDelete();
            $table->integer('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_mutations_items');
        Schema::dropIfExists('stock_mutations');
    }
};
