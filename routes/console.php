<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use App\Jobs\FetchAndProcessAiNews;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\ProcessNewsIngestionCommand;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::job(new FetchAndProcessAiNews)->everyFifteenMinutes(); //for development/testing
//Schedule::job(new FetchAndProcessAiNews)->hourly(); //for production
Schedule::command('news:ingest')->everyFifteenMinutes();