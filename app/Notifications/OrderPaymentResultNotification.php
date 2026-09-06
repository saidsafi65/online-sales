<?php

namespace App\Notifications;

use App\Models\Order;
use App\Notifications\Concerns\ResolvesTenantPushIcon;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;

class OrderPaymentResultNotification extends Notification
{
    use ResolvesTenantPushIcon;

    public function __construct(private Order $order, private bool $success)
    {
    }

    public function via($notifiable): array
    {
        return ['mail', 'webpush'];
    }

    public function toWebPush($notifiable, $notification): WebPushMessage
    {
        $title = $this->success
            ? "تم تأكيد الدفع - طلب #{$this->order->id}"
            : "تعذّر الدفع - طلب #{$this->order->id}";

        $body = $this->success
            ? 'تم تأكيد الدفع بنجاح — الإجمالي: ' . number_format($this->order->total, 2) . ' شيكل'
            : 'تعذّر إتمام عملية الدفع، والكمية المحجوزة رجعت للمخزون';

        return (new WebPushMessage)
            ->title($title)
            ->icon($this->resolveIcon())
            ->body($body)
            ->data(['url' => route('customer.orders.show', $this->order)])
            ->options(['TTL' => 300]);
    }

    public function toMail($notifiable): MailMessage
    {
        $storeName = app()->bound('currentTenant') ? app('currentTenant')->name : 'Online Sale';

        if ($this->success) {
            return (new MailMessage)
                ->subject("تم تأكيد الدفع - طلب #{$this->order->id} - {$storeName}")
                ->greeting('مرحباً ' . $this->order->customer_name)
                ->line("تم تأكيد الدفع بنجاح لطلبك رقم #{$this->order->id}.")
                ->line('الإجمالي: ' . number_format($this->order->total, 2) . ' شيكل')
                ->action('عرض الطلب', route('customer.orders.show', $this->order))
                ->line('شكراً لتسوقك معنا!');
        }

        return (new MailMessage)
            ->subject("تعذّر الدفع - طلب #{$this->order->id} - {$storeName}")
            ->greeting('مرحباً ' . $this->order->customer_name)
            ->line("للأسف تعذّر إتمام عملية الدفع لطلبك رقم #{$this->order->id}.")
            ->line('الكمية المحجوزة رجعت للمخزون، وطلبك ما اتأكد.')
            ->action('إعادة المحاولة', route('cart.index'))
            ->line('إذا استمرت المشكلة، تواصل معنا مباشرة.');
    }
}
