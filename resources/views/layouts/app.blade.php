<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Мой сайт')</title>
    <!-- Подключение Bootstrap CSS -->
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>
        body {
            width: 100%;
            background-image: url('/assets/images/background.png');
            background-repeat: no-repeat;
            background-size: cover;

        }
    </style>
</head>
<body>
<main class="container">
    @yield('content')
</main>

<!-- Подключение Bootstrap JS -->
<script src="/assets/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
