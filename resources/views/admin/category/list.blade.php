@extends('layouts.app')
@section('content')
<div class="main-panel">
    <div class="heading-section">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h4 class="heading-title">Category List</h4>
            </div>
            <div class="btn-option-info">
                <a href="{{ route('admin.category.create') }}" class="btn-ye">Add New Category</a>
            </div>
        </div>
    </div>
    <div class="di-section">
        @include('common.msg')
        <div class="user-table notification-table  table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($list as $row)
                    <tr>
                        <td class="">{{$row->name??'N/A'}}</td>
                        
                        <td>
                            @include('common.status',['status'=>$row->status])
                        </td>
                        <td>
                            <a href="{{ route('admin.category.edit',$row) }}" class="btn-edit-sm"><i class="fa fa-edit"></i></a>
                            <a href="javascript:void(0)" data-url="{{ route('admin.category.delete',$row) }}" class="btn-delete-sm delete-category"><i class="fa fa-trash"></i></a>
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
        {{$list->appends(Request::except('page'))->links()}}
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
        
        $(document).on('click','.delete-category',function(e){
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
</script>
@endpush
