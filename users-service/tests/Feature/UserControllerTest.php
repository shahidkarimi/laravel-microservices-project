<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Redis;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_user()
    {
        Redis::shouldReceive('publish')->once();

        $response = $this->postJson('/api/users', [
            'email' => 'test@example.com',
            'firstName' => 'John',
            'lastName' => 'Doe'
        ]);

        $response->assertStatus(201)
            ->assertJson([
                    'email' => 'test@example.com',
                    'firstName' => 'John',
                    'lastName' => 'Doe'
                
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'firstName' => 'John',
            'lastName' => 'Doe'
        ]);
    }

    public function test_cannot_create_user_with_invalid_data()
    {
        $response = $this->postJson('/api/users', [
            'email' => 'invalid-email',
            'firstName' => '',
            'lastName' => ''
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'firstName', 'lastName']);
    }
}