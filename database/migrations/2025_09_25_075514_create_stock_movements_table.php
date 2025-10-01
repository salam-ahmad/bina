<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $t) {
            $t->id();
            $t->foreignId('product_id')->constrained();
            $t->decimal('quantity_change', 18, 3); // +in, -out
            $t->morphs('source'); // OrderItem|PurchaseItem|Adjustment
            $t->date('date')->useCurrent();
            $t->text('note')->nullable();
            $t->timestamps();
            $t->index(['product_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
