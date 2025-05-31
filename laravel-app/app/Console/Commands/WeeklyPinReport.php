<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\GeneratePinReport;
use Illuminate\Support\Carbon;

class WeeklyPinReport extends Command
{
    protected $signature = 'report:weekly-pin';
    protected $description = 'Dispatch weekly pin report from last Sunday to this Saturday';

    public function handle()
    {
        $email = env('app.los_email'); // Replace with recipient
        $filters['start_date'] = Carbon::now()->subWeek()->startOfWeek(Carbon::SUNDAY)->toDateString(); // Last Sunday
        $filters['end_date'] = Carbon::now()->subDay()->endOfDay()->toDateString(); // Yesterday (Friday) if today is Saturday early

        GeneratePinReport::dispatch($filters, $email);

        $this->info('Weekly pin report dispatched for last Sunday to this Saturday.1');
    }
}

