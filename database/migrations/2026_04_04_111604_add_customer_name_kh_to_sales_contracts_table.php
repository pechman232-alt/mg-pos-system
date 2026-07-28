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
    Schema::table('sales_contracts', function (Blueprint $table) {
        // បង្កើតកូឡុំឈ្មោះខ្មែរ ដោយអនុញ្ញាតឱ្យទទេបាន (nullable)
        $table->string('customer_name_kh')->nullable()->after('customer_name');
        
        // 💡 ក្រែងលោក្នុង Database បងអត់ទាន់មាន source និង color ដែរ អាចថែមវាចូលត្រង់នេះតែម្តង
        // $table->string('source')->nullable();
        // $table->string('color')->nullable();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_contracts', function (Blueprint $table) {
            //
        });
    }
};
