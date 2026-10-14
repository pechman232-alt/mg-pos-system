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
        Schema::create('branch_capacities', function (Blueprint $table) {
            $table->id();
            $table->string('branch_name');
            $table->integer('branch_id')->nullable();
            $table->integer('tech_qty'); // ឧ. HQ = 15 នាក់
            $table->decimal('daily_hours_per_tech', 8, 2)->default(8.0); // ៨ ម៉ោង/នាក់
            $table->decimal('efficiency_rate', 8, 2)->default(0.9); // 90%
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_capacities');
    }
};
