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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            
            // 📅 ព័ត៌មានមូលដ្ឋាន (Core Attribution)
            $table->date('date');                               // ថ្ងៃដែលអតិថិជនទាក់ទងមកដំបូង
            $table->string('sales_person');                    // ឈ្មោះអ្នកលក់ដែលកាន់ Lead នេះ
            $table->string('branch');                          // សាខាប្រតិបត្តិការ
    
            // 👤 អត្តសញ្ញាណអតិថិជន (Customer Identity)
            $table->string('customer_name');                   // ឈ្មោះពេញ
            $table->string('contact_info')->unique();          // លេខទូរស័ព្ទ (Unique ការពារទិន្នន័យជាន់គ្នា)
            $table->string('telegram')->nullable();            // គណនី Telegram
            $table->string('location')->nullable();            // ខេត្ត/ក្រុង
            $table->string('source')->nullable();              // ប្រភព (FB, Walk-in, TikTok...)
    
            // 🚙 ព័ត៌មានរថយន្ត និងស្ថានភាព (Product & Status)
            $table->string('model');                           // ម៉ូដែលឡានដែលចាប់អារម្មណ៍ (MG7, ZS...)
            $table->string('color')->nullable();               // ពណ៌ដែលចង់បាន
            $table->string('status')->default('captured');     // ស្ថានភាពបច្ចុប្បន្ន (captured, won, lost...)
    
            // 💰 ព័ត៌មានហិរញ្ញវត្ថុ (Deal Framework)
            $table->decimal('dp_percent', 5, 2)->nullable();   // ភាគរយប្រាក់កក់មុន
            $table->decimal('mp', 10, 2)->nullable();          // ទឹកប្រាក់បង់ប្រចាំខែ (Monthly Payment)
            $table->integer('lp')->nullable();                 // រយៈពេលបង់រំលស់ (Loan Period គិតជាខែ)
    
            // 🧠 ការវិភាគគុណភាព (Qualitative Data)
            $table->text('concern')->nullable();               // ក្ដីបារម្ភរបស់ភ្ញៀវ
            $table->string('competition')->nullable();         // ម៉ាកឡានគូប្រជែងដែលភ្ញៀវធៀបជាមួយ
            $table->text('comment')->nullable();               // កំណត់ចំណាំដំបូង
    
            $table->timestamps();                              // ម៉ោងបង្កើត និង ម៉ោង Update ចុងក្រោយ
        });
    }
};