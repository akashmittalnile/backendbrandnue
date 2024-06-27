@extends('layouts.app')
@section('content')
<div class="main-panel">
    <div class="heading-section">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h4 class="heading-title">Edit Plan Description</h4>
            </div>
            <div class="btn-option-info">
                <a href="{{ route('admin.membership.plans') }}" class="btn-ye">Back</a>
            </div>
        </div>
    </div>
    @include('common.msg')
    <div class="di-section">
        <div class="add-form-info">
            <form class="" method="post" enctype="multipart/form-data" id="user-form">
                @csrf
            <div class="upload-video-form">
                <div class="filter-info">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Plan Name</label>
                                <span class="form-control">{{$plan->name}}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Plan Price</label>
                                <span class="form-control">{{$plan->price??"00"}}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Plan Frequency</label>
                                <span class="form-control">
                                    @if(empty($plan->subscription_interval))
                                        Free
                                    @else
                                        {{$plan->subscription_interval}} Month's
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Description</label>
                                <textarea name="description" class="form-control" id="full-description" >{!! $plan->description !!}</textarea>
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
<link rel="stylesheet" href="{{ asset('plugins/editor/summernote.css') }}">
@endpush
@push('js')
<script src="{{ asset('plugins/editor/ckeditor.js') }}"></script>
<script>
    $(document).ready(function(){
        if($("#full-description").length){
            CKEDITOR.replace( 'full-description' );
        }
    });
</script>
@endpush