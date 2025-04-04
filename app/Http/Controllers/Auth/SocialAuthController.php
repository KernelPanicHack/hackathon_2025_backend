<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use function Laravel\Prompts\error;

class SocialAuthController extends Controller
{
    /**
     * Редирект пользователя на страницу авторизации Google
     *
     * @return RedirectResponse
     */
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Обработка callback от Google
     *
     * @return RedirectResponse
     */

    public function handleGoogleCallback():RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            $rawPassword = Str::random(12);

            $user = User::updateOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name'              => $googleUser->getName(),
                    'email_verified_at' => Carbon::now(),
                    'google_id'         => $googleUser->id,
                    'avatar'            => $googleUser->avatar,
                    'password'          => Hash::make($rawPassword),
                    'provider_token'    => $googleUser->token
                ]
            );

            Auth::login($user);

            return redirect()->route('home');

        } catch (\Exception $e) {
            return redirect()->route('home')->withErrors('message', 'Что-то пошло не так!');
        }
    }
}
