@extends('layouts.login')
@section('content')
<h2>Hello! let's get started</h2>
<p>Sign in to continue.</p>
@include('common.msg')
<form class="pt-4" action="{{route('post.login')}}" method="post" id="login-form" autocomplete="off">
    @csrf
    <div class="form-group">
        <input type="email" class="form-control form-control-lg {{$errors->first('email')?'is-invalid':''}}"  placeholder="Email" name="email" />
        <span class="{{$errors->first('email')?'error invalid-feedback':''}}">{{$errors->first('email')}}</span>
    </div>
    <div class="form-group">
        <input type="password" class="form-control form-control-lg {{$errors->first('password')?'is-invalid':''}}" placeholder="Password" name="password" />
        <span class="{{$errors->first('password')?'error invalid-feedback':''}}">{{$errors->first('password')}}</span>
    </div>
    <div class="form-group">
        <button class="auth-form-btn">SIGN IN</button>
    </div>

    <div class="mt-1 text-center">
        <a href="{{ route('password.request') }}" class="auth-link text-black">Forgot password?</a>
    </div>
</form>
@endsection
@push('js')
<script>
    $(document).ready(function(){
        $('#login-form').validate({
            rules:{
                email:{
                    required: true,
                    email:true,
                    maxlength: 100
                },
                
                password:{
                    required:true,
                    maxlength:100
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