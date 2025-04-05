<?php

namespace App\Http\Controllers\AuthController;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!Auth::guest()) {
            return redirect(route('index'));
        }

        return view('auth.register');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RegisterRequest $request)
    {
        if (!Auth::guest()) {
            return redirect(route('index'));
        }
        User::makeUser($request);

        return redirect(route('login'), 201);
    }
}
