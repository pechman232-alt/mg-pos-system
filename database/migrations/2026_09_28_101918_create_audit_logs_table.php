<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // Who did it
            $table->string('module'); // e.g., 'POS', 'Inventory', 'Vendors'
            $table->string('action'); // e.g., 'Create', 'Update', 'Delete'
            $table->text('description'); // e.g., 'Created Invoice INV-260910'
            $table->json('old_values')->nullable(); // For updates (what changed)
            $table->json('new_values')->nullable(); // For updates (new data)
            $table->string('ip_address')->nullable(); // Track location
            $table->timestamps();

            // Optional: link to your users table
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('audit_logs');
    }
};