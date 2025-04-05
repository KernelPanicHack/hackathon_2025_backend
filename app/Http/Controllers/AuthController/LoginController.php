<?php

namespace App\Http\Controllers\AuthController;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function index()
    {
        if (!Auth::guest()) {
            return redirect(route('index'));
        }

        return view('auth.login');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(LoginRequest $request)
    {
        if (!Auth::guest()) {
            return redirect(route('index'));
        }
        $email = $request->get('email');
        $password = $request->get('password');
        $rememberMe = (boolean)$request->get('rememberMe');

        if (Auth::attempt(['email' => $email, 'password' => $password], $rememberMe)) {
            return redirect(route('index'));
        }
        return back()->withErrors([
            'invalidAuth' => 'Неверный логин или пароль',
        ])->withInput($request->toArray());
    }

    /**
     * @return \Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logout()
    {
        if (Auth::guest()) {
            return redirect(route('index'));
        }
        Auth::logout();
        return redirect(route('index'));
    }
}
