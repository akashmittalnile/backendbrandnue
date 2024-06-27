@extends('layouts.app')
@section('content')
<div class="main-panel">
    <div class="heading-section">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h4 class="heading-title">Change Password</h4>
            </div>
            <div class="btn-option-info">
                {{-- <a href="{{ route('admin.user.list') }}" class="btn-ye">Back</a> --}}
            </div>
        </div>
    </div>
    <div class="di-section">
        <div class="add-form-info">
            <script src="https://sandbox.web.squarecdn.com/v1/square.js"></script>
        	<!-- Provide a target element for the Card form -->
            <form id="payment-form">
              <div id="card-container"></div>
              <button id="card-button" type="button">Pay</button>
            </form>
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
                      console.log(`Payment token is ${result.token}`);
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

        </div>
    </div>
</div>
@endsection
@push('css')
<link rel="stylesheet" href="{{ asset('admin/css/recipe.css') }}">
@endpush
@push('js')
<script src="{{asset('plugins/js/jquery.validate.min.js')}}"></script>
<script>
    
@endpush