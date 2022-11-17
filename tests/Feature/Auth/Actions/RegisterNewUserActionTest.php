<?php

declare(strict_types=1);

namespace Tests\Feature\Auth\Actions;

use Domain\Auth\Contracts\RegisterNewUserContract;
use Domain\Auth\DTOs\NewUserDTO;
use Illuminate\Foundation\Testing\RefreshDatabase;

final class RegisterNewUserActionTest extends \Tests\TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @return void
     */
    public function it_success_user_created(): void
    {
        $this->assertDatabaseMissing('users', [
            'email' => 'sxdev@yandex.ru'
        ]);

        $action = app(RegisterNewUserContract::class);

        $action(NewUserDTO::make('Test', 'sxdev@yandex.ru', '123456789'));

        $this->assertDatabaseHas('users', [
            'email' => 'sxdev@yandex.ru'
        ]);
    }
}
