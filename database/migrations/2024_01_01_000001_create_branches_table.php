<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    // បន្ថែមលក្ខខណ្ឌនេះ៖ បើគ្មាន Table ឈ្មោះ branches ទេ ទើបបង្កើត
    if (!Schema::hasTable('branches')) {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('branch_name', 40);
            $table->timestamps();
        });
    }
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
