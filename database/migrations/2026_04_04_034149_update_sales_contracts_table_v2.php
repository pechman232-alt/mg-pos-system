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
        // 🌟 ១. ឆែកមើល បើមាន Column ចាស់ ត្រូវដូរឈ្មោះឱ្យត្រូវ Model
        if (Schema::hasColumn('sales_contracts', 'source')) {
            $table->renameColumn('source', 'source_id');
        }
        if (Schema::hasColumn('sales_contracts', 'location_province')) {
            $table->renameColumn('location_province', 'location');
        }

        // 🌟 ២. បន្ថែម Column 'offers' ជា JSON បើមិនទាន់មាន
        if (!Schema::hasColumn('sales_contracts', 'offers')) {
            $table->json('offers')->nullable()->after('color');
        }

        // 🌟 ៣. បន្ថែម Column 'engine_number' បើមិនទាន់មាន
        if (!Schema::hasColumn('sales_contracts', 'engine_number')) {
            $table->string('engine_number')->unique()->nullable()->after('vin_number');
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
