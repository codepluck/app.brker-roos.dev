<?php

namespace Modules\Abstracts\Notifications;

use Illuminate\Notifications\Notification as LaravelNotification;
use Illuminate\Support\Facades\Config;

abstract class Notification extends LaravelNotification
{
    public function via($notifiable = null): array
    {
        return Config::get('notification.channels'); // get default notification channel
    }
}
