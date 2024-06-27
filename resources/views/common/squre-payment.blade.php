@extends('layouts.login')

@section('content')

{{-- <h2>Hello! let's get started</h2>

<p>Sign in to continue.</p> --}}

    {{-- <script src="https://sandbox.web.squarecdn.com/v1/square.js"></script> --}}
    <script src="https://web.squarecdn.com/v1/square.js"></script>

	<!-- Provide a target element for the Card form -->

    <h2>{{strtoupper($id->name)}}</h2>

    <p class="mb-4">

      @if(!empty($id->price))

        {!! config('constant.defaultCurrency') !!}{{$id->price}}

      @else

        Free

      @endif

    </p>

    <div id="card-container"></div>

    <button id="card-button" type="button" class="btn btn-secondary">Pay</button>

    <!-- Configure the Web Payments SDK and Card payment method -->

    <script type="text/javascript">

      async function main() {

        const payments = Square.payments("{{config('constant.square_application_id')}}","{{config('constant.squareLocationId')}}");

        const card = await payments.card();

        await card.attach('#card-container');



        async function eventHandler(event) {

          event.preventDefault();



          try {

            const result = await card.tokenize();

            if (result.status === 'OK') {

              console.log(result.token);

            }

          } catch (e) {

            console.error(e);

          }

        };



        const cardButton = document.getElementById('card-button');

        cardButton.addEventListener('click', eventHandler);

      }

      main();

    </script>

@endsection