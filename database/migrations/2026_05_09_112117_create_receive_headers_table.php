<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // ១. បង្កើត Table ជាមុនសិន ដោយមិនទាន់ដាក់ Foreign Key ភ្លាមៗទេ
        Schema::create('receive_headers', function (Blueprint $table) {
            $table->id();
            $table->string('document_no')->unique();
            $table->date('date');
            $table->unsignedBigInteger('location_id'); // បង្កើតជា Column ធម្មតាសិន
            $table->unsignedBigInteger('vendor_id');   // បង្កើតជា Column ធម្មតាសិន
            $table->string('purchase_invoice_no')->nullable();
            $table->string('tran_vendor')->nullable();
            $table->timestamps();
        });

        // ២. បន្ទាប់មកទើបភ្ជាប់ Foreign Key តាមក្រោយ
        Schema::table('receive_headers', function (Blueprint $table) {
            // ភ្ជាប់ទៅ branches (ផ្អែកលើរូបភាពដែលអ្នកផ្ញើមក គឺមានអក្សរ e)
            $table->foreign('location_id')->references('id')->on('branches')->onDelete('cascade');
            
            // ភ្ជាប់ទៅ vendors (ដែលអ្នកទើបនឹងបង្កើត)
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('receive_headers');
    }
};