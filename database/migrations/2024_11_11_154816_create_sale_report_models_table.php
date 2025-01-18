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
            $table->unsignedInteger('branch_id');
            $table->string('member_card', 10);
            $table->string('brand_name', 10);
            $table->string('customer_name', 30);
            $table->string('phone_number', 45);
            $table->string('gender', 6);
            $table->date('dob');
            $table->integer('age');
            $table->unsignedBigInteger('source_id');
            $table->unsignedBigInteger('logistics_id');
            $table->string('plate_number', 10)->nullable();
            $table->string('invoice_no', 10);
            $table->date('invoice_date');
            $table->date('delivery_date');
            $table->string('follow_up', 10)->nullable();
            $table->unsignedInteger('warranty_id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('province_id');
            $table->string('contract_no', 10);
            $table->date('contract_date')->nullable();
            $table->string('vpc_vdr', 10);
            $table->string('offer_license_plate', 3);
            $table->string('offer_uv_film', 3);
            $table->string('offer_floor_mat', 3);
            $table->string('side_step', 3);
            $table->string('auto_tailgate', 3);
            $table->string('rear_spoiler', 3);
            $table->string('certificate_engine_oil', 20);
            $table->string('pro_offer_oil')->nulable();
            $table->date('pro_offer_oil_date')->nulable();
            $table->date('vihicle_battery')->nulable();
            $table->string('remark_sa')->nullable();
            $table->string('attach_file', 200);
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
