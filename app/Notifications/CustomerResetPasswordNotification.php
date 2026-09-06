<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordBase;
use Illuminate\Notifications\Messages\MailMessage;

class CustomerResetPasswordNotification extends ResetPasswordBase
{
    public function toMail($notifiable)
    {
        $url = url(route('customer.password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('استعادة كلمة المرور - Online Sale')
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('وصلنا طلب لاستعادة كلمة المرور الخاصة بحسابك.')
            ->action('إعادة تعيين كلمة المرور', $url)
            ->line('هذا الرابط صالح لمدة 60 دقيقة فقط.')
            ->line('إذا لم تطلب استعادة كلمة المرور، تجاهل هذه الرسالة.');
    }
}