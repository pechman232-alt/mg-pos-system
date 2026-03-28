<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule; // 👈 ត្រូវប្រាកដថាមានបន្ទាត់នេះ

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// 🚀 កំណត់ឱ្យផ្ញើរបាយការណ៍សង្ខេប Leads រាល់ថ្ងៃនៅម៉ោង ៥:៣០ ល្ងាច
Schedule::command('leads:send-summary')->dailyAt('17:30');