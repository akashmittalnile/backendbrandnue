@extends('layouts.app')
@section('content')
<div class="main-panel">
    <div class="heading-section">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h4 class="heading-title">Add New Recipe</h4>
            </div>
            <div class="btn-option-info">
                <a href="{{ route('admin.recipe.list') }}" class="btn-ye">Back</a>
            </div>
        </div>
    </div>
    <div class="di-section">
        <div class="add-form-info">
            <form class="" method="post" enctype="multipart/form-data" id="recipe-form">
            <div class="upload-video-form">
                <div class="filter-info">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Select Category </label>
                                <select class="form-control {{$errors->first('category_name')?'is-invalid':''}}" name="category_name">
                                    <option value="">Select</option>
                                    @forelse($categories as $category)
                                        <option value="{{$category->id}}" @if($category->id==old('category_name')) selected @endif>{{$category->name}}</option>
                                    @empty
                                    @endforelse
                                </select>
                                <span class="{{$errors->first('category_name')?'error invalid-feedback':''}}">{{$errors->first('category_name')}}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Meal Title</label>
                                <input type="text" name="meal_title" class="form-control {{$errors->first('meal_title')?'is-invalid':''}}" placeholder="Meal Title" />
                                <span class="{{$errors->first('meal_title')?'error invalid-feedback':''}}">{{$errors->first('meal_title')}}</span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Add Keyword</label>
                                <input type="text" name="meal_keyword" class="form-control {{$errors->first('meal_keyword')?'is-invalid':''}}" placeholder="Keyword" />
                                <span class="{{$errors->first('meal_keyword')?'error invalid-feedback':''}}">{{$errors->first('meal_keyword')}}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="upload-item-info">
                    <div class="upload-item-header">
                        <h2>Recipe Image</h2>
                    </div>
                    <div class="upload-item-body">
                        <div class="upload-item-form">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="file" class="form-control" name="image" accept='image/*'>
                                    </div>
                                </div>
                                {{-- <div class="col-md-2">
                                    <div class="upload-page-item">
                                        <div class="upload-image">
                                            <img src="{{ asset('admin/images/p1.jpg') }}" />
                                        </div>
                                        <span class="remove-file remove-image"><i class="fa fa-times"></i></span>
                                    </div>
                                </div> --}}
                                {{-- <div class="col-md-2">
                                    <div class="form-group">
                                        <div class="upload-file">
                                            <input type="file" name="addimages[]" id="addimages" class="uploadfile addfile" data-multiple-caption="{count} files selected" multiple="" onchange="preview_image()" />
                                            <label for="addimages">
                                                <div class="upload-media">
                                                    <i class="las la-plus-circle"></i>
                                                </div>
                                                <span>Choose a file…</span>
                                            </label>
                                        </div>
                                    </div>
                                </div> --}}
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label>Description</label>
                                        <textarea class="form-control" rows="3" name="image_description" id="image-description"></textarea> 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="upload-item-info">
                    <div class="upload-item-header">
                        <h2>
                            Recipe Video 
                            <select name="video_type" id="video-type">
                                <option value="">Select</option>
                                <option value="External">Embeded Video</option>
                                <option value="Local">Upload Video</option>
                            </select>
                        </h2>
                    </div>
                    <div class="upload-item-body">
                        <div class="upload-item-form">
                            <div class="row upload-local-file mt-3 mb-3" style="display: none;">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="hidden" name="recipe_video_url" id="recipe_video_url">
                                        {{-- <input type="file" class="form-control" name="recipe_video" accept="video/*"> --}}
                                        <input type="file" class="form-control col-md-4 p-2 {{$errors->first('file')?'is-invalid':''}}" name="file"  accept='video/*' id="upload-video-file">
                                        <span class="{{$errors->first('file')?'error invalid-feedback':''}}">{{$errors->first('file')}}</span>
                                    </div>
                                    <div class="progress mt-3">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated" role='progressbar' aria-volumein="0" aria-volumemax="100"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row upload-external-file" style="display: none;">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Embeded Video Url</label>
                                        <input type="text" name="embeded_video_url" class="form-control" placeholder="Enter Video Url" value=""/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label>Description</label>
                                        <textarea class="form-control" rows="3" name="video_description" id="video-description"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="upload-item-info">
                    <div class="upload-item-header">
                        <h2>Recipe Audio</h2>
                    </div>
                    <div class="upload-item-body">
                        <div class="upload-item-form">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="file" class="form-control" name="recipe_audio" accept="audio/*">
                                    </div>
                                </div>
                                {{-- <div class="col-md-2">
                                    <div class="form-group mb-3">
                                        <div class="upload-file">
                                            <input type="file" name="" id="addfile" class="uploadfile addfile" data-multiple-caption="{count} files selected" multiple="" />
                                            <label for="addfile">
                                                <div class="upload-media">
                                                    <i class="las la-plus-circle"></i>
                                                </div>
                                                <span>Choose a file…</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="upload-page-item">
                                        <div class="upload-audio">
                                            <audio controls>
                                                <source src="horse.ogg" type="audio/ogg" />
                                                <source src="horse.mp3" type="audio/mpeg" />
                                            </audio>
                                        </div>
                                    </div>
                                </div> --}}
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label>Description</label>
                                        <textarea class="form-control" rows="3" name="audio_description" id="audio-description"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="check_status">
                <div class="add-form-checkbox">
                    <div class="form-checkbox">
                        <input type="checkbox" value="Y" id="Mark this recipe as premium" name="premium" />
                        <label for="Mark this recipe as premium">Mark this recipe as premium</label>
                    </div>
                </div>

                <div class="add-form-btn">
                    <button class="btn-save save-as-draft" type="button" data-url="{{ route('admin.recipe.store') }}">Save Draft</button>
                    <button class="btn-publish save-as-publish" type="button" data-url="{{ route('admin.recipe.store') }}">Publish</button>
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
<script src="{{ asset('plugins/editor/ckeditor.js') }}"></script>
<script src="{{asset('plugins/js/resumable.js')}}"></script> 
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
        jQuery.validator.addMethod(
            "onlyvideo",
            function (value, element) {
                if (this.optional(element) || !element.files || !element.files[0]) {
                    return true;
                } else {
                    var fileType = element.files[0].type;
                    var isImage = /^(video)\//i.test(fileType);
                    return isImage;
                }
            },
            'Sorry, we can only accept video file.'
        );
        jQuery.validator.addMethod(
            "onlyaudio",
            function (value, element) {
                if (this.optional(element) || !element.files || !element.files[0]) {
                    return true;
                } else {
                    var fileType = element.files[0].type;
                    var isImage = /^(audio)\//i.test(fileType);
                    return isImage;
                }
            },
            'Sorry, we can only accept audio file.'
        );
        $('#recipe-form').validate({
            rules:{
                meal_title:{
                    required: true,                    
                    maxlength: 191
                },
                meal_keyword:{
                    required:true,
                    maxlength:191
                },
                image:{
                    required:true,
                    onlyimages:true
                },
                image_description:{
                    required:true,
                    maxlength:5000,
                },
                recipe_video:{
                    onlyvideo:true,
                },
                recipe_audio:{
                    onlyaudio:true,
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
        $(document).on('click','.save-as-draft',function(){
            let form = $('#recipe-form');
            const url = $(this).data('url');
            $('#check_status').val('{{config('constant.status.in_active')}}');
            if(form.valid()){
                if(rem.files.length==1){
                    console.log('Yes d');
                    rem.upload();
                    return false;
                }else{
                    console.log('No d');
                    save_as_draft(form,url);
                }
            }
        });
        
        /*---------------------------------------------------*/
        $(document).on('click','.save-as-publish',function(){
            let form = $('#recipe-form');
            const url = $(this).data('url');
            $('#check_status').val('{{config('constant.status.active')}}');
            if(form.valid()){
                if(rem.files.length==1){
                    console.log('Yes p');
                    rem.upload();
                    return false;
                }else{
                    console.log('No d');
                    save_as_publish(form,url);
                }
            }
        })
        /*---------------------------------------------------*/
        if($("#image-description").length){
            CKEDITOR.replace( 'image-description' );
        }

        if($("#video-description").length){
            CKEDITOR.replace( 'video-description' );
        }        

        if($("#audio-description").length){
            CKEDITOR.replace( 'audio-description' );
        }
        /*---------------------------------------------------*/
        $(document).on('change','#video-type',function(){
            let video_type = $(this).val();
            if(video_type=='Local'){
                $('.upload-local-file').show();
                $('.upload-external-file').hide();
            }else if(video_type=='External'){
                $('.upload-local-file').hide();
                $('.upload-external-file').show();
            }else{
                $('.upload-local-file').hide();
                $('.upload-external-file').hide();
            }
        }); 
        /*---------------------------------------------------*/
        hideProgress();
        /*---------------------------------------------------*/
        
    });
    

    var rem = new Resumable({
        target:'{{route('upload')}}',
        headers:{
            'Accept':'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        testChunks: false,
        throttleProgressCallbacks:1,
        size: 2024*1024
    });
    
    rem.assignBrowse(document.getElementById('upload-video-file'));
    //rem.assignDrop(document.getElementById('upload-video-file'));

    rem.on('fileProgress', function(file){
        console.debug('fileProgress', file);
        updateProgress(Math.floor(file.progress() * 100));
    });

    rem.on('fileAdded', function(file, event){
        console.log('fileAdded');
        let fileType = file.file.type;
        let isImage = /^(video)\//i.test(fileType);
        if(!isImage){
            swal({
                title:"Alert",
                text: "Please upload only video file",
                //icon: "warning",
                buttons: true,
                dangerMode: true,
            });
            return false;
        }
        console.debug('fileAdded', event);
        showProgress();
        //rem.upload(); //always start
    });

    rem.on('fileSuccess', function(file,response){
        response = JSON.parse(response);
        if(response.status==true){
            $('#recipe_video_url').val(response.path);
            let form = $('#recipe-form');
            const url = "{{ route('admin.recipe.store') }}";
            let status = $('#check_status').val();
            if(status=='Y'){
                save_as_publish(form,url);
            }else{
                save_as_draft(form,url);
            }
        }
        //console.debug('fileSuccess',file);
    });

    rem.on('fileError', function(file, message){
        $(".please-wait").hide();
        alert("File is not uploaded, please try again");
        hideProgress()
        console.debug('fileError', file, message);
    });
    /*rem.on('fileRetry', function(file){
        console.debug('fileRetry', file);
    });*/
    let progress = $('.progress');
    function showProgress(){
        progress.find('.progress-bar').css('width','0%');
        progress.find('.progress-bar').html('0%');
        progress.find('.progress-bar').removeClass('bg-success');
        progress.show();
    }

    function updateProgress(value) {
        progress.find('.progress-bar').css('width', `${value}%`)
        progress.find('.progress-bar').html(`${value}%`)
    }

    function hideProgress() {
        progress.hide();
    }

    function save_as_publish(form,url){
        var formData = new FormData(form[0]);
        formData.append('status','{{config('constant.status.active')}}');
        formData.append('image_description',CKEDITOR.instances["image-description"].getData());
        formData.append('video_description',CKEDITOR.instances["video-description"].getData());
        formData.append('audio_description',CKEDITOR.instances["audio-description"].getData());
        $(".please-wait").show();
        $.ajax({
            type: 'post',
            url : url,
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            contentType: false,
            cache: false,
            processData:false,
            success : function(res){
                if(res.status==true){
                    swal({
                        title:"Success",
                        text:res.msg,
                        dangerMode: true,
                    }).then(()=>{
                        window.location.href = res.url;
                    });
                }else{
                    swal({
                        title:"Alert",
                        text:res.msg,
                        dangerMode: true,
                    });
                    $(".please-wait").hide();
                }
            }
        });
    }

    function save_as_draft(form,url){
        var formData = new FormData(form[0]);
        formData.append('status','{{config('constant.status.in_active')}}');
        formData.append('image_description',CKEDITOR.instances["image-description"].getData());
        formData.append('video_description',CKEDITOR.instances["video-description"].getData());
        formData.append('audio_description',CKEDITOR.instances["audio-description"].getData());
        $(".please-wait").show();
        $.ajax({
            type: 'post',
            url : url,
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            contentType: false,
            cache: false,
            processData:false,
            success : function(res){
                if(res.status==true){
                    swal({
                        title:"Success",
                        text:res.msg,
                        // icon: "warning",
                        dangerMode: true,
                    }).then(()=>{
                        window.location.href = res.url;
                    });
                }else{
                    swal({
                        title:"Alert",
                        text:res.msg,
                        // icon: "warning",
                        dangerMode: true,
                    });
                    $(".please-wait").hide();
                }
            }
        });
    }

</script>
@endpush