<?php

use App\Jobs\GeneratePinReport;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    $filters = [
        'start_date' => now('Pacific/Auckland')->subWeek()->startOfWeek(Carbon::SUNDAY)->toDateString(),
        'end_date' => now('Pacific/Auckland')->subWeek()->endOfWeek(Carbon::SATURDAY)->toDateString(),
    ];
    $email = config('app.los_email'); // Use correct env key

    GeneratePinReport::dispatch($filters, $email);

    Log::info('Weekly pin report dispatched with filters: '.json_encode($filters));
})->weeklyOn(6, '13:00', 'Pacific/Auckland') // Saturday 1PM NZ time
    ->name('generate.weekly.pin.report')
    ->withoutOverlapping();

// Supabase free-tier projects auto-pause after inactivity; a lightweight
// periodic query keeps the project active. Remove once the Supabase
// project is on a paid plan (no auto-pause).
Schedule::call(function () {
    DB::select('select 1');
})->everyTwoDays()
    ->name('supabase.keep-alive')
    ->withoutOverlapping()
    ->onFailure(function () {
        Log::error('Supabase keep-alive query failed.');
    });
