<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('parties:update-statuses')->everyMinute();

Schedule::call(function () {
    $files = \Storage::disk('public')->files('temp-photos');
    foreach ($files as $file) {
        if (\Storage::disk('public')->lastModified($file) < now()->subHours(2)->timestamp) {
            \Storage::disk('public')->delete($file);
        }
    }
})->hourly();