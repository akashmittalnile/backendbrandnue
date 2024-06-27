@extends('layouts.app')
@section('content')
<div class="main-panel">
    <div class="heading-section">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h4 class="heading-title">Edit Notification</h4>
            </div>
            <div class="btn-option-info">
                <a href="{{ route('admin.notification.list') }}" class="btn-ye">Back</a>
            </div>
        </div>
    </div>
    <div class="di-section">
        <div class="add-form-info">
            <form class="" method="post" enctype="multipart/form-data" id="notification-form">
                @csrf
            <div class="upload-video-form">
                <div class="notification-info">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" name="title" class="form-control {{$errors->first('title')?'is-invalid':''}}" placeholder="Title" value="{{$data->title}}" />
                                <span class="{{$errors->first('title')?'error invalid-feedback':''}}">{{$errors->first('title')}}</span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" class="form-control {{$errors->first('description')?'is-invalid':''}}" placeholder="Description" >{{$data->data}}</textarea>
                                <span class="{{$errors->first('description')?'error invalid-feedback':''}}">{{$errors->first('description')}}</span>
                            </div>
                        </div>
                        {{-- <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Status</label>
                                <select name="status" class="form-control">
                                    <option value="">Select</option>
                                    @foreach(status_array() as $key=>$row)
                                        <option value="{{$key}}" @if($key==$data->status) selected @endif>{{$row}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="" class="form-label">Select Plan</label>
                                <select name="plan" class="form-control">
                                    <option value="">Select</option>
                                    @forelse($plans as $plan)
                                        <option value="{{$plan->id}}" @if($plan->id==$data->subscription_plan_id) selected @endif>{{$plan->name}}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Image</label>
                                <input type="file" class="form-control col-md-4 p-2" name="image" accept='image/*'>
                                @if($data->image)
                                <img src="{{ asset($data->image) }}" class="img-thumbnail" style="height: 100px;">
                                @endif
                            </div>
                        </div>
                        
                    </div>
                </div>
                <div class="add-form-checkbox">
                    <div class="form-checkbox">
                        <input type="checkbox" value="Y" id="Mark as publish" name="status" @if($data->status=='Y') checked @endif />
                        <label for="Mark as publish">Mark as publish</label>
                    </div>
                </div>
                <div class="add-form-btn">
                    <button class="btn-publish" type="submit">Update</button>
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
        $('#notification-form').validate({
            rules:{
                title:{
                    required: true,                    
                    maxlength: 191
                },
                description:{
                    required:true,
                    maxlength:2000
                },
                image:{
                    onlyimages:true
                },
                plan:{
                    required:true,
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