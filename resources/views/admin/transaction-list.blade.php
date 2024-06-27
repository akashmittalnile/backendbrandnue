@extends('layouts.app')

@push('css')
<link rel="stylesheet" type="text/css" href="{{ asset('admin/css/users.css') }}"> 
@endpush

@section('content')
<div class="main-panel">
    <div class="heading-section">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h4 class="heading-title">Transaction</h4>
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
                                <img src="{{ asset('admin/images/free-user.svg') }}" />
                            </div>
                            <div class="user-item-card-content">
                                <h3>{{count($total_members)}}</h3>
                                <p>Total Member</p>
                            </div>
                        </div>
                    </div>                    
                </div>
            </div>
            <div class="user-table-filter">
                <form action="">
                    <div class="row g-2">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Select Membership</label> 
                                <select class="form-select" name="p">
                                    <option value="">Select</option>
                                    @forelse($plans as $plan)
                                        <option value="{{$plan->id}}" @if(request()->p==$plan->id) selected @endif>{{$plan->name}}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Name / Email</label>
                                <input type="text" name="n" class="form-control" placeholder="Name / Email" value="{{request()->n}}" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Payment Device</label>
                                <input type="text" name="s" class="form-control" placeholder="Payment Device" value="{{request()->s}}" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Form Date</label>
                                <input type="date" name="f" class="form-control" value="{{request()->f}}"/>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>To Date</label>
                                <input type="date" name="t" class="form-control" value="{{request()->t}}"/>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mt-4">
                                <button class="btn-Search">Search</button>
                                <a href="{{ route('admin.transaction.list') }}" class="btn-Reset">Reset</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            @include('common.msg')
            <div class="user-table">
                <table>
                    <tbody>
                        @forelse($subscribe_members as $member)
                        <tr>
                            <td>
                                <div class="user-table-info">
                                    <div class="user-table-media">
                                        @if(empty($member->user->profile_image))
                                            <img src="{{ asset('admin/images/profile.png') }}" />
                                        @else
                                            <img src="{{ asset($member->profile_image) }}" />
                                        @endif
                                    </div>
                                    <div class="user-table-value">
                                        <h2>{{ucwords($member->planName->name ?? '')}}</h2>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="table-text-info">
                                    <div class="table-info-heading">{{$member->user->phone ?? ''}}</div>
                                    <div class="table-info-heading">{{$member->user->email ?? ''}}</div>
                                </div>
                            </td>
                            <td>
                                <div class="table-text-info">
                                    <div class="forms-text">Signup Date: 
                                        {{date('M d, Y',strtotime($member->user->created_at))}}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="table-text-info">
                                    <div class="table-info-staus">Amount: {{ $member->transaction->price ?? 0 }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="table-text-info">
                                    <div class="forms-text">Payment Status: 
                                        <b>{{ $member->transaction->payment_status ?? '' }}</b>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="table-text-info">
                                    <div class="forms-text">Payment Device: 
                                        <b>{{ $member->device ?? '' }}</b>
                                    </div>
                                </div>
                            </td>
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