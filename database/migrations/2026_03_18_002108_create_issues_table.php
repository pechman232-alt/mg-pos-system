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
        Schema::create('issues', function (Blueprint $table) {
            $table->id();
            $table->string('issue_id')->unique(); // លេខកូដសម្គាល់បញ្ហា (ឧទាហរណ៍៖ ISS-2026-001)
            $table->string('subject');            // ចំណងជើងបញ្ហា
            $table->string('category');           // IT Support, Fleet, Admin, Sales
            $table->string('department');         // ផ្នែកដែលពាក់ព័ន្ធ
            $table->text('description');          // ខ្លឹមសារលម្អិត
            $table->string('reported_by');        // ឈ្មោះអ្នករាយការណ៍
            $table->enum('priority', ['Low', 'Medium', 'High', 'Urgent'])->default('Medium');
            $table->enum('status', ['Open', 'In Progress', 'Resolved', 'Closed'])->default('Open');
            $table->text('admin_remark')->nullable(); // ចំណាំពី Admin
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issues');
    }
};
