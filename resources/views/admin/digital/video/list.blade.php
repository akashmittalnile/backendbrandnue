@extends('layouts.app')
@section('content')
<div class="main-panel">
    <div class="heading-section">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h4 class="heading-title">Instructional Video List</h4>
            </div>
            <div class="btn-option-info">
                <a href="{{ route('admin.instructional.video.create') }}" class="btn-ye">Add New Instructional Video</a>
            </div>
        </div>
    </div>
    <div class="di-section">
        @include('common.msg')
        <div class="user-table table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Plan</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($videos as $row)
                    <tr>
                        <td class="">{{$row->title??'N/A'}}</td>
                        <td class="">{{getStaticSubscription()[$row->subscription_type]??'All'}}</td>
                        <td>{{Str::words(strip_tags(html_entity_decode($row->description)), 10, ' .....')}}</td>
                        <td>
                            @include('common.status',['status'=>$row->status])
                        </td>
                        <td>
                            <a href="{{ route('admin.instructional.video.edit',$row) }}" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i></a>
                            <a href="{{ route('admin.instructional.video.show',$row) }}" class="btn btn-success btn-sm"><i class="fa fa-eye"></i></a>
                            <a href="javascript:void(0)" data-url="{{ route('admin.instructional.video.delete',$row) }}" class="btn btn-danger btn-sm delete-video"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5"><div class="text-center">No Data Available</div></td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pull-right">
            {{$videos->appends(Request::except('page'))->links()}}
        </div>
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
        
        $(document).on('click','.delete-video',function(e){
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
                                }else{
                                    swal({
                                        title:"Alert",
                                        text:res.msg,
                                        // icon: "warning",
                                        dangerMode: true,
                                    });
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
