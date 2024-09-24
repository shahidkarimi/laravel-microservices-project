<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function logUserCreated(array $userData)
    {
        Log::info('User created: ', $userData);
    }
}