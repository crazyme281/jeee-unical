@extends('layouts.base')

@section('title', 'My Account')
@section('profile.active', 'active')

@section('content')
    <section class="">
        <div class="container">
            <div class="col-md-12">
                <div class="title">
                    <h3>
                        My Account
                    </h3>
                </div>
            </div>

            <form class="form" method="POST" action="{{ route('account.update', '') }}">
                {{ csrf_field() }}
                {{ method_field('PUT') }}

                <div class="row">
                    <div class="col-md-6">
                        <div class="card card-signup">
                            <div class="content">
                                <div class="input-group">
										<span class="input-group-addon">
											<i class="material-icons">email</i>
										</span>
                                    <div class="form-group is-empty {{ $errors->has('email') ? ' has-error' : '' }}">
                                        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') ? old('email') : $profile['email'] }}" placeholder="Email..." required>
                                        <span class="material-input"></span>
                                    </div>

                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="input-group">
                                    <div class="form-group is-empty {{ $errors->has('corporate') ? ' has-error' : '' }}">
                                        <div class="checkbox">
                                            <label>
                                                <input id="corporate" type="checkbox" name="corporate" {{ (old('corporate') ? old('corporate') : $profile['corporate']) == '1' ? 'checked' : ''}}>
                                                Corporate Account (ONLY for an institution or hospital. Please leave UNCHECKED if individual.)
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="input-group">
										<span class="input-group-addon">
											<i class="material-icons">lock_outline</i>
										</span>
                                    <div class="form-group is-empty {{ $errors->has('password') ? ' has-error' : '' }}">
                                        <input id="password" type="password" class="form-control" name="password" placeholder="New Password or Leave Empty...">
                                        <span class="material-input"></span>
                                    </div>
                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="input-group">
										<span class="input-group-addon">
											<i class="material-icons">lock_outline</i>
										</span>
                                    <div class="form-group is-empty">
                                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="Confirm New Password...">
                                        <span class="material-input"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pull-right">
                            <button type="submit" class="btn btn-primary">
                                Update
                            </button>

                            <a class="btn btn-link" href="{{ route('home') }}">
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection