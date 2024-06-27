@extends('layouts.app')
@section('content')
<div class="main-panel">
    <div class="heading-section">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h4 class="heading-title">Add New Instructional Video</h4>
            </div>
            <div class="btn-option-info">
                <a href="{{ route('admin.instructional.video') }}" class="btn-ye">Back</a>
            </div>
        </div>
    </div>
    <div class="di-section">
        <div class="add-form-info">
            <form class="" method="post" enctype="multipart/form-data" id="video-form">
            @csrf
            <div class="upload-video-form">
                <div class="filter-info">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Select Plan</label>
                                <select name="plan" class="form-control">
                                    @foreach(getStaticSubscription() as $key=>$value)
                                        <option value="{{$key}}" @if($key==old('plan')) selected @endif >{{$value}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" name="title" class="form-control {{$errors->first('title')?'is-invalid':''}}" placeholder="Title" />
                                <span class="{{$errors->first('title')?'error invalid-feedback':''}}">{{$errors->first('title')}}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
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
                        <div class="col-md-12 mt-3">
                            <div class="upload-item-info">
                                <div class="upload-item-header">
                                    <h2>
                                        Instructional Video 
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
                                                    <label for="" class="form-label">Upload video File</label>
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
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" class="form-control {{$errors->first('description')?'is-invalid':''}}" placeholder="Description" id="video-description">{!! old('description') !!}</textarea>
                                <span class="{{$errors->first('description')?'error invalid-feedback':''}}">{{$errors->first('description')}}</span>
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
<script src="{{asset('plugins/js/resumable.js')}}"></script> 
<script src="{{ asset('plugins/editor/ckeditor.js') }}"></script>

<script>
    $(document).ready(function(){
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

        $('#video-form').validate({
            rules:{
                title:{
                    required: true,                    
                    maxlength: 191
                },
                
                file:{
                    //required:true,
                    onlyvideo:true
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
            submitHandler: function(form){
                $(".please-wait").show();
                if(rem.files.length==1){
                    rem.upload();
                    return false;
                }else{
                    return true;
                }
                
            }
        });

        /*-----------------------------------------------------------------------------------------------*/
        if($("#video-description").length){
            CKEDITOR.replace( 'video-description' );
        }
        /*-----------------------------------------------------------------------------------------------*/
        
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
        
        hideProgress();
        
    });

    
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
        //rem.upload(); //always start
    });

    rem.on('fileSuccess', function(file,response){
        response = JSON.parse(response);
        if(response.status==true){
            const form = $('#video-form');
            $.ajax({
                type:'post',
                url: form.attr('action'),
                data: form.serialize()+"&path="+response.path,
                headers:{
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res){
                    if(res.status==true){
                        window.location.href = res.url;
                    }else{
                        $(".please-wait").hide();
                        alert(res.msg);
                    }
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

</script>

@endpush
