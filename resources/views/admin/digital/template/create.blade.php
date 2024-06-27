@extends('layouts.app')
@section('content')
<div class="main-panel">
    <div class="heading-section">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h4 class="heading-title">Add New Instructional Template</h4>
            </div>
            <div class="btn-option-info">
                <a href="{{ route('admin.instructional.template') }}" class="btn-ye">Back</a>
            </div>
        </div>
    </div>
    <div class="di-section">
        <div class="add-form-info">
            <form class="" method="post" enctype="multipart/form-data" id="template-form">
            @csrf
            <div class="upload-video-form">
                <div class="filter-info">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Select Plan</label>
                                <select name="plan" class="form-control">
                                    @foreach(getStaticSubscription() as $key=>$value)
                                        <option value="{{$key}}" @if($key==old('plan')) selected @endif >{{$value}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" name="title" class="form-control {{$errors->first('title')?'is-invalid':''}}" placeholder="Title" />
                                <span class="{{$errors->first('title')?'error invalid-feedback':''}}">{{$errors->first('title')}}</span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" class="form-control {{$errors->first('description')?'is-invalid':''}}" placeholder="Description" id="template-description">{!! old('description') !!}</textarea>
                                <span class="{{$errors->first('description')?'error invalid-feedback':''}}">{{$errors->first('description')}}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Status</label>
                                <select name="status" class="form-control {{$errors->first('status')?'is-invalid':''}}">
                                    <option value="">Select</option>
                                    @foreach(status_array() as $key=>$row)
                                        <option value="{{$key}}" @if($key==old('status')) selected @endif >{{$row}}</option>
                                    @endforeach
                                </select>
                                <span class="{{$errors->first('status')?'error invalid-feedback':''}}">{{$errors->first('status')}}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="" class="form-label">Upload PDF File</label>
                                <input type="file" class="form-control col-md-4 p-2 {{$errors->first('file')?'is-invalid':''}}" name="file"  accept='application/pdf'>
                                <span class="{{$errors->first('file')?'error invalid-feedback':''}}">{{$errors->first('file')}}</span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="add-form-btn mt-3 pull-right"> 
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
<script src="{{asset('plugins/js/sweetalert.min.js')}}"></script>
<script src="{{asset('plugins/js/jquery.validate.min.js')}}"></script>
<script src="{{ asset('plugins/editor/ckeditor.js') }}"></script>
<script>
    $(document).ready(function(){
        jQuery.validator.addMethod(
            "onlypdf",
            function (value, element) {
                if (this.optional(element) || !element.files || !element.files[0]) {
                    return true;
                } else {
                    var fileType = element.files[0].type;
                    fileType = fileType.replace('application/', '');
                    if(fileType=='pdf') return true;
                    return false;
                }
            },
            'Sorry, we can only accept pdf file.'
        );

        $('#template-form').validate({
            rules:{
                title:{
                    required: true,                    
                    maxlength: 191
                },
                
                file:{
                    required:true,
                    onlypdf:true
                },
                description:{
                    required:true,
                    maxlength:5000,
                },
                status:{
                    required : true
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

        /*-----------------------------------------------------------------------------------------------*/
        if($("#template-description").length){
            CKEDITOR.replace( 'template-description' );
        }
        /*-----------------------------------------------------------------------------------------------*/
                
        
    });
</script>
@endpush
