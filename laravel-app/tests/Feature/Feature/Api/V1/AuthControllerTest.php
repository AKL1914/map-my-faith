<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_redirect_to_google_returns_url()
    {
        $response = $this->getJson('/api/v1/auth/google');

        $response->assertOk();
        $response->assertJsonStructure(['url']);
    }

    public function test_google_callback_creates_user_and_returns_token()
    {
        $mockUser = Mockery::mock(SocialiteUser::class);
        $mockUser->shouldReceive('getEmail')->andReturn('leeann@example.com');
        $mockUser->shouldReceive('getName')->andReturn('Leeann Cawley');
        $mockUser->shouldReceive('getId')->andReturn('google123');
        $mockUser->shouldReceive('getAvatar')->andReturn('http://example.com/avatar.jpg');

        Socialite::shouldReceive('driver->stateless->user')->andReturn($mockUser);

        $response = $this->getJson('/api/v1/auth/google/callback');

        $response->assertOk();
        $response->assertJsonStructure(['token', 'user' => ['email', 'name']]);
        $this->assertDatabaseHas('users', ['email' => 'leeann@example.com']);
    }
}
