@extends('layouts.jetsi')

@section('title', 'Subscription')
@section('nav', 'subscribe')

@section('content')
  <section class="page-head">
    <div class="container">
      <h1>Subscription</h1>
    </div>
  </section>

  <main id="main" class="subscription-page section">
    <div class="container">
      <section class="subscription-pricing">
        <div class="section-head">
          <h2>Subscription to the journals including postage is as follows:</h2>
        </div>
        <div class="rate-grid">
          <article class="rate-card card">
            <span class="material-icons" aria-hidden="true">apartment</span>
            <p class="rate-amount">Foreign institutions<br>USD100 per issue</p>
          </article>
          <article class="rate-card card">
            <span class="material-icons" aria-hidden="true">location_city</span>
            <p class="rate-amount">Local institutions<br>NGN10,000 per issue</p>
          </article>
          <article class="rate-card card">
            <span class="material-icons" aria-hidden="true">person</span>
            <p class="rate-amount">Individual subscribers<br>USD50 or NGN7000.00. per issue.</p>
          </article>
        </div>
        <div class="subscription-note">
          <p>All journals can be ordered through this Global Journal website.</p>
          <p>The prices of the books published by Bachudo Science Co. Ltd are as listed under products. Orders can also be placed in this website.</p>
        </div>
      </section>

      <section class="payment-panel card">
        @auth
          @if(Auth::user()->subscribedToCurrentVolume())
            <p class="subscription-status"><span class="material-icons" aria-hidden="true">check_circle</span>NOTE: You have an active subscription for the current volume ({{ $volumes->last()->year }}).</p>
          @endif

          <p class="meta"><a href="{{ route('subscriptions') }}">Click here</a> to view all your subscriptions.</p>
          <form id="payment" class="subscription-form">
            <div class="field">
              <label for="subscription-volume">Choose Volume:</label>
              <select name="volume" id="subscription-volume">
                @foreach($volumes as $volume)
                  <option value="{{ $volume->volume }}">Volume {{ $volume->volume }} ({{ $volume->year }})</option>
                @endforeach
              </select>
            </div>
            <div class="field">
              <label for="subscription-issue">Select Issue:</label>
              <select name="issue" id="subscription-issue">
                <option value="0">All Issues</option>
                <option value="1">Issue 1</option>
                <option value="2">Issue 2</option>
                <option value="3">Issue 3</option>
                <option value="4">Issue 4</option>
              </select>
            </div>
            <button id="pay" type="submit" class="btn btn-primary">Proceed to Payment</button>
          </form>
          {{--<button class="btn btn-danger hide" type="button" id="cancel-button">Cancel AutoSubscription</button>--}}
        @else
          <a class="btn btn-primary" href="{{ route('sub') }}">Login to Subscribe</a>
        @endauth
      </section>

      <section class="bank-payment">
        <div class="bank-grid">
          <article class="bank-card card">
            <h3>LOCAL AUTHORS</h3>
            <p>Payment can be made into the following Global Journal Account:</p>
            <dl>
              <div><dt>Account Name:</dt><dd>Global Journal of Pure and Applied Sciences</dd></div>
              <div><dt>Account Number:</dt><dd>2000551087</dd></div>
              <div><dt>Bank:</dt><dd>First Bank of Nigeria Calabar</dd></div>
            </dl>
          </article>
          <article class="bank-card card">
            <h3>FOREIGN AUTHORS</h3>
            <p>Payment should be made as money transfer to the account below:</p>
            <dl>
              <div><dt>Account Name:</dt><dd>Global Journal of Pure and Applied Sciences</dd></div>
              <div><dt>Account No:</dt><dd>2006009432 ( First Bank of Nigeria Plc, Calabar Road Branch, Calabar, Nigeria.)</dd></div>
              <div><dt>Swift Code:</dt><dd>SCBLUS33 OR FBNINGLA</dd></div>
              <div><dt>Sort Code:</dt><dd>011071206</dd></div>
            </dl>
          </article>
        </div>
      </section>
    </div>
  </main>
@endsection
@section('scripts')
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('bower_components/remarkable-bootstrap-notify/dist/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('js/front.js') }}"></script>
    <script src="https://js.paystack.co/v1/inline.js"></script>
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
                if({{ Auth()->user()->corporate }} === 1){
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
                        issue: issue
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
