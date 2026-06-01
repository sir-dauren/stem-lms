@extends('layouts.guest')
@section('title', 'Регистрация')

@section('form')
    <h1 class="font-display text-3xl font-extrabold">Создайте аккаунт</h1>
    <p class="mt-2 text-ink-600">Бесплатный доступ к курсам, тестам и сертификатам.</p>

    @if($errors->any())
        <div class="mt-5 rounded-2xl border border-coral-400/40 bg-coral-400/10 px-4 py-3 text-sm text-ink-900">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
        @csrf
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="label">Имя</label>
                <input name="first_name" value="{{ old('first_name') }}" required autofocus class="input">
            </div>
            <div>
                <label class="label">Фамилия</label>
                <input name="last_name" value="{{ old('last_name') }}" required class="input">
            </div>
        </div>
        <div>
            <label class="label">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="input">
        </div>
        <div>
            <label class="label">Пароль</label>
            <input type="password" name="password" required class="input">
            <p class="mt-1 text-xs text-ink-400">Минимум 8 символов, буквы и цифры.</p>
        </div>
        <div>
            <label class="label">Повторите пароль</label>
            <input type="password" name="password_confirmation" required class="input">
        </div>
        <button class="btn btn-signal w-full py-3 text-base">Зарегистрироваться</button>
    </form>

    <p class="mt-6 text-center text-sm text-ink-600">
        Уже есть аккаунт? <a href="{{ route('login') }}" class="font-semibold text-cyan-500 hover:underline">Войти</a>
    </p>

    <p class="mt-2 text-center text-xs text-ink-400">Фамилия будет указана в ваших сертификатах.</p>
@endsection
