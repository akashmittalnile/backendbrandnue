@extends('layouts.app')
@section('content')
<div class="main-panel">
    <div class="heading-section">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h4 class="heading-title">{{$recipe->meal_title}}</h4>
            </div>
            <div class="btn-option-info">
                <a href="{{ route('admin.recipe.list') }}" class="btn-ye bg-success">Back</a>
                <a href="{{route('admin.recipe.edit',$recipe)}}" class="btn-ye" >Edit Recipe</a>
            </div>
        </div>
    </div>
    <div class="di-single-content-info">
        @if(!empty($recipe->image_description) || $recipe->recipeImages->count()>0)
        <div class="di-single-item">
            <div class="row">
                <div class="col-md-5">
                    @if($recipe->recipeImages->count())
                        @foreach($recipe->recipeImages as $image)
                            <div class="di-single-images @if($loop->index!=0) mt-3 @endif">
                                @php
                                    $parsed = parse_url($image->url);

                                    if(isset($parsed['scheme']) && in_array($parsed['scheme'],['https','http'])){
                                        echo '<img src="'.$image->url.'" />';
                                    }else if(!empty($image->url)){
                                        echo '<img src="'.asset($image->url).'" />';
                                    }
                                @endphp
                            </div>
                        @endforeach
                    @endif
                </div>
                <div class="col-md-7">
                    <div class="di-single-content">
                        <h2>{{$recipe->meal_title}}</h2>
                        {!! $recipe->image_description !!}
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(!empty($recipe->video_description) || $recipe->recipeVideos->count()>0)
        <div class="di-single-item">
            <div class="row">
                <div class="col-md-5">
                    @if($recipe->recipeVideos->count())
                        @foreach($recipe->recipeVideos as $video)
                        @php 
                            $url = (strtolower($video->file_location)=='external') ? $video->url : asset($video->url);
                        @endphp
                            <div class="di-single-video @if($loop->index!=0) mt-3 @endif">
                                <iframe
                                    width="100%"
                                    height="315"
                                    src="{{ $url }}"
                                    title="YouTube video player"
                                    frameborder="0"
                                    allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    controls=""
                                    autoplay="false"
                                    allowfullscreen
                                    id='video-control'
                                ></iframe>
                                
                            </div>
                        @endforeach
                    @endif
                </div>
                <div class="col-md-7">
                    <div class="di-single-content">
                        <h2>{{$recipe->meal_title}}</h2>
                        {!! $recipe->video_description !!}
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(!empty($recipe->audio_description) || $recipe->recipeAudios->count()>0)
        <div class="di-single-item">
            <div class="row">
                <div class="col-md-5">
                    @if($recipe->recipeAudios->count())
                        @foreach($recipe->recipeAudios as $audio)
                            <div class="di-single-audio">
                                <audio controls>
                                    <source src="{{ asset($audio->url) }}" />
                                    Your browser does not support the audio element.
                                </audio>
                            </div>
                        @endforeach
                    @endif
                </div>
                <div class="col-md-7">
                    <div class="di-single-content">
                        <h2>{{$recipe->meal_title}}</h2>
                        {!! $recipe->audio_description !!}
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
@push('css')
<link rel="stylesheet" href="{{ asset('admin/css/details.css') }}">
@endpush
@push('js')
<script>
    window.addEventListener('load', function(event){
        myFunction();
    });
    function myFunction() {
      var iframe = document.getElementById("video-control");
      if(iframe){
            var elmnt = iframe.contentWindow.document.getElementsByTagName("video")[0];
            elmnt.pause();
      }
      
      //elmnt.style.display = "none";
    }
</script>
@endpush