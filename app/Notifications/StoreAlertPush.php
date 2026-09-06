<?php

namespace App\Notifications;

use App\Models\StoreNotification;
use App\Notifications\Concerns\ResolvesTenantPushIcon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class StoreAlertPush extends Notification
{
    use Queueable, ResolvesTenantPushIcon;

    public function __construct(protected StoreNotification $alert)
    {
    }

    public function via($notifiable): array
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title($this->alert->title)
            ->icon($this->resolveIcon())
            ->body($this->alert->body)
            ->data(['url' => $this->alert->url])
            ->options(['TTL' => 300]);
    }
}
