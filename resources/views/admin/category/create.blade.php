@extends('layouts.app')
@section('content')
<div class="main-panel">
    <div class="heading-section">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h4 class="heading-title">Add New Category</h4>
            </div>
            <div class="btn-option-info">
                <a href="{{ route('admin.category.list') }}" class="btn-ye">Back</a>
            </div>
        </div>
    </div>
    <div class="di-section"> 
        <div class="add-form-info">
            <form class="" method="post" enctype="multipart/form-data" id="category-form">
            @csrf
            <div class="upload-video-form">
                <div class="filter-info">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Category Name</label>
                                <input type="text" name="category_name" class="form-control {{$errors->first('category_name')?'is-invalid':''}}" placeholder="Category Name" value="{{old('category_name')}}" />
                                <span class="{{$errors->first('category_name')?'error invalid-feedback':''}}">{{$errors->first('category_name')}}</span>
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
        $('#category-form').validate({
            rules:{
                category_name:{
                    required: true,                    
                    maxlength: 191
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