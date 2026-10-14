<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('service_types', function (Blueprint $table) {
            $table->id();
            $table->string('category')->nullable(); // ឧ. Inspection & Repair
            $table->string('name'); // ឧ. Engine Malfunction
            $table->decimal('duration_hours', 8, 2); // ឧ. 4.5 (ម៉ោងប៉ាន់ស្មាន)
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_types');
    }
};
