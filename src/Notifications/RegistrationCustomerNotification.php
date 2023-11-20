<?php

namespace XtendLunar\Addons\RestifyApi\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RegistrationCustomerNotification extends Notification
{
    use Queueable;

    protected string $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected User $user)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $tempPassword = 'AWR23'.$this->user->latestCustomer()->id;
        return (new MailMessage)
                    ->line('Thank you for registering at awraaf.com!')
                    ->line("Your temporary password is: $tempPassword")
                    ->action('Login', url('https://awraaf.com/login'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
