@extends('layouts.app')
@section('content')
<div class="main-panel">
    <div class="heading-section">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h4 class="heading-title">Profile Update</h4>
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
                        <div class="col-md-12">
                            <div class="add-form-btn pull-right"> 
                                <button class="btn-publish edit-profile" type="button">Edit</button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control {{$errors->first('name')?'is-invalid':''}}" placeholder="Name" value="{{$user->name}}"/>
                                <span class="{{$errors->first('name')?'error invalid-feedback':''}}">{{$errors->first('name')}}</span>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email</label>
                                <span class="form-control bg-light" disabled />{{$user->email}}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="text" name="phone" class="form-control {{$errors->first('phone')?'is-invalid':''}}" placeholder="Phone" value="{{$user->phone}}"/>
                                <span class="{{$errors->first('phone')?'error invalid-feedback':''}}">{{$errors->first('phone')}}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Gender</label>
                                <select name="gender" class="form-control {{$errors->first('gender')?'is-invalid':''}}">
                                    <option value="">Select</option>
                                    @foreach(config('constant.genders') as $gender)
                                        <option value="{{$gender}}" @if($user->gender==$gender) selected @endif>{{$gender}}</option>
                                    @endforeach
                                </select>
                                <span class="{{$errors->first('gender')?'error invalid-feedback':''}}">{{$errors->first('gender')}}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Date Of Birth</label>
                                <input type="date" name="dob" class="form-control {{$errors->first('dob')?'is-invalid':''}}" placeholder="Date Of Birth" value="{{$user->dob}}"/>
                                <span class="{{$errors->first('dob')?'error invalid-feedback':''}}">{{$errors->first('dob')}}</span>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="" class="form-label">Profile Image</label>
                                <input type="file" class="form-control col-md-4 p-2" name="profile_image"  accept='image/*'>
                            </div>
                            @if(!empty($user->profile_image))
                            	<img src="{{ asset($user->profile_image) }}" alt="" class="img-thumbnail" style="height:100px;">
                            @endif
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
    	enableDisable();
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
                name:{
                    required: true,                    
                    maxlength: 50
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
       	$(document).on('click','.edit-profile',function(e){
       		enableDisable(false);
       		$('.add-form-btn').html(`
       			<button class="btn-publish cancel-edit bg-danger" type="button">Cancel</button>
       			<button class="btn-publish" type="submit">Update</button>
       		`);
       	});
       	$(document).on('click','.cancel-edit',function(){
       		enableDisable();
       		$('.add-form-btn').html(`
       			<button class="btn-publish edit-profile" type="button">Edit</button>
       		`);
       	});
        
    });
    
    function enableDisable(status=true){
    	$('#user-form').find('input, select').filter((e,ele)=> $(ele).attr('disabled',status));
    }
</script>
@endpush