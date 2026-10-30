@extends('layouts.base')

@section('title', 'My Profile')
@section('profile.active', 'active')

@section('content')
    <section class="">
        <div class="container">
            <div class="col-md-12">
                <div class="title">
                    <h3>
                        My Profile
                    </h3>
                </div>
            </div>

            <form class="form" method="POST" action="{{ route('profile', '') }}">
                {{ csrf_field() }}
                {{ method_field('PUT') }}

                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-addon">
                                First Name
                            </span>
                            <div class="form-group is-empty {{ $errors->has('first_name') ? ' has-error' : '' }}">
                                <input id="first_name" type="text" class="form-control" name="first_name" value="{{ old('first_name') ?? $profile['first_name'] }}" placeholder="First Name" required autofocus>
                                <span class="material-input"></span>
                            </div>

                            @if ($errors->has('first_name'))
                                <span class="help-block">
                                <strong>{{ $errors->first('first_name') }}</strong>
                            </span>
                            @endif
                        </div>

                        <div class="input-group">
                            <span class="input-group-addon">
                                Last Name
                            </span>
                            <div class="form-group is-empty {{ $errors->has('last_name') ? ' has-error' : '' }}">
                                <input id="last_name" type="text" class="form-control" name="last_name" value="{{ old('last_name') ?? $profile['last_name'] }}" placeholder="Last Name..." required>
                                <span class="material-input"></span>
                            </div>

                            @if ($errors->has('last_name'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('last_name') }}</strong>
                                    </span>
                            @endif
                        </div>

                        <div class="input-group">
                            <span class="input-group-addon">
                                Institution
                            </span>
                            <div class="form-group is-empty {{ $errors->has('institution') ? ' has-error' : '' }}">
                                <input id="institution" type="text" class="form-control" name="institution" value="{{ old('institution') ?? $profile['institution'] }}" placeholder="Institution..." required>
                                <span class="material-input"></span>
                            </div>

                            @if ($errors->has('institution'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('institution') }}</strong>
                                    </span>
                            @endif
                        </div>

                        <div class="input-group">
                            <span class="input-group-addon">
                                Department
                            </span>
                            <div class="form-group is-empty {{ $errors->has('department') ? ' has-error' : '' }}">
                                <input id="department" type="text" class="form-control" name="department" value="{{ old('department') ?? $profile['department'] }}" placeholder="Department...">
                                <span class="material-input"></span>
                            </div>

                            @if ($errors->has('department'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('department') }}</strong>
                                    </span>
                            @endif
                        </div>

                        <div class="input-group">
                            <span class="input-group-addon">
                                Position
                            </span>
                            <div class="form-group is-empty {{ $errors->has('position') ? ' has-error' : '' }}">
                                <input id="position" type="text" class="form-control" name="position" value="{{ old('position') ?? $profile['position'] }}" placeholder="Position...">
                                <span class="material-input"></span>
                            </div>

                            @if ($errors->has('position'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('position') }}</strong>
                                    </span>
                            @endif
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