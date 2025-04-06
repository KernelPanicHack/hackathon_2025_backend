@extends('layouts.auth-layout')
@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="container-fluid">
        <div class="row h-100">
            <div class="d-flex align-items-center justify-content-center">
                <div style="width: 360px; max-width: 100%;">
                    @error('invalidAuth')
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{$errors->first('invalidAuth')}}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @enderror
                    <form method="POST" action="{{ route('login.send-form') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input
                                id="email"
                                type="text"
                                class="form-control @error('email') is-invalid @enderror"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="Email"
                            >
                            @error('email')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Пароль</label>
                            <input
                                id="password"
                                type="password"
                                class="form-control @error('password') is-invalid @enderror"
                                name="password"
                                placeholder="Пароль"
                            >
                            @error('password')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="rememberMe"
                                       name="rememberMe" {{old('rememberMe', '') == 'on'?'checked':''}}>
                                <label class="form-check-label" for="rememberMe">
                                    Запомнить меня
                                </label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success w-100 mb-3">
                            Войти
                        </button>

                        <div class="text-center">
                            <a href="{{ route('register.create') }}" class="text-decoration-none">Регистрация</a>
                        </div>

                        <hr>
                        <a href="{{route('google.redirect')}}"
                           class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center">
                            <span class="fw-bold me-2">G</span> Войти через Google
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
