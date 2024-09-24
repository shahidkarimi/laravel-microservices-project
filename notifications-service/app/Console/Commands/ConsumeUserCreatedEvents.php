<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class ConsumeUserCreatedEvents extends Command
{
    protected $signature = 'consume:user-created-events';
    protected $description = 'Consume user created events from Redis';

    public function handle(NotificationService $notificationService)
    {
        $retryDelay = 5;

        while (true) {
            try {
                Redis::subscribe(['user-created'], function ($message) use ($notificationService) {
                    // Ensure $userData is an array before passing it to logUserCreated
                    $userData = json_decode($message, true);
                    if (is_array($userData)) {
                        $notificationService->logUserCreated($userData);
                    } else {
                        Log::error('Invalid user data received: ' . $message);
                    }
                });
            } catch (\Exception $e) {
                $this->error("Subscription failed: " . $e->getMessage());
                $this->info("Retrying in $retryDelay seconds...");
                sleep($retryDelay);
            }
        }
    }
}