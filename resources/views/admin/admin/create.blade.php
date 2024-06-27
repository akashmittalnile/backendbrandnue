@extends('layouts.app')
@section('content')
<div class="main-panel">
    <div class="heading-section">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h4 class="heading-title">Add New Admin</h4>
            </div>
            <div class="btn-option-info">
                <a href="{{ route('admin.admin.list') }}" class="btn-ye">Back</a>
            </div>
        </div>
    </div>
    <div class="di-section"> 
        <div class="add-form-info">
            <form class="" method="post" enctype="multipart/form-data" id="user-form">
            @csrf
            <div class="upload-video-form">
                <div class="filter-info">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>First Name</label>
                                <input type="text" name="first_name" class="form-control {{$errors->first('first_name')?'is-invalid':''}}" placeholder="First Name" value="{{old('first_name')}}" />
                                <span class="{{$errors->first('first_name')?'error invalid-feedback':''}}">{{$errors->first('first_name')}}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Last Name</label>
                                <input type="text" name="last_name" class="form-control {{$errors->first('last_name')?'is-invalid':''}}" placeholder="Last Name" value="{{old('last_name')}}"/>
                                <span class="{{$errors->first('last_name')?'error invalid-feedback':''}}">{{$errors->first('last_name')}}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control {{$errors->first('email')?'is-invalid':''}}" placeholder="Email" value="{{old('email')}}"/>
                                <span class="{{$errors->first('email')?'error invalid-feedback':''}}">{{$errors->first('email')}}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="text" name="phone" class="form-control {{$errors->first('phone')?'is-invalid':''}}" placeholder="Phone" value="{{old('phone')}}"/>
                                <span class="{{$errors->first('phone')?'error invalid-feedback':''}}">{{$errors->first('phone')}}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Gender</label>
                                <select name="gender" class="form-control {{$errors->first('gender')?'is-invalid':''}}">
                                    <option value="">Select</option>
                                    @foreach(config('constant.genders') as $gender)
                                        <option value="{{$gender}}" @if(old('gender')==$gender) selected @endif>{{$gender}}</option>
                                    @endforeach
                                </select>
                                <span class="{{$errors->first('gender')?'error invalid-feedback':''}}">{{$errors->first('gender')}}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Date Of Birth</label>
                                <input type="date" name="dob" class="form-control {{$errors->first('dob')?'is-invalid':''}}" placeholder="Date Of Birth" value="{{old('dob')}}"/>
                                <span class="{{$errors->first('dob')?'error invalid-feedback':''}}">{{$errors->first('dob')}}</span>
                            </div>
                        </div>
                                                                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Status</label>
                                <select name="status" class="form-control">
                                    <option value="">Select</option>
                                    @foreach(status_array() as $key=>$row)
                                        <option value="{{$key}}" @if($key==old('status')) selected @endif>{{$row}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="" class="form-label">Profile Image</label>
                                <input type="file" class="form-control col-md-4 p-2" name="profile_image"  accept='image/*'>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" name="password" id="password" class="form-control {{$errors->first('password')?'is-invalid':''}}" placeholder="Password" />
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
                                <button class="btn-publish" type="submit">Submit</button>
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
        jQuery.validator.addMethod(
            "onlyimages",
            function (value, element) {
                if (this.optional(element) || !element.files || !element.files[0]) {
                    return true;
                } else {
                    var fileType = element.files[0].type;
                    var isImage = /^(image)\//i.test(fileType);
                    return isImage;
                }
            },
            'Sorry, we can only accept image file.'
        );
        $('#user-form').validate({
            rules:{
                first_name:{
                    required: true,                    
                    maxlength: 50
                },
                last_name:{
                    required:true,
                    maxlength:50
                },
                email:{
                    required:true,
                    email:true,
                    maxlength:191
                },
                phone:{
                    required: true,
                    maxlength:12,
                    minlength:10,
                },
                gender:{
                    required:true
                },
                dob:{
                    required:true
                },
                password:{
                    required:true,
                    maxlength:191
                },
                password_confirmation:{
                    required:true,
                    maxlength:191,
                    equalTo:'#password'
                },
                status:{
                    required: true
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