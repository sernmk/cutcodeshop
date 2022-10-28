@extends('layouts.auth')

@section('title', 'Сохранение нового пароля')

@section('content')

    <x-forms.auth-forms
        title="Сохранение нового пароля"
        action="{{ route('password.update') }}"
        method="POST"
    >

        @csrf

        <input type="hidden" name="token" value="{{ $token }}">
        <x-forms.text-input
            type="email"
            name="email"
            placeholder="E-mail"
            required="true"
            value="{{ request('email') }}"
            :isError="$errors->has('email')"
        ></x-forms.text-input>
        @error('email')
        <x-forms.error>
            {{ $message }}
        </x-forms.error>
        @enderror

        <x-forms.text-input
            type="password"
            name="password"
            placeholder="Пароль"
            required="true"
            :isError="$errors->has('password')"
        ></x-forms.text-input>
        @error('password')
        <x-forms.error>
            {{ $message }}
        </x-forms.error>
        @enderror

        <x-forms.text-input
            type="password"
            name="password_confirmation"
            placeholder="Повторите пароль"
            required="true"
            :isError="$errors->has('password_confirmation')"
        ></x-forms.text-input>
        @error('password_confirmation')
        <x-forms.error>
            {{ $message }}
        </x-forms.error>
        @enderror

        <x-forms.primary-button>
            Обновить пароль
        </x-forms.primary-button>

        <x-slot:socialAuth></x-slot:socialAuth>

        <x-slot:buttons></x-slot:buttons>

    </x-forms.auth-forms>

@endsection
