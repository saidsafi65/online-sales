<?php

namespace App\Notifications;

use App\Models\Repair;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RepairCompletedNotification extends Notification
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
        $totalCost = (float) $this->repair->cost_cash + (float) $this->repair->cost_bank;

        return (new MailMessage)
            ->subject("تمت صيانة جهازك بنجاح - {$storeName}")
            ->greeting('شكراً لتعاملك معنا ' . $this->repair->customer_name)
            ->line("نشكرك على ثقتك بـ {$storeName}.")
            ->line("تمت صيانة جهازك ({$this->repair->device_name} {$this->repair->model}) بنجاح.")
            ->line('العطل: ' . $this->repair->issue)
            ->line('تكلفة الصيانة: ' . number_format($totalCost, 2) . ' شيكل')
            ->line('⏱️ معك ضمان 24 ساعة على هذه الصيانة من تاريخ الاستلام — إذا واجهتك نفس العطل خلال هذه الفترة، تواصل معنا فوراً ومنساعدك.')
            ->line('شكراً لثقتك فينا، ونتمنالك يوم سعيد!');
    }
}
