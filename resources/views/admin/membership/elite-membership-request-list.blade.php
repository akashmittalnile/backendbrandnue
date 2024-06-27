@extends('layouts.app')
@section('content')
<div class="main-panel">
    <div class="heading-section">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h4 class="heading-title">Elite Member Request</h4>
            </div>
            <div class="btn-option-info">
                <div class="search-group">
                </div>
            </div>
        </div>
    </div>
    <div class="user-data-section">
        <div class="user-list-item">
            <div class="user-table-filter">
                <form action="">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Status</label>
                                <select class="form-select" name="status">
                                    <option value="">Select Status</option>
                                    @foreach(config('constant.elite_member_request_status') as $key=>$value)
                                        <option value="{{$key}}" @if(request()->status==$key) selected @endif>{{$value}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Form Date</label>
                                <input type="date" name="f" class="form-control" value="{{request()->f}}" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>To Date</label>
                                <input type="date" name="t" class="form-control" value="{{request()->t}}" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mt-4">
                                <button class="btn-Search">Search</button>
                                <a href="{{ route('admin.membership.elite-membership-request-list') }}" class="btn-Reset">Reset</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="user-table">
                <table>
                    <tbody>
                        @forelse($elit_member_requests as $row)
                        <tr @if($row->read_status==1) style="background: #ff000026;" @endif>
                            <td>
                                <div class="user-table-info">
                                    <div class="user-table-media">
                                        @if(isset($row->user->profile_image) && !empty($row->user->profile_image))
                                        <img src="{{ asset($row->user->profile_image) }}" />
                                        @else
                                        <img src="{{ asset('admin/images/avatar-fch_9.png') }}" />
                                        @endif
                                    </div>
                                    <div class="user-table-value">
                                        <h2>{{$row->name}}</h2>
                                        @if($row->read_status==1)
                                            <span class="badge text-danger new-{{$row->id}}">New</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="table-text-info">
                                    <div class="table-info-heading">{{$row->phone}}</div>
                                    <div class="table-info-heading">{{$row->email}}</div>
                                </div>
                            </td>

                            <td>
                                <div class="table-text-info">
                                    <div class="table-info-address"><i class="las la-map-marker"></i> {{$row->city}}, {{$row->state}}</div>
                                </div>
                            </td>
                            <td>
                                <div class="table-text-info">
                                    <div class="table-info-staus">Status: <span class="new-status-{{$row->id}}">{{config('constant.elite_member_request_status')[$row->status]??''}}</span></div>
                                    <div class="table-info-heading">Request Date: {{dateFormat($row->created_at)}}</div>
                                    <div class="table-info-heading response-{{$row->id}}">Response Date: @if(!empty($row->response_date)) {{dateFormat($row->response_date)}} @else N/A @endif</div>
                                </div>
                            </td>
                            <td style="white-space: nowrap;">
                                <a  href="javascript:void(0)" class="action-btn request-member-details" data-id='{{$row->id}}' data-url="{{ route('admin.membership.elite-member.modal',$row) }}"><i class="las la-eye"></i>View Details</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">
                                <div class="text-center text-success">No Data Available</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table> 
            </div>
            {{$elit_member_requests->appends(Request::except('page'))->links()}}
        </div>
    </div>
</div>
<div class="modal fade NUE-modal" id="viewdetails" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        
</div>
@endsection
@push('css')
<link rel="stylesheet" type="text/css" href="{{ asset('admin/css/elite-member-request.css') }}"> 
@endpush
@push('js')
<script src="{{asset('plugins/js/sweetalert.min.js')}}"></script>
<script>
    $(document).ready(function(){
        $(document).on('click','.request-member-details',function(e){
            const url = $(this).data('url');
            let id = $(this).data('id');
            let self = $(this);
            if(url){
                $.ajax({
                    url:url,
                    success: function(res){
                        self.closest('tr').removeAttr('style');
                        $('.new-'+id).remove();
                        $('#viewdetails').html(res.html);
                        $('#viewdetails').modal('show');
                    }
                })
            }
        });
        /*------------------------------------------------------------------------------------------*/
        $(document).on('change','#current-status-change',function(e){
            const change_status = $(this).val();
            if(!change_status){
                $('.elite-member-status').hide();
                $('.become-elite-member-status').hide();
                return false;
            }else if(change_status=='elite_member'){
                $('.elite-member-status').hide();
                $('.become-elite-member-status').show();
            }else{
                $('.elite-member-status').show();
                $('.become-elite-member-status').hide();
            }

            
        })
        /*------------------------------------------------------------------------------------------*/

        $(document).on('click','.become-elite-member-button',function(e){
            const url = $(this).data('url');
            const id = $(this).data('id');
            const previous_status = $(this).data('status');
            const change_status = $('#current-status-change').val();
            let selectedvalue = $('#current-status-change').children('option:selected').text();
            let self = $(this);
            const start_date = $('#_start_date').val();
            const end_date = $('#_end_date').val();
            if(!start_date){
                alert('Please select start date');
                return false;
            }else if(!end_date){
                alert('Please select end date');
                return false;
            }else if(new Date(start_date)>new Date(end_date)){
                alert('End date should be greater then start date');
                return false;
            }
            
            if(url){
                swal({
                    title:"Are you sure?",
                    text: "You want to change the request status",
                    //icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((isConfirmed) => {
                    if(isConfirmed){
                        $('.please-wait').show();
                        $.ajax({
                            type:'post',
                            url : url,
                            data: {status:change_status,start_date:start_date,end_date:end_date},
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            dataType: 'json',
                            success: function(res){
                                if(res.status==true){
                                    $('.response-'+id).text(res.response);
                                    $('.new-status-'+id).text(selectedvalue);
                                    $('#viewdetails').modal('hide');
                                }else{
                                    alert(res.msg);
                                }
                                $('.please-wait').hide();
                            }
                        });
                    }
                });
            }
        })
        /*------------------------------------------------------------------------------------------*/
        $(document).on('click','.elite-member-button',function(e){
            const url = $(this).data('url');
            const id = $(this).data('id');
            const previous_status = $(this).data('status');
            const change_status = $('#current-status-change').val();
            let selectedvalue = $('#current-status-change').children('option:selected').text();
            let self = $(this);
                        
            if(url){
                swal({
                    title:"Are you sure?",
                    text: "You want to change the request status",
                    //icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((isConfirmed) => {
                    if(isConfirmed){
                        $('.please-wait').show();
                        $.ajax({
                            type:'post',
                            url : url,
                            data: {status:change_status},
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            dataType: 'json',
                            success: function(res){
                                if(res.status==true){
                                    $('.response-'+id).text(res.response);
                                    $('.new-status-'+id).text(selectedvalue);
                                    $('#viewdetails').modal('hide');
                                }else{
                                    alert(res.msg);
                                }
                                $('.please-wait').hide();
                            }
                        });
                    }
                });
            }
        })
        /*------------------------------------------------------------------------------------------*/
        $(document).on('click','.edit-become-member-by-admin',function(){
            $('.become-elite-member-status').show();
            $('.cancel-become-member-by-admin').show();
            $('.edit-become-member-by-admin').hide();
            const start_date = $(this).data('start');
            const end_date = $(this).data('end');
            $('#_start_date').val(start_date);
            $('#_end_date').val(end_date);
        });

        /*------------------------------------------------------------------------------------------*/
        $(document).on('click','.cancel-become-member-by-admin',function(){
            $('.become-elite-member-status').hide();
            $('.cancel-become-member-by-admin').hide();
            $('.edit-become-member-by-admin').show();

        });
        /*------------------------------------------------------------------------------------------*/
    });
</script>
@endpush