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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->foreignId('unit_id')->constrained('units');
            $table->foreignId('currency_id')->constrained('currencies');
            $table->string('code')->nullable()->index();
            $table->decimal('quantity', 18, 3)->default(0);
            $table->decimal('default_buy_price',  18, 4)->nullable();
            $table->decimal('default_sell_price', 18, 4)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
