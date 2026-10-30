@extends('layouts.jetsi')

@section('title', 'Login')

@section('content')

  <div class="page-head">
    <div class="container"><h1>Login</h1></div>
  </div>

  <main id="main">
    <section class="section">
      <div class="container">
        <div class="auth-card card">
          <p class="meta">Sign in to submit manuscripts and manage your subscriptions.</p>

          <a class="btn btn-secondary btn-block" href="{{ url('/auth/google') }}">Continue with Google</a>
          <div class="auth-divider"><span>or</span></div>

          <form class="auth-form" method="POST" action="{{ route('login') }}">
            {{ csrf_field() }}

            @php $has = $errors->has('email'); @endphp
            <div class="field{{ $has ? ' has-error' : '' }}">
              <label for="email">Email</label>
              <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus{!! $has ? ' aria-invalid="true" aria-describedby="email-error"' : '' !!}>
              @if ($has)<p class="field-error" id="email-error">{{ $errors->first('email') }}</p>@endif
            </div>

            @php $has = $errors->has('password'); @endphp
            <div class="field{{ $has ? ' has-error' : '' }}">
              <label for="password">Password</label>
              <input id="password" name="password" type="password" autocomplete="current-password" required{!! $has ? ' aria-invalid="true" aria-describedby="password-error"' : '' !!}>
              @if ($has)<p class="field-error" id="password-error">{{ $errors->first('password') }}</p>@endif
            </div>

            <div class="auth-links">
              <label class="check"><input type="checkbox" name="remember"{{ old('remember') ? ' checked' : '' }}> Remember me</label>
              <a href="{{ route('password.request') }}">Forgot your password?</a>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Login</button>
          </form>

          <p class="auth-foot">Don't have an account? <a href="{{ route('register') }}">Register</a></p>
        </div>
      </div>
    </section>
  </main>

@endsection
