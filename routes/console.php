<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Cek handover pending > 24 jam — setiap hari jam 08:00
Schedule::command('check:pending-handovers')->dailyAt('08:00');

// Cek stok kritis bahan baku — setiap hari jam 07:00
Schedule::command('check:low-stock')->dailyAt('07:00');

// Cek reject rate bulanan — setiap hari pertama bulan jam 09:00
Schedule::command('check:reject-rate')->monthlyOn(1, '09:00');
