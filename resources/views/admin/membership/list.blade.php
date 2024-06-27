@extends('layouts.app')
@section('content')
<div class="main-panel">
    <div class="heading-section">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h4 class="heading-title">Membership</h4>
            </div>
            <div class="btn-option-info"></div>
        </div>
    </div>
    <div class="membership-section">
        <div class="membership-list">
            <div class="row">
                @if(count($plans))
            	@foreach($plans as $plan)
                <div class="col-md-3 mt-3">
                    <a href="{{ route('admin.membership.plans.edit',$plan) }}" class="btn btn-success btn-sm pull-right">Edit</a>
                    <div class="membership-list-item @if($loop->index % 2 == 0) gr-bg @else or-bg @endif">
                        <div class="membership-header">
                            <div class="membership-title">{{ucwords($plan->name)}}</div>
                            <!--<div class="membership-subtitle">Monthly plan</div>-->
                        </div>
                        <div class="membership-body">
                            <div class="membership-price">
                            	@if(empty($plan->price))
                            		Free
                            	@else
                            		{{config('constant.defaultCurrency')}}{{$plan->price}}
                            	@endif
                            </div>
                            <div class="membership-list">
                            	@if(count($plan->description))
                                <ul>
                                    @foreach($plan->description as $desc)
                                        <li><i class="las la-check-circle"></i>{!! $desc !!}</li>
                                    @endforeach
                                </ul>
                                @endif
                            </div>
                            {{-- <a class="Buy-btn" href="#">Manage Plan</a> --}}
                        </div>
                        <a class="view-text" href="{{ route('admin.user.list',['p'=>$plan->id]) }}" >View All Users</a>
                    </div>
                </div>
                @endforeach
                @else
                <div class="col-md-12">
                	<h5 class="text-center">No Plan created yet</h5>
                </div>
                @endif
                
            </div>
        </div>
    </div>
</div>

@endsection
@push('css')
<link rel="stylesheet" href="{{ asset('admin/css/membership.css') }}">
@endpush