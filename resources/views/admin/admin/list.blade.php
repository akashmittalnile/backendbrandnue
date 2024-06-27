@extends('layouts.app')
@section('content')
<div class="main-panel">
    <div class="heading-section">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h4 class="heading-title">Admins</h4>
            </div>
            <div class="btn-option-info">
                {{-- <div class="search-group">
                    <input type="text" name="" class="form-control" />
                    <i class="search-form-icon las la-search"></i>
                </div> --}}
                <a class="btn-ye" href="{{ route('admin.admin.create') }}">Add New Admin</a>
            </div>
        </div>
    </div>
    <div class="user-data-section">
        <div class="user-list-item">
            <div class="user-table-filter"> 
                <form action="">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Name / Email</label>
                                <input type="text" name="n" class="form-control" placeholder="Name / Email" value="{{request()->n}}" />
                            </div>
                        </div>
                        <div class="col-md-4">
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
                        <div class="col-md-4">
                            <div class="form-group mt-4">
                                <button class="btn-Search">Search</button>
                                <a href="{{ route('admin.admin.list') }}" class="btn-Reset">Reset</a>
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
                                        @if($user->profile_image)
                                            <img src="{{ asset($user->profile_image) }}" />
                                        @else
                                            <img src="{{ asset('admin/images/profile.png') }}" />
                                        @endif
                                    </div>
                                    <div class="user-table-value">
                                        <h2>{{$user->name}}</h2>
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
                                @include('common.status',['status'=>$user->status])
                            </td>
                            
                            <td>
                                <a class="btn-edit-sm" href="{{ route('admin.admin.edit',$user) }}" ><i class="fa fa-edit"></i></a>
                                <a class="btn-view-sm" href="{{ route('admin.admin.show',$user) }}" ><i class="fa fa-eye"></i></a>
                                <a href="javascript:void(0)" data-url="{{ route('admin.admin.delete',$user) }}" class="btn-delete-sm delete-admin"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="6"><div class="text-center text-success">No Data Available</div></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3 pull-right">{{$users->appends(Request::except('page'))->links()}}</div>
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
        
        $(document).on('click','.delete-admin',function(e){
            e.preventDefault();
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
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            success: function(res){
                                if(res.status==true){
                                    window.location.reload();
                                }
                                swal({
                                    title:"Alert",
                                    text: res.msg,
                                    //icon: "warning",
                                    buttons: true,
                                    dangerMode: true,
                                })
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