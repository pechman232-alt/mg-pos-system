<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::create('products', function (Blueprint $table) {
        $table->id(); // នេះគឺជា id (bigint unsigned) ដែល receive_details ត្រូវការ
        $table->string('item_code')->unique();
        $table->string('item_name');
        $table->string('description')->nullable();
        $table->string('category')->nullable();
        $table->decimal('cost', 15, 2)->default(0);
        $table->decimal('price', 15, 2)->default(0);
        $table->decimal('stock_qty', 15, 2)->default(0);
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
