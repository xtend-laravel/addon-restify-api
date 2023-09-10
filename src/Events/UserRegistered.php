<?php

namespace XtendLunar\Addons\RestifyApi\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;
use XtendLunar\Addons\RestifyApi\Notifications\RegistrationAdminNotification;
use XtendLunar\Addons\RestifyApi\Notifications\RegistrationCustomerNotification;

class UserRegistered
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user)
    {
        $user->notify(new RegistrationCustomerNotification($user));

        Notification::route('mail', config('mail.from.address'))
            ->notify(new RegistrationAdminNotification($user));
    }
}
