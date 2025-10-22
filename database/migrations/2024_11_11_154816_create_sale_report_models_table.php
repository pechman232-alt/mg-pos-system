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
        Schema::create('sale_reports', function (Blueprint $table) {
            $table->id();
            $table->string('image', 200);
            $table->unsignedInteger('branch_id')->nullable();
            $table->string('member_card_id', 50)->nullable();
            $table->string('brand_name', 10);
            $table->string('customer_name', 100)->nullable();
            $table->string('phone_number', 45)->nullable();
            $table->string('gender', 7);
            $table->date('dob')->nullable();
            $table->string('source_id', 50)->nullable();
            $table->unsignedBigInteger('logistics_id');
            $table->string('plate_number', 30)->nullable();
            $table->string('invoice_no', 15)->nullable();
            $table->date('invoice_date');
            $table->date('delivery_date');
            $table->string('follow_up', 10)->nullable();
            $table->unsignedInteger('warranty_id')->nullable();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('province_id');
            $table->string('contract_no', 10)->nullable();
            $table->date('contract_date')->nullable();
            $table->string('vpc_vdr', 10);
            $table->string('offer_license_plate', 30);
            $table->string('offer_uv_film', 30);
            $table->string('offer_floor_mat', 30);
            $table->string('side_step', 30)->nullable();
            $table->string('auto_tailgate', 30)->nullable();
            $table->string('rear_spoiler', 30)->nullable();
            $table->string('certificate_engine_oil', 30);
            $table->string('pro_offer_oil')->nullable();
            $table->date('pro_offer_oil_date')->nullable();
            $table->date('vihicle_battery')->nullable();
            $table->string('price', 30)->nullable();
            $table->string('remark_sa')->nullable();
            $table->string('attach_file', 200)->nullable();
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_reports');
    }
};
