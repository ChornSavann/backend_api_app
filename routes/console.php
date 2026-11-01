<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 🌟 កំណត់ការរត់ស្វ័យប្រវត្តិជារៀងរាល់ថ្ងៃ
Schedule::call(function () {
    app('App\Http\Controllers\PromotionController')->expirepromotion();
})->daily();