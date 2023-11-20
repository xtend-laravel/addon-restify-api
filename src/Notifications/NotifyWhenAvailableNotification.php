<?php

namespace XtendLunar\Addons\RestifyApi\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use XtendLunar\Addons\PageBuilder\Models\FormSubmission;

class NotifyWhenAvailableNotification extends Notification
{
    use Queueable;

    protected Collection $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected FormSubmission $formSubmission)
    {
        $formName = Str::of($this->formSubmission->form->name)->headline()->value();
        $payload = $this->formSubmission->payload;

        //{"email": "vofujo@mailinator.com", "action": "notify-when-available", "product": "41", "selectedOptions": {"size": null, "color": 70}}

        $this->message = collect([
            __('Form: :form', ['form' => $formName]),
            __('Email: :email', ['email' => $payload['email']]),
            __('Product: :product', ['product' => $payload['product']]),
            __('Selected Options: :selectedOptions', ['selectedOptions' => $payload['selectedOptions']]),
            __('Submitted at: :date', ['date' => $this->formSubmission->created_at->format('m/d/Y h:i A')]),
        ]);
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
        return (new MailMessage)
            ->subject('User has requested to be notified when this product is available.')
            ->lines($this->message);
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
