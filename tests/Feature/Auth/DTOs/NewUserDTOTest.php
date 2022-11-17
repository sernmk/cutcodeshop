<?php

declare(strict_types=1);

namespace Tests\Feature\Auth\DTOs;

use App\Http\Requests\SignUpFormRequest;
use Domain\Auth\DTOs\NewUserDTO;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class NewUserDTOTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @return void
     */
    public function it_instance_created_from_form_request(): void
    {
        $dto = NewUserDTO::formRequest(new SignUpFormRequest([
            'name' => 'test',
            'email' => 'sxdev@yandex.ru',
            'password' => '123456789'
        ]));

        $this->assertInstanceOf(NewUserDTO::class, $dto);
    }
}
