<?php

namespace App\Listeners;

use App\Events\UserCreated;
use Illuminate\Support\Facades\Redis;

class SendUserCreatedNotification
{
    public function handle(UserCreated $event)
    {
        $userData = json_encode([
            'email' => $event->user->email,
            'firstName' => $event->user->firstName,
            'lastName' => $event->user->lastName,
        ]);

        Redis::publish('user-created', $userData);
    }
}