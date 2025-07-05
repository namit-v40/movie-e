<?php

use App\Console\Commands\UpdateStatusSubscription;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(UpdateStatusSubscription::class)->dailyAt('23:59');
