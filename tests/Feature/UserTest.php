<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use WithFaker;

    public function testFindAllUsers()
    {
        $response = $this->get('/api/users');
        $response->assertStatus(200);
    }

    public function testFindUserById()
    {
        $user = User::factory()->create();
        $response = $this->get("/api/users/{$user->id}");
        $response->assertStatus(200);
    }

    public function testFindUserByEmail()
    {
        $user = User::factory()->create();
        $response = $this->get("/api/users/email/{$user->email}");
        $response->assertStatus(200);
    }

    public function testMe()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post('/api/users/me');
        $response->assertStatus(200);
    }

    public function testDeleteMyAccount()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->delete('/api/users/me');
        $response->assertStatus(200);
    }


    public function testSendEmailVerificationNotification()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post('/api/users/v1/email/verify');
        $response->assertStatus(200);
    }

    public function testLogin()
    {
        $user = User::factory()->create();
        $response = $this->post('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);
        $response->assertStatus(200);
    }
    public function testLoginWithGoogle()
    {
        $response = $this->get('/api/auth/google');
        $response->assertStatus(302);
    }

}
