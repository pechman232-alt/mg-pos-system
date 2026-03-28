<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\LeadController;

class SendDailyLeadSummary extends Command
{
    /**
     * ឈ្មោះសម្រាប់ហៅប្រើតាមរយៈ Terminal (ឧទាហរណ៍៖ php artisan leads:send-summary)
     */
    protected $signature = 'leads:send-summary';

    /**
     * ការពិពណ៌នាអំពី Command នេះ
     */
    protected $description = 'ផ្ញើរបាយការណ៍សង្ខេប Leads ប្រចាំថ្ងៃទៅកាន់ Telegram Group';

    /**
     * ដំណើរការ Command
     */
    public function handle()
    {
        $this->info('កំពុងចាប់ផ្ដើមផ្ញើរបាយការណ៍ទៅ Telegram...');

        try {
            // ហៅមុខងារ sendDailySummary ពី LeadController
            $controller = new LeadController();
            $controller->sendDailySummary();

            $this->info('របាយការណ៍ត្រូវបានផ្ញើជោគជ័យ!');
        } catch (\Exception $e) {
            $this->error('មានបញ្ហាក្នុងការផ្ញើរបាយការណ៍៖ ' . $e->getMessage());
        }
    }
}