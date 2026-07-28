<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // ១. បង្កើត Table ជាមុនសិន
        Schema::create('receive_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('receive_header_id'); 
            $table->unsignedBigInteger('product_id'); // ប្រើបែបនេះសិន បើខ្លាច Error លំដាប់ Migration ជាមួយ products
            $table->string('description')->nullable();
            $table->string('rec_um')->default('UNIT');
            $table->decimal('qty', 15, 2)->default(0);
            $table->decimal('cost', 15, 2)->default(0);
            $table->decimal('total_cost', 15, 2)->default(0);
            $table->timestamps();
        });

        // ២. ភ្ជាប់ Foreign Key ទាំងអស់តាមក្រោយក្នុងពេលតែមួយ
        Schema::table('receive_details', function (Blueprint $table) {
            $table->foreign('receive_header_id')
                  ->references('id')->on('receive_headers')
                  ->onDelete('cascade');

            $table->foreign('product_id')
                  ->references('id')->on('products')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        // ពេលលុបវិញ ត្រូវបិទ Foreign Key Check សិនដើម្បីកុំឱ្យទាស់គ្នា
        Schema::table('receive_details', function (Blueprint $table) {
            $table->dropForeign(['receive_header_id']);
            $table->dropForeign(['product_id']);
        });
        Schema::dropIfExists('receive_details');
    }
};