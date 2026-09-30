<?php

namespace Tests\Feature;

use App\Jobs\SendAccessRequestNotificationToAdmins;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AuthRegistrationLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_displays_email_sign_in_and_registration_alongside_google_sign_in(): void
    {
        $response = $this->get(route('home'))->assertOk();
        $content = $response->getContent();

        $this->assertStringContainsString('Sign in with email', $content);
        $this->assertStringContainsString('Sign in with Google', $content);
        $this->assertStringContainsString('href="'.route('register').'"', $content);
        $this->assertLessThan(
            strpos($content, 'Sign in with Google'),
            strpos($content, 'Sign in with email')
        );
    }

    public function test_guest_can_open_registration_page(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Create an account');
    }

    public function test_registration_creates_an_inactive_user_and_notifies_admins(): void
    {
        Queue::fake();

        $this->post(route('register.submit'), [
            'name' => 'New User',
            'email' => 'new@example.com',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
        ])->assertRedirect(route('activate'));

        $user = User::where('email', 'new@example.com')->firstOrFail();
        $this->assertFalse((bool) $user->is_activated);
        $this->assertTrue(Hash::check('secret-password', $user->password));
        $this->assertGuest();

        Queue::assertPushed(SendAccessRequestNotificationToAdmins::class);
    }

    public function test_inactive_user_cannot_sign_in(): void
    {
        User::factory()->create([
            'email' => 'waiting@example.com',
            'password' => Hash::make('secret-password'),
            'is_activated' => false,
        ]);

        $this->post(route('login.submit'), [
            'email' => 'waiting@example.com',
            'password' => 'secret-password',
        ])->assertRedirect(route('activate'));

        $this->assertGuest();
    }

    public function test_activated_user_can_sign_in(): void
    {
        User::factory()->create([
            'email' => 'active@example.com',
            'password' => Hash::make('secret-password'),
            'is_activated' => true,
        ]);

        $this->post(route('login.submit'), [
            'email' => 'active@example.com',
            'password' => 'secret-password',
        ])->assertRedirect('/maps');

        $this->assertAuthenticated();
    }

    public function test_registration_requires_matching_confirmed_password(): void
    {
        $this->from(route('register'))
            ->post(route('register.submit'), [
                'name' => 'New User',
                'email' => 'new@example.com',
                'password' => 'secret-password',
                'password_confirmation' => 'different-password',
            ])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors('password');

        $this->assertDatabaseMissing('users', ['email' => 'new@example.com']);
    }
}
