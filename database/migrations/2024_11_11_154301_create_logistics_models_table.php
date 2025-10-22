<?php

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
        Schema::create('logistics', function (Blueprint $table) {
            $table->id();
            $table->string('model_name_id', 70);
            $table->string('exterior_color', 50)->nullable();
            $table->string('interior_color', 50)->nullable();
            $table->string('model_year', 10);
            $table->string('vin_number', 40);
            $table->string('engine_number', 40);
            $table->text('remark')->nullable();
            $table->integer('is_sale')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logistics');
    }
};
