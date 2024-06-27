@extends('layouts.app')
@section('content')
<div class="main-panel">
    <div class="heading-section">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h4 class="heading-title">{{$data->title}}</h4>
            </div>
            <div class="btn-option-info">
                <a href="{{ route('admin.notification.list') }}" class="btn-ye">Back</a>
            </div>
        </div>
    </div>
    <div class="di-single-content-info">
        <div class="di-single-item">
            <div class="row">
                @if($data->image)
                <div class="col-md-5">
                    <div class="di-single-images ">
                        <img src="{{asset($data->image)}}" />
                    </div>
                </div>
                @endif
                <div class="col-md-7">
                    <div class="di-single-content">
                        <h2>{{$data->title}}</h2>
                        <p>{{$data->data}}</p>
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