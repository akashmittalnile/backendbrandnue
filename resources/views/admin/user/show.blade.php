@extends('layouts.app')
@section('content')
<div class="main-panel">
    <div class="heading-section">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h4 class="heading-title">User Details</h4>
            </div>
            <div class="btn-option-info">
                <a href="{{ route('admin.user.list') }}" class="btn-ye">Back</a>
            </div>
        </div>
    </div>
    <div class="di-section">
        <div class="add-form-info">
            <form class="" method="post" enctype="multipart/form-data" id="user-form">
                @csrf
            <div class="upload-video-form">
                <div class="user-filter-info">
                    <div class="row g-1">
                        <div class="col-md-4">
                            <div class="user-show-item">
                                <div class="user-show-text">First Name:</div>
                                <div class="user-show-value">{{$user->first_name}}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="user-show-item">
                                <div class="user-show-text">Last Name:</div>
                                <div class="user-show-value">{{$user->last_name}}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="user-show-item">
                                <div class="user-show-text">Email:</div>
                                <div class="user-show-value">{{$user->email}}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="user-show-item">
                                <div class="user-show-text">Phone:</div>
                                <div class="user-show-value">{{$user->phone}}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="user-show-item">
                                <div class="user-show-text">Gender:</div>
                                <div class="user-show-value">{{$user->gender}}</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="user-show-item">
                                <div class="user-show-text">Date Of Birth:</div>
                                <div class="user-show-value">
                                    @if(!empty($user->dob))
                                        {{dateFormat($user->dob)}}
                                    @else
                                        N/A
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="user-show-item">
                                <div class="user-show-text">Height in feet:</div>
                                <div class="user-show-value">{{$user->height_feet}}</div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="user-show-item">
                                <div class="user-show-text">Height in inch:</div>
                                <div class="user-show-value">{{$user->height_inch??'N/A'}}</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="user-show-item">
                                <div class="user-show-text">Waist Measurement:</div>
                                <div class="user-show-value">{{$user->waist_measurement}}</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="user-show-item">
                                <div class="user-show-text">Current Weight:</div>
                                <div class="user-show-value">{{$user->current_weight}}</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="user-show-item">
                                <div class="user-show-text">Goal Weight:</div>
                                <div class="user-show-value">{{$user->goal_weight}}</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="user-show-item">
                                <div class="user-show-text">Status:</div>
                                <div class="user-show-value">{{$user->status}}</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="user-show-item">
                                <div class="user-show-text">Profile Image:</div>
                                <div class="user-show-value">
                                    @if(!empty($user->profile_image))
                                    <img src="{{ asset($user->profile_image) }}" alt="" width="100" height="100">
                                @else
                                    <p>N/A</p>
                                @endif
                                </div>
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
