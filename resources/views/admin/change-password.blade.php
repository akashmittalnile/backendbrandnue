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
        	@include('common.msg')
            <form class="" method="post" enctype="multipart/form-data" id="user-form">
            @csrf
            <div class="upload-video-form">
                <div class="filter-info">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control {{$errors->first('password')?'is-invalid':''}}" placeholder="Password" id="password" />
                                <span class="{{$errors->first('password')?'error invalid-feedback':''}}">{{$errors->first('password')}}</span>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control {{$errors->first('password_confirmation')?'is-invalid':''}}" placeholder="Confirm Password" />
                                <span class="{{$errors->first('password_confirmation')?'error invalid-feedback':''}}">{{$errors->first('password_confirmation')}}</span>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="add-form-btn pull-right"> 
                                <button class="btn-publish" type="submit">Update</button>
                            </div>
                        </div>
                        
                    </div>
                </div>
                
            </div>
            </form>
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
    $(document).ready(function(){
        $('#user-form').validate({
            rules:{
                password:{
                    required: true,                    
                    maxlength: 50
                },
                password_confirmation:{
                    required: true,
                    maxlength:50,
                    equalTo:"#password"
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
            invalidHandler: function(form, validator) {
                if (!validator.numberOfInvalids()) return;
                    $('html, body').animate({scrollTop: $(validator.errorList[0].element).offset().top-60}, 1000);
            },
        });
        /*---------------------------------------------------*/
       	        
    });
    
    
</script>
@endpush