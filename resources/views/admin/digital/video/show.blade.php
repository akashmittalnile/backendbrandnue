@extends('layouts.app')
@section('content')
<div class="main-panel">
    <div class="heading-section">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h4 class="heading-title">{{$video->title}}</h4>
            </div>
            <div class="btn-option-info">
                <a href="{{ route('admin.instructional.video') }}" class="btn-ye">Back</a>
            </div>
        </div>
    </div>
    <div class="di-single-content-info">
        <div class="di-single-item">
            <div class="row">
                @if($video->url)
                    <div class="col-md-5">
                        <div class="di-single-images ">
                            @php 
                                $url = (strtolower($video->location_type)=='External') ? $video->url : asset($video->url);
                            @endphp
                            <iframe src="{{ $url }}" frameborder="0" width="100%" height="100%"></iframe>
                        </div>
                    </div>
                @endif
                <div class="col-md-7">
                    <div class="di-single-content">
                        <h2>{{$video->title}}</h2>
                        {!! $video->description !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('css')
<link rel="stylesheet" href="{{ asset('admin/css/details.css') }}">
@endpush