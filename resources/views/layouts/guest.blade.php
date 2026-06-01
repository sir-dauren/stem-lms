<!DOCTYPE html>
<html lang="ru" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Вход') — {{ $brandName }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Sora:wght@500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full">
    <div class="grid min-h-screen lg:grid-cols-2">
        {{-- Visual side --}}
        <div class="relative hidden bg-aurora p-12 text-white lg:flex lg:flex-col lg:justify-between">
            <div class="absolute inset-0 bg-blueprint opacity-40"></div>
            <a href="{{ route('home') }}" class="relative flex items-center gap-2.5">
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-signal-300 font-display text-lg font-extrabold text-ink-950">S</span>
                <span class="font-display text-xl font-extrabold">{{ $brandName }}</span>
            </a>
            <div class="relative">
                <h2 class="font-display text-4xl font-extrabold leading-tight">Учись строить <span class="text-signal-300">будущее</span></h2>
                <p class="mt-4 max-w-sm text-white/70">{{ setting('tagline', 'Наука, технологии, инженерия и математика — в одном месте. Курсы, тесты и сертификаты.') }}</p>
            </div>
            <p class="relative text-sm text-white/40">© {{ date('Y') }} {{ $brandName }}</p>
        </div>

        {{-- Form side --}}
        <div class="flex items-center justify-center p-6 sm:p-12">
            <div class="w-full max-w-md">
                <a href="{{ route('home') }}" class="mb-8 flex items-center gap-2.5 lg:hidden">
                    <span class="grid h-9 w-9 place-items-center rounded-xl bg-ink-950 font-display text-lg font-extrabold text-signal-300">S</span>
                    <span class="font-display text-xl font-extrabold">{{ $brandName }}</span>
                </a>
                @yield('form')
            </div>
        </div>
    </div>
</body>
</html>
