<?php
?>
@extends('layouts.auth-layout')
@section('content')
    <div class="container-fluid">
        <div class="row h-100">
            <div class="d-flex align-items-center justify-content-center">
                <div style="width: 360px; max-width: 100%;">

                    <form method="POST" action="{{ route('register.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input
                                id="email"
                                type="email"
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
                                value="{{ old('password') }}"
                            >
                            @error('password')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Повторите пароль</label>
                            <input
                                id="password_confirmation"
                                type="password"
                                class="form-control @error('password_confirmation') is-invalid @enderror"
                                name="password_confirmation"
                                placeholder="Повторите пароль"
                                value="{{ old('password_confirmation') }}"
                            >
                            @error('password_confirmation')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-success w-100 mb-3">
                            Регистрация
                        </button>

                        <div class="text-center">
                            <a href="{{ route('login') }}" class="text-decoration-none">Войти</a>
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
