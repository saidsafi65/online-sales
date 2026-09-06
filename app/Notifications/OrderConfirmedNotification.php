<?php

namespace App\Notifications;

use App\Models\Order;
use App\Notifications\Concerns\ResolvesTenantPushIcon;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;

class OrderConfirmedNotification extends Notification
{
    use ResolvesTenantPushIcon;

    public function __construct(private Order $order)
    {
    }

    public function via($notifiable): array
    {
        return ['mail', 'webpush'];
    }

    public function toWebPush($notifiable, $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title("تأكيد الطلب #{$this->order->id}")
            ->icon($this->resolveIcon())
            ->body('استلمنا طلبك وجاري تجهيزه — الإجمالي: ' . number_format($this->order->total, 2) . ' شيكل')
            ->data(['url' => route('customer.orders.show', $this->order)])
            ->options(['TTL' => 300]);
    }

    public function toMail($notifiable): MailMessage
    {
        $storeName = app()->bound('currentTenant') ? app('currentTenant')->name : 'Online Sale';

        $message = (new MailMessage)
            ->subject("تأكيد الطلب #{$this->order->id} - {$storeName}")
            ->greeting('مرحباً ' . $this->order->customer_name)
            ->line("استلمنا طلبك رقم #{$this->order->id} وجاري تجهيزه.")
            ->line('تفاصيل الطلب:');

        foreach ($this->order->items as $item) {
            $message->line("- {$item->product_name} × {$item->quantity} — " . number_format($item->price * $item->quantity, 2) . ' شيكل');
        }

        return $message
            ->line('الإجمالي: ' . number_format($this->order->total, 2) . ' شيكل')
            ->line('عنوان التوصيل: ' . $this->order->shipping_address)
            ->action('متابعة حالة الطلب', route('customer.orders.show', $this->order))
            ->line('رح نبعتلك تحديث تاني لما تتأكد حالة الدفع.');
    }
}
