@extends('layouts.admin')
@section('title', 'Настройки')
@section('heading', 'Настройки платформы')

@section('content')
<form method="POST" action="{{ route('admin.settings.update') }}" class="max-w-2xl">
    @csrf @method('PUT')

    <div class="card p-6">
        <h2 class="font-display text-lg font-bold">Общие</h2>
        <div class="mt-4 space-y-4">
            <div>
                <label class="label">Название платформы *</label>
                <input name="site_name" value="{{ $settings['site_name'] }}" required class="input">
            </div>
            <x-i18n-field name="tagline" label="Слоган" :translations="$translatable['tagline']"
                hint="Показывается на главной, в подвале и на страницах входа." />
            <div>
                <label class="label">Email поддержки</label>
                <input type="email" name="support_email" value="{{ $settings['support_email'] }}" class="input">
            </div>
            <x-i18n-field name="footer_note" label="Текст в подвале" :translations="$translatable['footer_note']" />
        </div>
    </div>

    <div class="card mt-6 p-6">
        <h2 class="font-display text-lg font-bold">Сертификаты</h2>
        <p class="mt-1 text-sm text-ink-500">Эти данные отображаются в выдаваемых сертификатах.</p>
        <div class="mt-4 space-y-4">
            <div>
                <label class="label">Подписант (ФИО)</label>
                <input name="certificate_signer" value="{{ $settings['certificate_signer'] }}" class="input">
            </div>
            <div>
                <label class="label">Должность подписанта</label>
                <input name="certificate_signer_title" value="{{ $settings['certificate_signer_title'] }}" class="input">
            </div>
        </div>
    </div>

    <div class="mt-6 flex justify-end">
        <button class="btn btn-signal px-8">Сохранить настройки</button>
    </div>
</form>
@endsection
