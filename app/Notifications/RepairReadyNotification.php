<?php

namespace App\Notifications;

use App\Models\Repair;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RepairReadyNotification extends Notification
{
    public function __construct(private Repair $repair)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $storeName = app()->bound('currentTenant') ? app('currentTenant')->name : 'Online Sale';

        return (new MailMessage)
            ->subject("جهازك جاهز للاستلام - {$storeName}")
            ->greeting('مرحباً ' . $this->repair->customer_name)
            ->line("جهازك ({$this->repair->device_name} {$this->repair->model}) خلصت صيانته وجاهز للاستلام.")
            ->line('العطل: ' . $this->repair->issue)
            ->line('تقدر تمر تستلمه بأي وقت مناسب إلك.');
    }
}
