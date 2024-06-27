@extends('layouts.app')
@section('content')
<div class="main-panel">
    <div class="heading-section">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h4 class="heading-title">Digital Library</h4>
            </div>
        </div>
    </div>
    @include('common.msg')
    <div class="di-section">
        <div class="category-list">
            <div class="category-list-item">
                <div class="category-list-item-media">
                    <a href="{{ route('admin.recipe.list') }}">
                        @if(isset($recipe['image']) && !empty($recipe['image']))
                            <img src="{{ asset($recipe['image']) }}" />
                        @else
                            <img src="{{ asset('admin/images/recipe.jpg') }}" />
                        @endif
                    </a>
                </div>
                <div class="category-list-item-text">
                    <h2><a href="{{ route('admin.recipe.list') }}">{{$recipe['title']??'N/A'}}</a></h2>
                    <p>
                        {{$recipe['description']??'N/A'}}
                    </p>
                </div>
                <div class="category-action-btn">
                    <a class="btn-view" href="{{ route('admin.digital.library.edit',1) }}"> EDIT <i class="fa fa-edit"></i></a>
                    <a class="btn-view" href="{{ route('admin.recipe.list') }}"> GO <i class="las la-arrow-right"></i></a>
                </div>

            </div>
            <div class="category-list-item">
                <div class="category-list-item-media">
                    <a href="{{ route('admin.instructional.guide') }}">
                        @if(isset($guide['image']) && !empty($guide['image']))
                            <img src="{{ asset($guide['image']) }}" />
                        @else
                            <img src="{{ asset('admin/images/resources.jpg') }}" />
                        @endif
                    </a>
                </div>
                <div class="category-list-item-text">
                    <h2><a href="{{ route('admin.instructional.guide') }}">{{$guide['title']??"N/A"}}</a></h2>
                    <p>
                        {{$guide['description']??'N/A'}}
                    </p>
                </div>
                <div class="category-action-btn">
                    <a class="btn-view" href="{{ route('admin.digital.library.edit',2) }}"> EDIT <i class="fa fa-edit"></i></a>
                    <a class="btn-view" href="{{ route('admin.instructional.guide') }}"> GO <i class="las la-arrow-right"></i></a>
                </div>
            </div>
            <div class="category-list-item">
                <div class="category-list-item-media">
                    <a href="{{ route('admin.instructional.template') }}">
                        @if(isset($template['image']) && !empty($template['image']))
                            <img src="{{ asset($template['image']) }}" />
                        @else
                            <img src="{{ asset('admin/images/instructionaltemplate.jpg') }}" />

                        @endif
                    </a>
                </div>
                <div class="category-list-item-text">
                    <h2><a href="{{ route('admin.instructional.template') }}">{{$template['title']??'N/A'}}</a></h2>
                    <p>
                        {{$template['description']??'N/A'}}
                    </p>
                </div>
                <div class="category-action-btn">
                    <a class="btn-view" href="{{ route('admin.digital.library.edit',3) }}"> EDIT <i class="fa fa-edit"></i></a>
                    <a class="btn-view" href="{{ route('admin.instructional.template') }}"> GO <i class="las la-arrow-right"></i></a>
                </div>
            </div>

            <div class="category-list-item">
                <div class="category-list-item-media">
                    <a href="{{ route('admin.instructional.video') }}">
                        @if(isset($video['image']) && !empty($video['image']))
                            <img src="{{ asset($video['image']) }}" />
                        @else
                            <img src="{{ asset('admin/images/instructionalvideo.jpg') }}" />
                        @endif
                    </a>
                </div>
                <div class="category-list-item-text">
                    <h2><a href="{{ route('admin.instructional.video') }}">{{$video['title']??'N/A'}}</a></h2>
                    <p>
                        {{$video['description']??'N/A'}}
                    </p>
                </div>
                <div class="category-action-btn">
                    <a class="btn-view" href="{{ route('admin.digital.library.edit',4) }}"> EDIT <i class="fa fa-edit"></i></a>
                    <a class="btn-view" href="{{ route('admin.instructional.video') }}"> GO <i class="las la-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('admin/css/digitallibrary.css') }}">
@endpush