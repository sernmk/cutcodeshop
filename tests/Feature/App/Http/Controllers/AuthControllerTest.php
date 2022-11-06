<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\SignInController;
use App\Http\Controllers\Auth\SignUpController;
use App\Http\Requests\SignInFormRequest;
use App\Http\Requests\SignUpFormRequest;
use App\Listeners\SendEmailNewUserListener;
use App\Notifications\NewUserNotification;
use Database\Factories\UserFactory;
use Domain\Auth\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    // TODO ДЗ - сделать остальные тесты и неудачные тоже (когда неправильные данные поступают)
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_login_page_success(): void
    {
        $this->get(action([SignInController::class, 'page']))
            ->assertOk()
            ->assertSee('Вход в аккаунт')
            ->assertViewIs('auth.login');
    }

    /**
     * @test
     */
    public function it_sign_up_page_success(): void
    {
        $this->get(action([SignUpController::class, 'page']))
            ->assertOk()
            ->assertSee('Регистрация')
            ->assertViewIs('auth.sign-up');
    }

    /**
     * @test
     */
    public function it_forgot_page_success(): void
    {
        $this->get(action([ForgotPasswordController::class, 'page']))
            ->assertOk()
            ->assertSee('Восстановление пароля')
            ->assertViewIs('auth.forgot-password');
    }

    /**
     * @test
     */
    public function it_sign_in_success(): void
    {
        $password = '123456789';
        $user = UserFactory::new()->create([
            'email' => 'sxdev@yandex.ru',
            'password' => bcrypt($password)
        ]);
        $request = SignInFormRequest::factory()->create([
            'email' => $user->email,
            'password' => $password
        ]);

        $response = $this->post(action([SignInController::class, 'handle']), $request);

        $response->assertValid()
            ->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
    }

    /**
     * @test
     * @return void
     */
    public function it_sign_in_fail_email(): void
    {
        $password = '123456789';
        $user = UserFactory::new()->create([
            'email' => 'sxdev@yandex.ru',
            'password' => bcrypt($password)
        ]);
        $request = SignInFormRequest::factory()->create([
            'email' => 'sxdevvvv@yandex.ru',
            'password' => $password
        ]);

        $response = $this->post(action([SignInController::class, 'handle']), $request);

        $response->assertInvalid();

    }

    /**
     * @test
     * @return void
     */
    public function it_sign_in_fail_password(): void
    {
        $password = '123456789';
        $user = UserFactory::new()->create([
            'email' => 'sxdev@yandex.ru',
            'password' => bcrypt($password)
        ]);
        $request = SignInFormRequest::factory()->create([
            'email' => $user->email,
            'password' => '111111111'
        ]);

        $response = $this->post(action([SignInController::class, 'handle']), $request);

        $response->assertInvalid();
    }

    /** @test */
    public function it_sign_up_success(): void
    {
        Notification::fake();
        Event::fake();

        $request = SignUpFormRequest::factory()->create([
            'email' => 'sxdev@yandex.ru',
            'password' => '123456789',
            'password_confirmation' => '123456789',
        ]);

        $this->assertDatabaseMissing('users', [
            'email' => $request['email']
        ]);

        $response = $this->post(
            action([SignUpController::class, 'handle']),
            $request
        );

        $response->assertValid();

        $this->assertDatabaseHas('users', [
            'email' => $request['email']
        ]);

        $user = User::query()
            ->where('email', $request['email'])
            ->first();

        Event::assertDispatched(Registered::class);

        Event::assertListening(Registered::class, SendEmailNewUserListener::class);

        $event = new Registered($user);
        $listener = new SendEmailNewUserListener();
        $listener->handle($event);

        Notification::assertSentTo($user, NewUserNotification::class);

        $this->assertAuthenticatedAs($user);

        $response->assertRedirect(route('home'));
    }

    /**
     * @test
     */
    public function it_logout_success(): void
    {
        $user = UserFactory::new()->create([
            'email' => 'sxdev@yandex.ru'
        ]);

        $this->actingAs($user)
            ->delete(action([SignInController::class, 'logOut']));

        $this->assertGuest();
    }
}
