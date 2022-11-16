<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Domain\Auth\Models\User;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SocialAuthController extends Controller
{
    public function redirect(string $driver): \Symfony\Component\HttpFoundation\RedirectResponse|RedirectResponse
    {
        try {
            return Socialite::driver($driver)
                ->redirect();
        } catch (Throwable $exception) {
            throw new DomainException('Произошла ошибка или драйвер не поддерживается');
        }

    }

    public function callback(string $driver): RedirectResponse
    {
        if ($driver !== 'github') {
            throw new DomainException('Драйвер не поддерживается');
        }

        $githubUser = Socialite::driver($driver)->user();

//        if (User::query()
//            ->where('email', $githubUser->getEmail())
//            ->exists()) {
//            return to_route('login')->withErrors([
//                'email' => __('validation.unique', ['attribute' => 'email'])
//            ]);
//        }

        //TODO move to custom table
        // table: socials_auth, туда вынести провайдеры (github, vk, ok и т.д.)
        // привязка к user_id с ключами

        //TODO если есть созданный не через гитхаб пользователь, то Duplicate entry

        $user = User::query()->updateOrCreate([
            $driver.'_id' => $githubUser->getId(),
        ], [
            'name' => $githubUser->getName(),
            'email' => $githubUser->getEmail(),
            'password' => bcrypt(str()->random(20))
        ]);

        auth()->login($user);

        return redirect()->intended(route('home'));
    }
}
