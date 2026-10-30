@extends('layouts.jetsi')

@section('title', 'Register')

@section('content')

  <div class="page-head">
    <div class="container"><h1>Register</h1></div>
  </div>

  <main id="main">
    <section class="section">
      <div class="container">
        <div class="auth-card card">
          <p class="meta">Create an account to submit manuscripts and subscribe.</p>

          <a class="btn btn-secondary btn-block" href="{{ url('/auth/google') }}">Sign up with Google</a>
          <div class="auth-divider"><span>or</span></div>

          <form class="auth-form" method="POST" action="{{ route('register') }}">
            {{ csrf_field() }}

            <div class="field-row">
              @php $has = $errors->has('first_name'); @endphp
              <div class="field{{ $has ? ' has-error' : '' }}">
                <label for="first_name">First name</label>
                <input id="first_name" name="first_name" type="text" value="{{ old('first_name') }}" autocomplete="given-name" required autofocus{!! $has ? ' aria-invalid="true" aria-describedby="first_name-error"' : '' !!}>
                @if ($has)<p class="field-error" id="first_name-error">{{ $errors->first('first_name') }}</p>@endif
              </div>

              @php $has = $errors->has('last_name'); @endphp
              <div class="field{{ $has ? ' has-error' : '' }}">
                <label for="last_name">Last name</label>
                <input id="last_name" name="last_name" type="text" value="{{ old('last_name') }}" autocomplete="family-name" required{!! $has ? ' aria-invalid="true" aria-describedby="last_name-error"' : '' !!}>
                @if ($has)<p class="field-error" id="last_name-error">{{ $errors->first('last_name') }}</p>@endif
              </div>
            </div>

            @php $has = $errors->has('email'); @endphp
            <div class="field{{ $has ? ' has-error' : '' }}">
              <label for="email">Email</label>
              <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required{!! $has ? ' aria-invalid="true" aria-describedby="email-error"' : '' !!}>
              @if ($has)<p class="field-error" id="email-error">{{ $errors->first('email') }}</p>@endif
            </div>

            @php $has = $errors->has('password'); @endphp
            <div class="field{{ $has ? ' has-error' : '' }}">
              <label for="password">Password</label>
              <input id="password" name="password" type="password" autocomplete="new-password" required{!! $has ? ' aria-invalid="true" aria-describedby="password-error"' : '' !!}>
              @if ($has)<p class="field-error" id="password-error">{{ $errors->first('password') }}</p>@endif
            </div>

            <div class="field">
              <label for="password-confirm">Confirm password</label>
              <input id="password-confirm" name="password_confirmation" type="password" autocomplete="new-password" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Register</button>
          </form>

          <p class="auth-foot">Already have an account? <a href="{{ route('login') }}">Login</a></p>
        </div>
      </div>
    </section>
  </main>

@endsection
