@extends('layouts.login')
@section('content')
<h2>Reset Password</h2>
<form class="pt-4" action="{{route('password.email')}}" method="post" id="login-form" autocomplete="off">
    @csrf
    <div class="form-group">
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible" id="successMessage">
                {{ session()->get('success') }}
                {{-- <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> --}}
            </div>
        @endif
        <input type="email" class="form-control form-control-lg {{$errors->first('email')?'is-invalid':''}}"  placeholder="Email" name="email" />
        <span class="{{$errors->first('email')?'error invalid-feedback':''}}">{{$errors->first('email')}}</span>
    </div>
    
    <div class="form-group">
        <button class="auth-form-btn">Submit</button>
    </div>

    <div class="mt-1 text-center">
        <a href="{{ route('login') }}" class="auth-link text-black">Back To Login</a>
    </div>
</form>
@endsection
@push('js')
<script>
    $(document).ready(function(){
        $('#login-form').validate({
            rules:{
                email1:{
                    required: true,
                    email:true,
                    maxlength: 100
                }                                
            },
            errorElement: "span",
            errorPlacement: function (error, element) {
                error.addClass("invalid-feedback");
                element.closest(".form-group").append(error);
            },
            highlight: function (element, errorClass, validClass) {
                $('.please-wait').hide();
                $(element).addClass("is-invalid");
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("is-invalid");
            },
        });
    });
</script>
@endpush