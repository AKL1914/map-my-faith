<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendAccessRequestNotificationToAdmins implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $requestingUser;

    public function __construct(User $requestingUser)
    {
        $this->requestingUser = $requestingUser;
    }

    public function handle()
    {
        // Get all admin emails from environment variables
        $adminEmails = [
            env('ADMIN_EMAIL1'),
            env('ADMIN_EMAIL2'),
            env('ADMIN_EMAIL5'),
        ];

        foreach ($adminEmails as $adminEmail) {
            Mail::raw(
                "Hello Admin,\n\n"
                ."User {$this->requestingUser->name} ({$this->requestingUser->email}) has requested access.\n\n"
                ."You can review and activate their account here:\n"
                ."https://app.mapmyfaith.co.nz/admin/users\n\n"
                .'Thank you.',
                function ($message) use ($adminEmail) {
                    $message->to($adminEmail)
                        ->subject('User Access Request');
                }
            );
        }
    }
}
