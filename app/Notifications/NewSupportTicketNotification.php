<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewSupportTicketNotification extends Notification
{
    public function __construct(private SupportTicket $ticket)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $storeName = $this->ticket->tenant->name ?? 'غير معروف';
        $typeLabel = SupportTicket::TYPES[$this->ticket->type] ?? $this->ticket->type;

        return (new MailMessage)
            ->subject("[{$typeLabel}] {$this->ticket->subject} - {$storeName}")
            ->greeting("تذكرة دعم فني جديدة من معرض {$storeName}")
            ->line("النوع: {$typeLabel}")
            ->line("مقدّم الطلب: {$this->ticket->submitter_name}")
            ->line("الموضوع: {$this->ticket->subject}")
            ->line('التفاصيل:')
            ->line($this->ticket->message)
            ->action('فتح لوحة الدعم الفني', route('system-admin.support.show', $this->ticket))
            ->line('رقم التذكرة: #' . $this->ticket->id);
    }
}
