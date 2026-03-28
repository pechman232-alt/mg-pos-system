<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('service_bookings');

        Schema::create('service_bookings', function (Blueprint $table) {
            $table->id();
            
            // ១. ព័ត៌មានអតិថិជន និងរថយន្ត
            $table->string('customer_name');
            $table->string('contact'); 
            $table->string('model');
            $table->string('plate');
            $table->string('service_type'); 
            
            // ២. ព័ត៌មានការណាត់ជួប
            $table->date('booking_date');
            $table->time('booking_time');
            
            // ៣. ទីតាំងសាខា
            $table->string('location'); 
            
            // ៤. ស្ថានភាពការងារ
            $table->string('status')->default('Pending');

            // ៥. បុគ្គលិកទទួលបន្ទុក និងម៉ោងធ្វើការ
            $table->string('sa_name')->nullable();           // Service Advisor
            $table->string('technician_name')->nullable();   // មេជាងជួសជុល
            
            // ប្រើ Timestamp ដើម្បីងាយស្រួលគណនារយៈពេល (Diff)
            $table->timestamp('arrival_time')->nullable();   // ម៉ោងភ្ញៀវមកដល់
            $table->timestamp('start_time')->nullable();     // ម៉ោងចាប់ផ្ដើមធ្វើ
            $table->timestamp('end_time')->nullable();       // ម៉ោងបញ្ចប់ការងារ
            
            $table->date('est_finish_date')->nullable();     // ថ្ងៃរំពឹងថានឹងរួច
            $table->integer('total_seconds')->default(0);    // រយៈពេលធ្វើការសរុប (Seconds)
            
            $table->text('remarks')->nullable();    
            $table->text('mechanic_remark')->nullable();     // ចំណាំពីជាង
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_bookings');
    }
};