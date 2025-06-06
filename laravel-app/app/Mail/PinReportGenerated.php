<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PinReportGenerated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $filePath;

    /**
     * Create a new message instance.
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your Pin Report is Ready')
            ->markdown('emails.pin_report')
            ->attach($this->filePath, [
                'as' => 'pin_report.csv',
                'mime' => 'text/csv',
            ]);
    }
}
