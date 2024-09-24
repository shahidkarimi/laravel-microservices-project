<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class UserCreatedEventConsumerTest extends TestCase
{
    public function test_consumes_user_created_event()
    {
        $userData = [
            'email' => 'test@example.com',
            'firstName' => 'John',
            'lastName' => 'Doe'
        ];

        Redis::shouldReceive('subscribe')
            ->once()
            ->withArgs(function ($channels, $callback) {
                return $channels === ['user-created'] && is_callable($callback);
            })
            ->andReturnUsing(function ($channels, $callback) use ($userData) {
                $callback('user-created', json_encode($userData));
                Redis::shouldReceive('__call')->with('close')->once()->andReturn(true);
                return true; 
            });

        Log::shouldReceive('info')
            ->once()
            ->with('New user created: ' . json_encode($userData));

        $this->artisan('consume:user-created-events')
            ->expectsOutput('New user created: ' . json_encode($userData))
            ->assertExitCode(0);
    }
}