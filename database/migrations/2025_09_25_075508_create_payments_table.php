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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->morphs('payable');
            $table->enum('direction', ['in', 'out']);
            $table->foreignId('currency_id')->constrained('currencies');
            $table->decimal('amount', 18, 4);
            $table->date('date');
            $table->text('note')->nullable();
            $table->index(['payable_type', 'payable_id', 'currency_id', 'direction']);
            $table->index('date');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
