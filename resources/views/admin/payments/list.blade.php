@extends('layouts.app')
@section('content')
<div class="main-panel">
    <div class="heading-section">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h4 class="heading-title">Payment</h4>
            </div>
            <div class="btn-option-info">
                
            </div>
        </div>
    </div>
    <div class="user-data-section">
        <div class="user-list-item">
            <div class="overview-section">
                <div class="row">
                    <div class="col-md-4">
                        <div class="user-item-card">
                            <div class="user-item-card-icon">
                                <img src="{{ asset('admin/images/payment.svg') }}" />
                            </div>
                            <div class="user-item-card-content">
                                <h3>{{priceFormat($total_payment)}}</h3>
                                <p>Total Payment Received</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="user-item-card">
                            <div class="user-item-card-icon">
                                <img src="{{ asset('admin/images/paid-user.svg') }}" />
                            </div>
                            <div class="user-item-card-content">
                                <h3>{{priceFormat($premium_payment)}}</h3>
                                <p>Premium Member Payment Received</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="user-item-card">
                            <div class="user-item-card-icon">
                                <img src="{{ asset('admin/images/paid-user.svg') }}" />
                            </div>
                            <div class="user-item-card-content">
                                <h3>{{priceFormat($elit_payment)}}</h3>
                                <p>Elite Member Payment Received</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="user-table">
                <table>
                    <tbody>
                        @forelse($subscribe_members as $member)
                        <tr>
                            <td>
                                <div class="user-table-info">
                                    <div class="user-table-media">
                                        @if(empty($member->profile_image))
                                            <img src="{{ asset('admin/images/profile.png') }}" />
                                        @else
                                            <img src="{{ asset($member->profile_image) }}" />
                                        @endif

                                    </div>
                                    <div class="user-table-value">
                                        <h2>{{ucwords($member->name)}}</h2>
                                        <div class="forms-text">{{ucwords($member->plan_name)}}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="table-text-info">
                                    <div class="table-info-heading">{{$member->phone}}</div>
                                    <div class="table-info-heading">{{$member->email}}</div>
                                </div>
                            </td>
                            <td>
                                <div class="table-text-info">
                                    <div class="forms-text">Signup Date: 
                                        {{date('M d, Y',strtotime($member->created_at))}}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="table-text-info">
                                    <div class="table-info-staus">Total Payment Received: {{getTotalAmountBySubscription($member->subscribe_member_id,$member->price)}}</div>
                                </div>
                            </td>

                            {{-- <td>
                                <a href="javascript:void(0)" class="action-btn"> <i class="las la-eye"></i> View Payment Details</a>
                            </td> --}}
                        </tr>
                        @empty
                        <tr>
                            <td><div class="text-center text-success">No Data Available</div></td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pagination-list-info mt-3 pull-right">{{$subscribe_members->appends(Request::except('page'))->links()}}</div>
    </div>
</div>
@endsection
@push('css')
<link rel="stylesheet" href="{{ asset('admin/css/payment.css') }}">
@endpush