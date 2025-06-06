<?php

namespace App\Jobs;

use App\Models\Pin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class GeneratePinReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filters;

    protected $userEmail;

    /**
     * Create a new job instance.
     */
    public function __construct(array $filters, $userEmail = null)
    {
        if (empty($filters['start_date']) || empty($filters['end_date'])) {
            $filters['start_date'] = Carbon::now()->subWeek()->startOfWeek(Carbon::SUNDAY)->toDateString(); // Last Sunday
            $filters['end_date'] = Carbon::now()->subDay()->endOfDay()->toDateString(); // Yesterday (Friday) if today is Saturday early
            if (Carbon::now()->isSaturday()) {
                $filters['end_date'] = Carbon::now()->endOfDay()->toDateString(); // Include today if Saturday
            }
        }

        $this->filters = $filters;
        $this->userEmail = $userEmail;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Build query with filters
        $query = Pin::with(['campaign', 'user']);

        if (! empty($this->filters['campaign_id'])) {
            $query->where('campaign_id', $this->filters['campaign_id']);
        }

        if (! empty($this->filters['start_date'])) {
            $query->whereDate('created_at', '>=', Carbon::parse($this->filters['start_date']));
        }

        if (! empty($this->filters['end_date'])) {
            $query->whereDate('created_at', '<=', Carbon::parse($this->filters['end_date']));
        }

        $pins = $query->get();

        // Temporary file path for CSV
        $filename = 'pin_report_'.time().'.csv';
        $filepath = storage_path('app/reports/'.$filename);

        // Ensure reports directory exists
        if (! file_exists(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }

        // Open file and write CSV
        $handle = fopen($filepath, 'w');

        fputcsv($handle, [
            'ID',
            'Campaign Name',
            'User Name',
            'CFO',
            'Suburb',
            'Latitude',
            'Longitude',
            'Notes',
            'Created At',
        ]);

        foreach ($pins as $pin) {
            fputcsv($handle, [
                $pin->id,
                $pin->campaign->name ?? 'N/A',
                $pin->user->name ?? 'N/A',
                $pin->user->cfo ?? 'N/A',
                $pin->suburb,
                $pin->latitude,
                $pin->longitude,
                $pin->notes,
                $pin->created_at,
            ]);
        }

        fclose($handle);

        // Send email with attachment
        //        Mail::to($this->userEmail)->send(new PinReportGenerated($filepath));
        $filePath = $filepath;
        Mail::raw('Your Pin Report is Ready', function ($message) use ($filePath) {
            $message->to($this->userEmail)
                ->subject('Your Pin Report is Ready')
                ->attach($filePath, [
                    'as' => 'pin_report.csv',
                    'mime' => 'text/csv',
                ]);
        });

        // Optional: delete the file after sending
        unlink($filepath);
    }
}
