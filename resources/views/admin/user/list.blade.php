@extends('layouts.app')
@section('content')
<div class="main-panel">
    <div class="heading-section">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h4 class="heading-title">Users</h4>
            </div>
            <div class="btn-option-info">
                {{-- <div class="search-group">
                    <input type="text" name="" class="form-control" />
                    <i class="search-form-icon las la-search"></i>
                </div> --}}
                <a class="btn-ye" href="{{ route('admin.user.create') }}">Add New User</a>
            </div>
        </div>
    </div>
    <div class="user-data-section">
        <div class="user-list-item">
            <div class="overview-section">
                <div class="row">
                    <div class="col-md-3">
                        <div class="user-item-card">
                            <div class="user-item-card-icon">
                                <img src="{{ asset('admin/images/group.svg') }}" />
                            </div>
                            <div class="user-item-card-content">
                                <h3>{{$total_users}}</h3>
                                <p>Total Users</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="user-item-card">
                            <div class="user-item-card-icon">
                                <img src="{{ asset('admin/images/free-user.svg') }}" />
                            </div>
                            <div class="user-item-card-content">
                                <h3>{{$non_member_users}}</h3>
                                <p><a href="{{ route('admin.user.list') }}?nm=1">Unsubscribed Members</a></p>
                            </div>
                        </div>
                    </div>
                    @forelse($total_plans as $row)
                    <div class="col-md-3">
                        <div class="user-item-card">
                            <div class="user-item-card-icon">
                                <img src="{{ asset('admin/images/free-user.svg') }}" />
                            </div>
                            <div class="user-item-card-content">
                                <h3>{{$row->totalMemberUser->count()}}</h3>
                                <p>{{$row->name}} Users</p>
                            </div>
                        </div>
                    </div>
                    @empty
                    @endforelse
                </div>
            </div>
            <div class="user-table-filter">
                <form action="" id="frm-sbmit">
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
                                <label for="">Status</label>
                                <select name="s" class="form-select">
                                    <option value="">Select</option>
                                    @foreach(status_array() as $key=>$row)
                                        <option value="{{$key}}" @if(request()->s==$key) selected @endif>{{$row}}</option>
                                    @endforeach
                                </select>
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
                                <input type="hidden" name="export_to" id="export_to" value="">
                                <button class="btn-Search" id="search-btn">Search</button>
                                <a href="{{ route('admin.user.list') }}" class="btn-Reset">Reset</a>
                                <a href="javascript:void(0)" class="btn-Reset" id="dwn-itm-btn"><i class="menu-icon las la-download"></i> Export Data</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            @include('common.msg')
            <div class="user-table table-responsive">
                <table class="table">
                    <tbody>
                    	@forelse($users as $user)
                        
                        <tr>
                            <td>
                                <div class="user-table-info">
                                    <div class="user-table-media">
                                        <img src="{{ asset('admin/images/profile.png') }}" />
                                    </div>
                                    <div class="user-table-value">
                                        <h2>{{$user->name}}</h2>
                                        <div class="forms-text">{{$user->plan_name??''}} Users</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="table-text-info">
                                    <div class="table-info-heading">{{$user->phone}}</div>
                                    <div class="table-info-heading">{{$user->email}}</div>
                                </div>
                            </td>
                            <td>
                                <div class="table-text-info">
                                    <div class="forms-text">Signup Date: {{date('M d, Y',strtotime($user->created_at))}}</div>
                                </div>
                            </td>
                            <td style="white-space: nowrap;">
                                <div class="table-text-info">
                                    <div class="table-info-text">Membership Name:</div>
                                    <div class="table-info-value">{{$user->plan_name??'N/A'}}</div>
                                </div>
                            </td>

                            <td style="white-space: nowrap;">
                                <div class="table-text-info">
                                    <div class="table-info-text">Start Date:</div>
                                    <div class="table-info-value">
                                        @if(!empty($user->activated_date))
                                            {{date('M d, Y',strtotime($user->activated_date))}}
                                        @else
                                        N/A
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td style="white-space: nowrap;">
                                <div class="table-text-info">
                                    <div class="table-info-text">End Date:</div>
                                    <div class="table-info-value">
                                        @if(!empty($user->activated_date))
                                            {{date('M d, Y',strtotime($user->renewal_date))}}
                                        @else
                                        N/A
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td style="white-space: nowrap;">
                                {{-- <a href="javascript:void(0)" class="block-btn"> <i class="las la-ban"></i> Block User</a> --}}
                                
                                    @if($user->status=='Y')
                                        <a class="block-btn border-0 bg-success text-white">Active</a>
                                        <a href="javascript:void(0)" class="block-btn change-user-status" data-url="{{ route('admin.user.status',['user'=>$user->id,'status'=>'N']) }}"> 
                                            CLICK TO IN ACTIVE
                                        </a>
                                    @else
                                        <a class="block-btn border-0 bg-success text-white">In Active</a>
                                        <a href="javascript:void(0)" class="block-btn change-user-status" data-url="{{ route('admin.user.status',['user'=>$user->id,'status'=>'Y']) }}"> 
                                            CLICK TO ACTIVE 
                                        </a>
                                    @endif
                                
                                
                                
                            </td>
                            <td style="white-space: nowrap;">
                                
                                <a href="{{ route('admin.user.show',$user) }}" class="view-btn"><i class="las la-eye"></i> View User Profile</a><br>
                                <a href="{{ route('admin.user.edit',$user) }}" class="view-btn"><i class="las la-edit"></i> Edit User Profile</a><br>
                                <a href="{{ route('admin.user.daily-traking',$user) }}" class="view-btn"><i class="las la-eye"></i> View Daily Tracking Report</a><br>
                                <a href="javascript:void(0)" data-url="{{ route('admin.user.password-reset',$user) }}" class="view-btn text-info reset-password"><i class="la la-refresh"></i> Reset Default Password <b>{{config('constant.defaultPassword')}}</b></a><br>
                                <a href="javascript:void(0)" data-url="{{ route('admin.user.delete',$user) }}" class="view-btn text-danger delete-user"><i class="la la-trash"></i> Delete User</b></a><br>
                                <a href="javascript:void(0)" data-url="{{ route('admin.user.cancel-active-subscription',['user'=>$user,'subscription_id'=>$user->square_payment_subscription_id]) }}" class="view-btn text-danger cancel-user-subscription" data-device="{{$user->device}}"><i class="la la-times"></i> Cancel Subscription</b></a>
                            </td>
                        </tr>
                        @empty 
                            <tr>
                                <td colspan="8"><div class="text-center text-success">No Data Available</div></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pagination-list-info mt-3 pull-right">{{$users->appends(Request::except('page'))->links()}}</div> 
    </div>
</div>
@endsection
@push('css')
<link rel="stylesheet" type="text/css" href="{{ asset('admin/css/users.css') }}"> 
@endpush
@push('js')
<script src="{{asset('plugins/js/sweetalert.min.js')}}"></script>
<script>
    $(document).ready(function(){
        $(document).on('click','.reset-password',function(e){
            const url = $(this).data('url');
            if(url){
                swal({
                    title:"Are you sure?",
                    text: "You want to reset the default password",
                    //icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((isConfirmed) => {
                    if(isConfirmed){
                        $('.please-wait').show();
                        $.ajax({
                            type:'put',
                            url : url,
                            data: {},
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            success: function(res){
                                if(res.status==true){
                                    window.location.reload();
                                }else{

                                    alert(res.msg);
                                }
                                $('.please-wait').hide();
                            }
                        });
                    }
                });
            }
        });
        /*----------------------------------------------------------------------------------*/
        $(document).on('click','.delete-user',function(e){
            const url = $(this).data('url');
            if(url){
                swal({
                    title:"Are you sure?",
                    text: "You won't be able to revert this",
                    //icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((isConfirmed) => {
                    if(isConfirmed){
                        $('.please-wait').show();
                        $.ajax({
                            type:'delete',
                            url : url,
                            data: {},
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            success: function(res){
                                if(res.status==true){
                                    window.location.reload();
                                }else{
                                    alert(res.msg);
                                }
                                $('.please-wait').hide();
                            }
                        });
                    }
                });
            }
        });

        /*----------------------------------------------------------------------------------*/
        $(document).on('click','.cancel-user-subscription',function(e){
            const url = $(this).data('url');
            const device = $(this).data('device');
            if(device=='Apple'){
                alert('Your subscription has been purchased from InApp, So you can not cancel from here.');
            }else{
                swal({
                    title:"Are you sure?",
                    text: "You want to cancel the subscription",
                    //icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((isConfirmed) => {
                    if(isConfirmed){
                        $('.please-wait').show();
                        $.ajax({
                            type:'put',
                            url : url,
                            data: {},
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            success: function(res){
                                if(res.status==true){
                                    window.location.reload();
                                }else{
                                    alert(res.msg);
                                }
                                $('.please-wait').hide();
                            }
                        });
                    }
                });
            }
        });
        /*----------------------------------------------------------------------------------*/
        $(document).on('click','.change-user-status',function(e){
            const url = $(this).data('url');
            if(url){
                swal({
                    title:"Are you sure?",
                    text: "You want to change the user status",
                    //icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((isConfirmed) => {
                    if(isConfirmed){
                        $('.please-wait').show();
                        $.ajax({
                            type:'patch',
                            url : url,
                            data: {},
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            success: function(res){
                                if(res.status==true){
                                    window.location.reload();
                                }else{
                                    alert(res.msg);
                                }
                                $('.please-wait').hide();
                            }
                        });
                    }
                });
            }
        });
        /*----------------------------------------------------------------------------------*/
        
        $("#dwn-itm-btn").click(function () {
            $('#export_to').val('item-excel');
            $("#frm-sbmit").submit();
        });

        $("#search-btn").click(function () {
            $('#export_to').val('');
        });
    });
</script>
@endpush