@extends('layouts.base')

@section('title', 'My Subscriptions')
@section('profile.active', 'active')

@section('content')
    <section class="">
        <div class="container">
            <div class="col-md-12">
                <div class="title">
                    <h3>
                        My Subscriptions
                    </h3>
                </div>
            </div>

            <div class="col-xs-8 col-sm-4">
                @if(Auth::user()->subscribedToCurrentVolume())
                    <p class="text-info">
                    <h5>NOTE: You have an active subscription for the current volume.</h5>
                    </p>
                    {{--<button class="btn btn-danger hide" type="button" id="cancel-button"> Cancel AutoSubscription </button>--}}
                @else
                    <a href="{{ route('sub') }}" class="btn btn-primary btn-lg">
                        <i class="material-icons">payment</i> Subscribe for the Current Issue
                        <div class="ripple-container"></div>
                    </a>
                @endif
            </div>

            <table class="table">
                <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th>Volume</th>
                    <th>Issue</th>
                    <th>Amount</th>
                    <th>Paid At</th>
                </tr>
                </thead>
                <tbody>
                @if($subscriptions->count() > 0)
                    @foreach($subscriptions as $subscription)
                        <tr>
                            <td class="text-center">1</td>
                            <td>{{ $subscription['volume'] }}</td>
                            <td>{{ $subscription['issue'] == 0 ? 'All' : $subscription['issue'] }}</td>
                            <td>{{ $subscription['amount'] }}</td>
                            <td>{{ $subscription['created_at'] }}</td>
                    @endforeach
                @else
                    <tr><td><h5>No records</h5></td></tr>
                @endif

                </tbody>
            </table>
        </div>
    </section>
@endsection