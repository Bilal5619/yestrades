<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CompanyResetPasswordNotification
    extends Notification
{
    use Queueable;

    protected $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = route(
            'company.password.reset',
            [
                'token' => $this->token,
                'email' => $notifiable->email,
            ]
        );

        return (new MailMessage)
            ->subject(
                'Reset Your Company Portal Password'
            )
            ->greeting(
                'Hello '
                . $notifiable->first_name
                . ','
            )
            ->line(
                'We received a request to reset your Company Portal password.'
            )
            ->action(
                'Reset Password',
                $url
            )
            ->line(
                'This password reset link will expire in 60 minutes.'
            )
            ->line(
                'If you did not request a password reset, no action is required.'
            );
    }
}