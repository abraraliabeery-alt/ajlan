@extends('admin.layout')
@section('title', __('admin.login'))
@section('content')
<div class="login-box">
    <div class="card">
        <h1>{{ __('admin.panel') }}</h1>
        @if($errors->any())
            <div class="alert-err">{{ $errors->first() }}</div>
        @endif
        <form method="post" action="{{ route('admin.login') }}">
            @csrf
            <label for="password">{{ __('admin.password') }}</label>
            <input type="password" id="password" name="password" required autofocus>
            <button class="btn" type="submit">{{ __('admin.login') }}</button>
        </form>
        <div class="admin-lang" style="margin-top:18px;justify-content:center">
            @foreach(config('app.supported_locales') as $loc)
                <a href="{{ route('admin.lang', $loc) }}" class="{{ app()->getLocale() === $loc ? 'active' : '' }}">{{ strtoupper($loc) }}</a>
            @endforeach
        </div>
    </div>
</div>
@endsection
