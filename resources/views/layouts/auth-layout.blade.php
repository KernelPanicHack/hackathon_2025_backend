<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="/assets/css/bootstrap.bundle.min.js"></script>
</head>
<body>
<div class="container-fluid h-100">
    <div class="row h-100">
        <div class="col-xl-7 d-none d-xl-flex bg-left-side d-flex align-items-center justify-content-center">
            <div class="d-flex align-items-center justify-content-center">
                <img src="/assets/img/card.png" alt="" srcset="" style="max-width: 700px">
            </div>
        </div>
        <div class="col-12 col-xl-5 align-items-center">
            <div class="logo">
                <img src="/assets/logo/logo-login.png" alt="">
            </div>
            @yield('content')
        </div>
    </div>
</div>

<style>
    body, html {
        height: 100%;
    }

    .bg-left-side {
        background-image: url('/assets/img/background-login.jpg');
        background-repeat: no-repeat;
        background-size: cover;
    }

    .logo {
        display: flex;
        justify-content: center;
        margin-top: 50px;
    }
</style>

</body>
</html>
