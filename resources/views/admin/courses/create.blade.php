@extends('layouts.admin')
@section('title', 'Новый курс')
@section('heading', 'Создание курса')

@section('content')
<form method="POST" action="{{ route('admin.courses.store') }}" enctype="multipart/form-data">
    @csrf
    @include('admin.courses._fields')
    <div class="mt-6 flex justify-end gap-3">
        <a href="{{ route('admin.courses.index') }}" class="btn btn-ghost">Отмена</a>
        <button class="btn btn-signal">Создать и перейти к программе</button>
    </div>
</form>
@endsection
