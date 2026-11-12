@extends('layouts.base')

@section('title', 'Subscription')
@section('subscription.active', 'active')

@section('content')
    <section class="section">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div class="title">
                        <h3>
                            Subscription
                        </h3>
                    </div>

                    <p>
                    Subscription to the journals including postage is as follows:

Foreign institutions USD100 per issue

Local institutions NGN10,000 per issue

Individual subscribers USD50 or NGN7000.00. per issue.

All journals can be ordered through this Global Journal website.

The prices of the books published by Bachudo Science Co. Ltd are as listed under products. Orders can also be placed in this website.
                    </p>
                    <p>
                        
                    </p>
                </div>
            </div>

            <div class="row">
                <div class="subscribe col-md-8">
                    @auth
                    @if(Auth::user()->subscribedToCurrentVolume())
                        <p class="text-info">
                            <h5>NOTE: You have an active subscription for the current volume.</h5>
                        </p>
                        {{--<button class="btn btn-danger hide" type="button" id="cancel-button"> Cancel AutoSubscription </button>--}}
                    @endif

                    <p>
                        <a href="{{ route('subscriptions') }}">Click here</a> to view all your subscriptions.
                    </p>
                    @if ($volumes->isEmpty())
                        <p>No issues are available for subscription yet.</p>
                    @elseif (!config('services.paystack.public'))
                        <p>Online payments are not set up on this copy of the site.</p>
                    @else
                    <form id="payment">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group label-floating is-empty">
                                    <label for="sel1">Choose Volume:</label>
                                    <select name="volume" class="form-control" id="sel1">
                                        @foreach($volumes as $volume)
                                            <option value="{{ $volume->volume }}">Volume {{ $volume->volume }} ({{ $volume->year }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group label-floating is-empty">
                                    <label for="sel1">Select Issue:</label>
                                    <select name="issue" class="form-control" id="sel1">
                                        <option value="0">All Issues</option>
                                        <option value="1">Issue 1</option>
                                        <option value="2">Issue 2</option>
                                        <option value="3">Issue 3</option>
                                        <option value="4">Issue 4</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4 text-center">
                                <button id="pay" type="submit" class="btn btn-primary btn-raised">
                                    Proceed to Payment
                                    <div class="ripple-container"></div>
                                </button>
                            </div>
                        </div>
                    </form>
                    @endif
                    @else
                        <a class="btn btn-secondary" href="{{ route('sub') }}">Login to Subscribe</a>
                    @endauth
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <p>
                        LOCAL AUTHORS
                    </p>
                    <p>
                        Payment can be made into the following Global Journal Account:
                    </p>
                    <p>
                        Account Name: Global Journal of Pure and Applied Sciences
                    </p>
                    <p>
                        Account Number: 2000551087
                    </p>
                    <p>
                        Bank: First Bank of Nigeria Calabar
                    </p>
                    <br>
                    <p>
                        FOREIGN AUTHORS, <BR></BR>Payment should be made as money transfer to the account below:
                    </p>
                    <p>
                        Account Name: Global Journal of Pure and Applied Sciences
                    </p>
                    <p>
                        Account No: 2006009432 ( First Bank of Nigeria Plc, Calabar Road Branch, Calabar, Nigeria.)
                    </p>
                    <p>
                        Swift Code: SCBLUS33 OR FBNINGLA
                    </p>
                    <p>
                        Sort Code: 011071206
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('scripts')
    @if (config('services.paystack.public'))
    <script src="https://js.paystack.co/v1/inline.js"></script>
    @endif
    @auth
    <script>
        $('#cancel-button').click(function () {
            var $this = $(this);

            $this.text('Canceling...');
            $this.prop('disabled', true);

            $.post('{{ route("sub.cancel") }}', {
                reference: response.reference
            }).done(function (data) {
                $this.text('Canceled');
                success('Autorenewal cancelled successfully');
            }).fail(function (data) {
                $this.text('Cancel');
                $this.prop('disabled', false);
                success('Unable to unsubscribe. Please try again later.');
            });
        });

        $('#payment').submit(function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            var volume = formData.get('volume');
            var issue = formData.get('issue');
            console.log(issue);

            var amount = '{{ issue_price() }}';
            // If all Issues
            if(issue == 0){
                if({{ (int) Auth()->user()->corporate }} === 1){
                    amount = '{{ volume_price_multi() }}';
                }
                else{
                    amount = '{{ volume_price() }}';
                }
            }

            var $this = $(this);
            var handler = PaystackPop.setup({
                key: '{{ config("services.paystack.public") }}',
                email: '{{ Auth()->user()->email }}',
                amount: amount,
                metadata: {
                    custom_fields: [
                        {
                            display_name: "Mobile Number",
                            variable_name: "mobile_number",
                            value: "+2348012345678"
                        }
                    ]
                },
                callback: function(response){
                    $this.text('Subscribing...');
                    $this.prop('disabled', true);

                    console.log(response.reference);
                    $.post('{{ route("sub") }}', {
                        reference: response.reference,
                        volume: volume,
                        issue: issue,
                        _token: '{{ csrf_token() }}'
                    }).done(function (data) {
                        $this.text('subscribed');
                        success('Subscription successful!');
                    }).fail(function (data) {
                        $this.text('Subscribe');
                        $this.prop('disabled', false);
                        success('Subscription failed. Please try again later.');
                    });
                },
                onClose: function(){
                    console.log("Cancelled");
                    error("Subscription cancelled");
                }
            });
            handler.openIframe();
        });

    </script>
    @endauth
@endsection
