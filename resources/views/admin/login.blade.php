@extends('admin.layout')
@section('title', 'Sign in')

@section('content')
<div class="login-shell">
  <div class="login-card">
    <div class="mark"><img src="{{ asset('images/founderimage4.jpeg') }}" alt="Victoria Love"></div>
    <h1>Welcome back.</h1>
    <p class="sub">Sign in to your Victoria Love admin dashboard.</p>

    @if ($errors->any())
      <div class="flash error" style="margin-bottom: 16px;">{{ $errors->first() }}</div>
    @endif
    @if (session('success'))
      <div class="flash success" style="margin-bottom: 16px;">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.login') }}" autocomplete="on">
      @csrf
      <label>
        <span>Email address</span>
        <input type="email" name="email" required autofocus value="{{ old('email') }}" autocomplete="username">
      </label>
      <label>
        <span>Password</span>
        <input type="password" name="password" required placeholder="••••••••" autocomplete="current-password">
      </label>
      <label class="remember">
        <input type="checkbox" name="remember" value="1"> Keep me signed in
      </label>
      <button type="submit">Sign in</button>
    </form>

    <p class="fine">Protected area · Authorized access only</p>
  </div>
</div>
@endsection
