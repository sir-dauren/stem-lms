@php($navMenu = $headerMenu)
<header x-data="{ open: false }" class="sticky top-0 z-50 border-b border-paper-line bg-paper/90 backdrop-blur">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3.5 sm:px-6 lg:px-8">
        {{-- Brand --}}
        <a href="{{ route('home') }}" class="flex items-center gap-2.5">
            <span class="grid h-9 w-9 place-items-center rounded-xl bg-ink-950 font-display text-lg font-extrabold text-signal-300">S</span>
            <span class="font-display text-xl font-extrabold tracking-tight text-ink-950">{{ $brandName }}</span>
        </a>

        {{-- Desktop nav --}}
        <nav class="hidden items-center gap-1 lg:flex">
            <a href="{{ route('courses.index') }}" class="rounded-full px-3.5 py-2 text-sm font-semibold text-ink-700 hover:bg-white">{{ __('app.nav_courses') }}</a>
            <a href="{{ route('quizzes.index') }}" class="rounded-full px-3.5 py-2 text-sm font-semibold text-ink-700 hover:bg-white">{{ __('app.nav_tests') }}</a>
            <a href="{{ route('about') }}" class="rounded-full px-3.5 py-2 text-sm font-semibold text-ink-700 hover:bg-white">{{ __('app.nav_about') }}</a>
            @if($navMenu)
                @foreach($navMenu->items as $item)
                    <a href="{{ $item->url }}" target="{{ $item->target }}" class="rounded-full px-3.5 py-2 text-sm font-semibold text-ink-700 hover:bg-white">{{ $item->label }}</a>
                @endforeach
            @endif
        </nav>

        {{-- Right side: language switcher + auth --}}
        <div class="hidden items-center gap-2.5 lg:flex">
            {{-- Language switcher --}}
            <div x-data="{ l: false }" class="relative">
                <button @click="l = !l" class="flex items-center gap-1.5 rounded-full border border-paper-line bg-white px-3 py-2 text-sm font-semibold">
                    <span>{{ locale_flag() }}</span>
                    <span class="uppercase">{{ current_locale() }}</span>
                </button>
                <div x-show="l" @click.outside="l = false" x-transition x-cloak class="absolute right-0 mt-2 w-44 overflow-hidden rounded-2xl border border-paper-line bg-white py-1.5 shadow-xl">
                    @foreach(locales() as $code => $meta)
                        <a href="{{ route('locale.switch', $code) }}"
                           class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold hover:bg-paper {{ $code === current_locale() ? 'text-signal-500' : '' }}">
                            <span>{{ $meta['flag'] }}</span><span>{{ $meta['native'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            @guest
                <a href="{{ route('login') }}" class="btn btn-ghost">{{ __('app.nav_login') }}</a>
                <a href="{{ route('register') }}" class="btn btn-signal">{{ __('app.start_learning') }}</a>
            @else
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-ghost">{{ __('app.nav_admin') }}</a>
                @endif
                <div x-data="{ m: false }" class="relative">
                    <button @click="m = !m" class="flex items-center gap-2 rounded-full border border-paper-line bg-white py-1.5 pl-1.5 pr-3 text-sm font-semibold">
                        <span class="grid h-8 w-8 place-items-center rounded-full bg-ink-950 text-xs font-bold text-signal-300">{{ auth()->user()->initials }}</span>
                        <span class="max-w-[8rem] truncate">{{ auth()->user()->first_name }}</span>
                    </button>
                    <div x-show="m" @click.outside="m = false" x-transition x-cloak class="absolute right-0 mt-2 w-52 overflow-hidden rounded-2xl border border-paper-line bg-white py-1.5 shadow-xl">
                        <a href="{{ route('dashboard') }}" class="block px-4 py-2.5 text-sm font-semibold hover:bg-paper">{{ __('app.nav_dashboard') }}</a>
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2.5 text-sm font-semibold hover:bg-paper">{{ __('app.nav_profile') }}</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="block w-full px-4 py-2.5 text-left text-sm font-semibold text-coral-500 hover:bg-paper">{{ __('app.nav_logout') }}</button>
                        </form>
                    </div>
                </div>
            @endguest
        </div>

        {{-- Mobile toggle --}}
        <button @click="open = !open" class="grid h-10 w-10 place-items-center rounded-xl border border-paper-line bg-white lg:hidden">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
    </div>

    {{-- Mobile menu --}}
    <div x-show="open" x-cloak x-transition class="border-t border-paper-line bg-white px-4 py-3 lg:hidden">
        <a href="{{ route('courses.index') }}" class="block rounded-lg px-3 py-2.5 font-semibold hover:bg-paper">{{ __('app.nav_courses') }}</a>
        <a href="{{ route('quizzes.index') }}" class="block rounded-lg px-3 py-2.5 font-semibold hover:bg-paper">{{ __('app.nav_tests') }}</a>
        <a href="{{ route('about') }}" class="block rounded-lg px-3 py-2.5 font-semibold hover:bg-paper">{{ __('app.nav_about') }}</a>

        {{-- Mobile language switcher --}}
        <div class="mt-2 flex flex-wrap gap-2 border-t border-paper-line pt-3">
            @foreach(locales() as $code => $meta)
                <a href="{{ route('locale.switch', $code) }}" class="rounded-full border border-paper-line px-3 py-1.5 text-sm font-semibold {{ $code === current_locale() ? 'bg-ink-950 text-signal-300' : '' }}">{{ $meta['flag'] }} {{ strtoupper($code) }}</a>
            @endforeach
        </div>

        <div class="mt-2 flex gap-2 border-t border-paper-line pt-3">
            @guest
                <a href="{{ route('login') }}" class="btn btn-ghost flex-1">{{ __('app.nav_login') }}</a>
                <a href="{{ route('register') }}" class="btn btn-signal flex-1">{{ __('app.nav_register') }}</a>
            @else
                <a href="{{ route('dashboard') }}" class="btn btn-ghost flex-1">{{ __('app.nav_dashboard') }}</a>
                <form method="POST" action="{{ route('logout') }}" class="flex-1">@csrf<button class="btn btn-ink w-full">{{ __('app.nav_logout') }}</button></form>
            @endguest
        </div>
    </div>
</header>
