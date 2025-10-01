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
        Schema::create('cash_movements', function (Blueprint $table) {
            $table->id();
            $table->morphs('source');                 // e.g. payments:id
            $table->enum('direction', ['in','out']);  // mirror of payment direction
            $table->foreignId('currency_id')->constrained('currencies');
            $table->decimal('amount', 18, 4);
            $table->dateTime('occurred_at')->useCurrent();
            $table->decimal('balance_after', 18, 4)->nullable();
            $table->text('note')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['currency_id','direction','occurred_at']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_movements');
    }
};
