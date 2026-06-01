<!DOCTYPE html>
<html lang="ru" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $brandName) — @yield('subtitle', 'STEM образование онлайн')</title>
    <meta name="description" content="{{ setting('tagline', 'Учись строить будущее — наука, технологии, инженерия, математика.') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Sora:wght@500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex flex-col">
    @include('partials.header')

    <main class="flex-1">
        @include('components.flash')
        @yield('content')
    </main>

    @include('partials.footer')
    @stack('scripts')
</body>
</html>
