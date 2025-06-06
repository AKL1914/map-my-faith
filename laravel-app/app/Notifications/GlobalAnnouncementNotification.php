<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;

class GlobalAnnouncementNotification extends Notification
{
    use Queueable;

    public $title;

    public $body;

    public $level;

    public function __construct($title, $body, $level = 'info')
    {
        $this->title = $title;
        $this->body = $body;
        $this->level = $level;
    }

    public function via($notifiable)
    {
        return ['webpush'];
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title($this->title)
            ->body($this->body)
            ->icon('/icon.png')
            ->data([
                'url' => '/admin/events',
                'title' => $this->title,
                'body' => $this->body,
                'level' => $this->level,
            ]);
    }
}
