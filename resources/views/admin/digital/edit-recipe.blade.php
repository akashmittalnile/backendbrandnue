@extends('layouts.app')
@section('content')
<div class="main-panel">
    <div class="heading-section">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h4 class="heading-title">Update Recipe</h4>
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
                <input type="hidden" id="recipe_video_id" value="{{$recipe->id}}" data-url="{{ route('admin.upload.file',$recipe) }}">
                <div class="filter-info">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Select Category </label>
                                <select class="form-control {{$errors->first('category_name')?'is-invalid':''}}" name="category_name">
                                    <option value="">Select</option>
                                    @forelse($categories as $category)
                                        <option value="{{$category->id}}" @if($category->id==$recipe->category_id) selected @endif>{{$category->name}}</option>
                                    @empty
                                    @endforelse
                                </select>
                                <span class="{{$errors->first('category_name')?'error invalid-feedback':''}}">{{$errors->first('category_name')}}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Meal Title</label>
                                <input type="text" name="meal_title" class="form-control {{$errors->first('meal_title')?'is-invalid':''}}" placeholder="Meal Title" value="{{$recipe->meal_title}}" />
                                <span class="{{$errors->first('meal_title')?'error invalid-feedback':''}}">{{$errors->first('meal_title')}}</span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Add Keyword</label>
                                <input type="text" name="meal_keyword" class="form-control {{$errors->first('meal_keyword')?'is-invalid':''}}" placeholder="Keyword" value="{{$recipe->meal_keyword}}"/>
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
                                @if($recipe->recipeImages->count())
                                    @foreach($recipe->recipeImages as $image)
                                        <div class="col-md-2 image-{{$image->id}}">
                                            <div class="upload-page-item">
                                                <div class="upload-image">
                                                    @php
                                                        $parsed = parse_url($image->url);
                                                        if(isset($parsed['scheme']) && in_array($parsed['scheme'],['https','http'])){
                                                            echo '<img src="'.$image->url.'" />';
                                                        }else if(!empty($image->url)){
                                                            echo '<img src="'.asset($image->url).'" />';
                                                        }
                                                    @endphp
                                                </div>
                                                <span class="remove-file remove-image" data-id="{{$image->id}}"><i class="fa fa-times"></i></span>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                                <div class="col-md-2 before-image">
                                    <div class="form-group">
                                        <div class="upload-file">
                                            <input type="file" name="addimage" id="addimage" class="uploadfile addfile" data-url="{{ route('admin.upload.file',$recipe) }}" accept="image/*" />
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
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label>Description</label>
                                        <textarea class="form-control" rows="3" name="image_description" id="image-description">{!! $recipe->image_description !!}</textarea>
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
                            <div class="row video-append">
                                @if($recipe->recipeVideos->count())
                                    @foreach($recipe->recipeVideos as $video)
                                    @php 
                                        $url = (strtolower($video->file_location)=='external') ? $video->url : asset($video->url);
                                    @endphp
                                    <div class="col-md-2 video-{{$video->id}}">
                                        <div class="upload-page-item">
                                            <div class="upload-image">                                                
                                                <iframe
                                                width="100%"
                                                src="{{ $url }}"
                                                title="YouTube video player"
                                                frameborder="0"
                                                allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                controls= "false"
                                                allowfullscreen
                                                id="video-control"
                                            ></iframe>
                                           
                                            </div>
                                            <span class="remove-file remove-video" data-id="{{$video->id}}"><i class="fa fa-times"></i></span>
                                        </div>
                                    </div>
                                    @endforeach
                                @endif
                            </div>
                            <div class="row">
                                <div class="col-md-6 before-video upload-local-file mt-3 mb-3" style="display: none;">
                                    <div class="form-group">
                                        <input type="file" class="form-control col-md-4 p-2 {{$errors->first('file')?'is-invalid':''}}" name="file"  accept='video/*' id="upload-video-file">
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
                                        <textarea class="form-control" rows="3" name="video_description" id="video-description">{!! $recipe->video_description !!}</textarea>
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
                                @if($recipe->recipeAudios->count())
                                    @foreach($recipe->recipeAudios as $audio)
                                        <div class="col-md-4 audio-{{$audio->id}}">
                                            <div class="upload-page-item">
                                                <div class="upload-audio">
                                                    <audio controls>
                                                        <source src="{{asset($audio->url)}}" />
                                                    </audio>
                                                </div>
                                                <span class="remove-file remove-audio" data-id="{{$audio->id}}"><i class="fa fa-times"></i></span>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                                <div class="col-md-2 before-audio">
                                    <div class="form-group mb-3">
                                        <div class="upload-file">
                                            <input type="file" id="addAudio" class="uploadfile addfile" data-url="{{ route('admin.upload.file',$recipe) }}" accept="audio/*" />
                                            <label for="addAudio">
                                                <div class="upload-media">
                                                    <i class="las la-plus-circle"></i>
                                                </div>
                                                <span>Choose a audio…</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label>Description</label>
                                        <textarea class="form-control" rows="3" name="audio_description" id="audio-description">{!! $recipe->audio_description !!}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="add-form-checkbox">
                    <div class="form-checkbox">
                        <input type="checkbox" value="Y" id="Recipe is premium" name="premium" @if($recipe->is_premium==config('constant.status.active')) checked @endif />
                        <label for="Recipe is premium">Recipe is premium</label>
                    </div>
                </div>

                <div class="add-form-btn">
                    <button class="btn-save save-as-draft" type="button" data-url="{{ route('admin.recipe.update',$recipe) }}">Save Draft</button>
                    <button class="btn-publish save-as-publish" type="button" data-url="{{ route('admin.recipe.update',$recipe) }}">Update to Publish</button>
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
                    onlyimages:true
                },
                image_description:{
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
        /*---------------------------------------------------*/
        $(document).on('click','.save-as-draft',function(){
            let form = $('#recipe-form');
            const url = $(this).data('url');
            if(form.valid()){
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
                            });
                            setTimeout(()=>{
                                window.location.href = res.url;
                            },2000);
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
                });
            }
        });
        
        /*---------------------------------------------------*/
        $(document).on('click','.save-as-publish',function(){
            let form = $('#recipe-form');
            const url = $(this).data('url');
            if(form.valid()){
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
                                // icon: "warning",
                                dangerMode: true,
                            });
                            setTimeout(()=>{
                                window.location.href = res.url;
                            },2000);
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
                });
            }
        })
        /*---------------------------------------------------*/
        $(document).on('click','.remove-image',function(){
            const id = $(this).data('id');
            swal({
                title: "Are you sure?",
                text: "You won't be able to revert this",
                // icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    $('.please-wait').show();
                    $.ajax({
                        type:'delete',
                        url : "{{ route('admin.delete.file') }}",
                        headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')},
                        data:{
                            type: 'recipe_image',
                            id: id
                        },
                        success: function(res){
                            if(res.status==true){
                                $('.image-'+id).remove();
                                swal({
                                    title:"Success",
                                    text:res.msg,
                                    // icon: "warning",
                                    dangerMode: true,
                                });
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
        /*---------------------------------------------------*/
        $(document).on('change','#addimage',function(){
            let file = $('#addimage');
            let url = file.data('url');
            let file_data = file.prop('files')[0];
            let fileType = file_data.type;
            let isImage = /^(image)\//i.test(fileType);
            if(isImage==false){
                alert('Sorry, we can only accept image file.');
                return false;
            }
            
            let form_data = new FormData();
            form_data.append('image', file_data);
            form_data.append('type', 'recipe_image');
            $(".please-wait").show();
            $.ajax({
                type:'post',
                url : url,
                data : form_data,
                headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')},
                cache       : false,
                contentType : false,
                processData : false,
                success: function(res){
                    if(res.status==true){
                        let image=`
                            <div class="col-md-2 image-${res.id}">
                                <div class="upload-page-item">
                                    <div class="upload-image">
                                        <img src="${res.url}">
                                    </div>
                                    <span class="remove-file remove-image" data-id="${res.id}"><i class="fa fa-times"></i></span>
                                </div>
                            </div>
                        `;
                        $(image).insertBefore('.before-image');
                        file.val('');
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
            });
        });

        /*---------------------------------------------------*/
        $(document).on('change','#addVideo',function(){
            let file = $('#addVideo');
            let url = file.data('url');
            let file_data = file.prop('files')[0];
            let fileType = file_data.type;
            let isVideo = /^(video)\//i.test(fileType);
            if(isVideo==false){
                alert('Sorry, we can only accept video file.');
                return false;
            }
            let form_data = new FormData();
            form_data.append('video', file_data);
            form_data.append('type', 'recipe_video');
            $(".please-wait").show();
            $.ajax({
                type:'post',
                url : url,
                data : form_data,
                headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')},
                cache       : false,
                contentType : false,
                processData : false,
                success: function(res){
                    if(res.status==true){
                        let video=`
                            <div class="col-md-2 video-${res.id}">
                                <div class="upload-page-item">
                                    <div class="upload-image">
                                        <iframe
                                            width="100%"
                                            src="${res.url}"
                                            title="YouTube video player"
                                            frameborder="0"
                                            allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                            autoplay="false"
                                            controls="false"
                                            allowfullscreen
                                        >
                                        </iframe>
                                    </div>
                                    <span class="remove-file remove-video" data-id="${res.id}"><i class="fa fa-times"></i></span>
                                </div>
                            </div>
                        `;
                        $(video).insertBefore('.before-video');
                        file.val('');
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
            });
        });
        /*---------------------------------------------------*/
        $(document).on('click','.remove-video',function(){
            const id = $(this).data('id');
            swal({
                title: "Are you sure?",
                text: "You won't be able to revert this",
                // icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    $('.please-wait').show();
                    $.ajax({
                        type:'delete',
                        url : "{{ route('admin.delete.file') }}",
                        headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')},
                        data:{
                            type: 'recipe_video',
                            id: id
                        },
                        success: function(res){
                            if(res.status==true){
                                $('.video-'+id).remove();
                                swal({
                                    title:"Success",
                                    text:res.msg,
                                    // icon: "warning",
                                    dangerMode: true,
                                });
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

        /*---------------------------------------------------*/
        $(document).on('change','#addAudio',function(){
            let file = $('#addAudio');
            let url = file.data('url');
            let file_data = file.prop('files')[0];
            let fileType = file_data.type;
            let isAudio = /^(audio)\//i.test(fileType);
            if(isAudio==false){
                alert('Sorry, we can only accept audio file.');
                return false;
            }
            let form_data = new FormData();
            form_data.append('audio', file_data);
            form_data.append('type', 'recipe_audio');
            $(".please-wait").show();
            $.ajax({
                type:'post',
                url : url,
                data : form_data,
                headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')},
                cache       : false,
                contentType : false,
                processData : false,
                success: function(res){
                    if(res.status==true){
                        let video=`
                            <div class="col-md-4 audio-${res.id}">
                                <div class="upload-page-item">
                                    <div class="upload-audio">
                                        <audio controls>
                                            <source src="${res.url}" />
                                        </audio>
                                    </div>
                                    <span class="remove-file remove-audio" data-id="${res.id}"><i class="fa fa-times"></i></span>
                                </div>
                            </div>
                        `;
                        $(video).insertBefore('.before-audio');
                        file.val('');
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
            });
        });
        /*---------------------------------------------------*/
        $(document).on('click','.remove-audio',function(){
            const id = $(this).data('id');
            swal({
                title: "Are you sure?",
                text: "You won't be able to revert this",
                // icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    $('.please-wait').show();
                    $.ajax({
                        type:'delete',
                        url : "{{ route('admin.delete.file') }}",
                        headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')},
                        data:{
                            type: 'recipe_audio',
                            id: id
                        },
                        success: function(res){
                            if(res.status==true){
                                $('.audio-'+id).remove();
                                swal({
                                    title:"Success",
                                    text:res.msg,
                                    // icon: "warning",
                                    dangerMode: true,
                                });
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
    

    window.addEventListener('load', function(event){
        myFunction();
    });
    function myFunction() {
      var iframe = document.getElementById("video-control");
      if(iframe){
        var elmnt = iframe.contentWindow.document.getElementsByTagName("video")[0];
        if(elmnt){

          elmnt.pause();
        }
      }
      
      //elmnt.style.display = "none";
    }



    var rem = new Resumable({
        target:'{{route('upload')}}',
        //query:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        //query: {overwrite: !!start},
        //fileType: ['mp4'],
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
        rem.upload(); //always start
    });

    rem.on('fileSuccess', function(file,response){
        response = JSON.parse(response);
        if(response.status==true){
            let recipe = $("#recipe_video_id");
            let recipe_url = recipe.data('url');
            //let recipe_id  = recipe.val();
            $.ajax({
                type:'post',
                url : recipe_url,
                data:{type:'video',url:response.path},
                headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')},
                success : function(res){
                    let html = `
                        <div class="col-md-2">
                            <div class="upload-page-item">
                                <div class="upload-image">
                                    <iframe width="100%" src="${base_url}/${response.path}" frameborder="0" allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture" controls="false" allowfullscreen="" id="video-control"></iframe>
                                </div>
                                <span class="remove-file remove-video" data-id="${res.id}"><i class="fa fa-times"></i></span>
                            </div>
                        </div>
                    `;
                    $(".video-append").append(html);
                    
                }
            });
        }
        //console.debug('fileSuccess',file);
    });

    rem.on('fileError', function(file, message){
        $(".please-wait").hide();
        alert("File is not uploaded, please try again");
        hideProgress()
        console.debug('fileError', file, message);
    });
    rem.on('fileRetry', function(file){
        console.debug('fileRetry', file);
    });
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
</script>
@endpush