@extends('layouts.app')
@section('content')
<div class="main-panel">
    <div class="heading-section">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h4 class="heading-title">Digital Library Update</h4>
            </div>
            <div class="btn-option-info">
                <a href="{{ route('admin.digital.library') }}" class="btn-ye">Back</a>
            </div>
        </div>
    </div>
    <div class="di-section">
        <div class="add-form-info">
            <form class="" method="post" enctype="multipart/form-data" id="digital-library-form">
            <div class="upload-video-form">
                <div class="filter-info">
                    <div class="row">
                        @csrf
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
                                <textarea class="form-control" rows="3" name="description">{{$data->description}}</textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="">Image</label>
                                    </div>
                                </div>
                                <div class="col-md-2 single-image" @if(empty($data->image)) style="display:none" @endif>
                                    <div class="upload-page-item">
                                        <div class="upload-image">
                                            @php
                                                $parsed = parse_url($data->image);
                                                if(isset($parsed['scheme']) && in_array($parsed['scheme'],['https','http'])){
                                                    echo '<img src="'.$data->image.'" />';
                                                }else if(!empty($data->image)){
                                                    echo '<img src="'.asset($data->image).'" />';
                                                }
                                            @endphp
                                        </div>
                                        <span class="remove-file remove-image" data-id="{{$data->id}}" data-text="{{$data->image}}" data-url="{{ route('admin.digital.library.deleteImage',$data) }}"><i class="fa fa-times"></i></span>
                                    </div>
                                </div>
                                <div class="col-md-2 before-image" @if(!empty($data->image)) style="display:none" @endif>
                                    <div class="form-group">
                                        <div class="upload-file">
                                            <input type="file" name="image" id="addimage" class="uploadfile addfile"  accept="image/*" />
                                            <label for="addimage">
                                                <div class="upload-media">
                                                    <i class="las la-plus-circle"></i>
                                                </div>
                                                <span>Choose a file…</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group pull-right">
                            <button class="btn-save" type="submit">Update</button>
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
<script src="{{asset('plugins/js/sweetalert.min.js')}}"></script>
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
        
        $('#digital-library-form').validate({
            rules:{
                title:{
                    required: true,                    
                    maxlength: 191
                },
                
                image:{
                    onlyimages:true
                },
                description:{
                    required:true,
                    maxlength:5000,
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
        /*------------------------------------------------------------------------------------------------------*/
        $(document).on('click','.remove-image',function(){
            const id = $(this).data('id');
            const url = $(this).data('url');
            const text = $(this).data('text');

            swal({
                title: "Are you sure?",
                text: "You won't be able to revert this",
                // icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    if(text.length==0){
                        $('#addimage').val('');
                        $(".single-image").hide();
                        $(".before-image").show();
                        return; 
                    }
                    $('.please-wait').show();
                    $.ajax({
                        type:'delete',
                        url : url,
                        headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')},
                        success: function(res){
                            if(res.status==true){
                                $('#addimage').val('');
                                $(".single-image").hide();
                                $(".before-image").show();
                            }else{
                                swal({
                                    title:"Alert",
                                    text:res.msg,
                                    // icon: "warning",
                                    dangerMode: true,
                                });
                            }
                            $(".please-wait").hide();
                        }
                    })
                } else {
                }
            });

        });
        /*------------------------------------------------------------------------------------------------------*/
        $(document).on('change','#addimage',function(){
            let file = $('#addimage');
            let file_data = file.prop('files')[0];
            let fileType = file_data.type;
            let isImage = /^(image)\//i.test(fileType);
            if(isImage==false){
                alert('Sorry, we can only accept image file.');
                return false;
            }
            
            $(".upload-image").html('<img src="'+window.URL.createObjectURL(file_data)+'"/>');
            $(".single-image").show();
            $(".before-image").hide();
            
        });
        /*------------------------------------------------------------------------------------------------------*/

    });
</script>
@endpush