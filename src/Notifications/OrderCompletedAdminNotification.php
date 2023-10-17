<?php

namespace XtendLunar\Addons\RestifyApi\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Xtend\Extensions\Lunar\Core\Models\Order;

class OrderCompletedAdminNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected Order $order)
    {
        $this->message = collect([
            __('Order :reference has been completed.', ['reference' => $this->order->reference]),
            __('Customer: :customer', ['customer' => $this->order->customer->email]),
            __('Total: :total', ['total' => $this->order->total]),
        ])->implode('<br>');
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
        return (new MailMessage)->line($this->message);
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
