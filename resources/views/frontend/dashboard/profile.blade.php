@extends('layouts.app')
@section('title', 'Профиль')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">
    <h1 class="font-display text-3xl font-extrabold">Настройки профиля</h1>

    {{-- Profile --}}
    <div class="card mt-8 p-6 sm:p-8">
        <h2 class="font-display text-lg font-bold">Личные данные</h2>
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-5 space-y-4">
            @csrf
            @method('PUT')
            <div class="flex items-center gap-4">
                <div class="grid h-16 w-16 shrink-0 place-items-center overflow-hidden rounded-2xl bg-ink-950 text-lg font-bold text-signal-300">
                    @if($user->avatar)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($user->avatar) }}" class="h-full w-full object-cover" alt="">
                    @else
                        {{ $user->initials }}
                    @endif
                </div>
                <div class="flex-1">
                    <label class="label">Аватар</label>
                    <input type="file" name="avatar" accept="image/*" class="input">
                </div>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="label">Имя</label>
                    <input name="first_name" value="{{ old('first_name', $user->first_name) }}" class="input" required>
                </div>
                <div>
                    <label class="label">Фамилия</label>
                    <input name="last_name" value="{{ old('last_name', $user->last_name) }}" class="input" required>
                </div>
            </div>
            <div>
                <label class="label">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="input" required>
            </div>
            <div>
                <label class="label">Заголовок (роль)</label>
                <input name="headline" value="{{ old('headline', $user->headline) }}" placeholder="Например: Будущий инженер" class="input">
            </div>
            <div>
                <label class="label">О себе</label>
                <textarea name="bio" rows="3" class="input">{{ old('bio', $user->bio) }}</textarea>
            </div>
            <div>
                <label class="label">{{ __('app.profile_language') }}</label>
                <select name="locale" class="input">
                    @foreach(locales() as $code => $meta)
                        <option value="{{ $code }}" @selected(($user->locale ?? current_locale()) === $code)>{{ $meta['flag'] }} {{ $meta['native'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end"><button class="btn btn-signal">{{ __('app.save') }}</button></div>
        </form>
    </div>

    {{-- Password --}}
    <div class="card mt-6 p-6 sm:p-8">
        <h2 class="font-display text-lg font-bold">Смена пароля</h2>
        <form method="POST" action="{{ route('profile.password') }}" class="mt-5 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="label">Текущий пароль</label>
                <input type="password" name="current_password" class="input" required>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="label">Новый пароль</label>
                    <input type="password" name="password" class="input" required>
                </div>
                <div>
                    <label class="label">Повторите пароль</label>
                    <input type="password" name="password_confirmation" class="input" required>
                </div>
            </div>
            <div class="flex justify-end"><button class="btn btn-ink">Обновить пароль</button></div>
        </form>
    </div>
</div>
@endsection
