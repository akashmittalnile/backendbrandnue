@extends('layouts.app')
@section('content')
<div class="main-panel">
    <div class="heading-section">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h4 class="heading-title">Notification List</h4>
            </div>
            <div class="btn-option-info">
                <a href="{{ route('admin.notification.create') }}" class="btn-ye">Add New Notification</a>
            </div>
        </div>
    </div> 
    <div class="di-section">
        @include('common.msg')
        <div class="user-table notification-table  table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Plan</th>
                        <th>Short Description</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notifications as $row)
                    <tr>
                        <td class="">{{$row->title??'N/A'}}</td>
                        <td>{{$row->plan->name}}</td>
                        <td>{{Str::words($row->data, 10, ' .....')}}</td>
                        <td>
                            @include('common.status',['notify'=>$row->status])
                        </td>
                        <td>
                            @if($row->status==config('constant.status.in_active'))
                                <a href="{{ route('admin.notification.edit',$row) }}" class="btn-edit-sm"><i class="fa fa-edit"></i></a>
                            @endif
                            <a href="{{ route('admin.notification.show',$row) }}" class="btn-view-sm"><i class="fa fa-eye"></i></a>
                            <a href="javascript:void(0)" data-url="{{ route('admin.notification.delete',$row) }}" class="btn-delete-sm delete-notification"><i class="fa fa-trash"></i></a>
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
        {{$notifications->appends(Request::except('page'))->links()}}
    </div>
</div>
@endsection
@push('css')
<link rel="stylesheet" href="{{ asset('admin/css/recipe.css') }}">
@endpush
@push('js')
<script src="{{asset('plugins/js/sweetalert.min.js')}}"></script>
<script>
    $(document).ready(function(){
        
        $(document).on('click','.delete-notification',function(e){
            e.preventDefault();
            const url = $(this).data('url');
            if(url){
                swal({
                    title:"Are you sure?",
                    text: "You won't be able to revert this",
                    // icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((isConfirmed) => {
                    if(isConfirmed){
                        $('.please-wait').show();
                        $.ajax({
                            type:'delete',
                            url : url,
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            success: function(res){
                                if(res.status==true){
                                    window.location.reload();
                                }
                                $('.please-wait').hide();
                            }
                        });
                    }
                });
            }
        });
        
    });

    /*title: 'Are you sure?',
    text: "You won't be able to revert this!",
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#0d0d0d',
    cancelButtonColor: '#fc4a26',
    confirmButtonText: 'Yes, delete it!'*/
</script>
@endpush
