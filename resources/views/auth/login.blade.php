@extends('layouts.guest')
@section('title', 'Вход')

@section('form')
    <h1 class="font-display text-3xl font-extrabold">С возвращением</h1>
    <p class="mt-2 text-ink-600">Войдите, чтобы продолжить обучение.</p>

    @if($errors->any())
        <div class="mt-5 rounded-2xl border border-coral-400/40 bg-coral-400/10 px-4 py-3 text-sm text-ink-900">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
        @csrf
        <div>
            <label class="label">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus class="input">
        </div>
        <div>
            <label class="label">Пароль</label>
            <input type="password" name="password" required class="input">
        </div>
        <label class="flex items-center gap-2 text-sm text-ink-600">
            <input type="checkbox" name="remember" class="h-4 w-4 accent-cyan-500"> Запомнить меня
        </label>
        <button class="btn btn-signal w-full py-3 text-base">Войти</button>
    </form>

    <p class="mt-6 text-center text-sm text-ink-600">
        Нет аккаунта? <a href="{{ route('register') }}" class="font-semibold text-cyan-500 hover:underline">Зарегистрироваться</a>
    </p>
@endsection
