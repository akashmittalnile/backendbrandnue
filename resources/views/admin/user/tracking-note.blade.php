@extends('layouts.app')
@section('content')
<div class="main-panel">
    <div class="heading-section">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                
            </div>
            <div class="btn-option-info">
                <a href="{{ route('admin.user.daily-traking',$user) }}" class="btn-ye">Back</a>
            </div>
        </div>
    </div>
    <div class="note-form-card">
        <h2>Flag the note</h2>
        <form action="" method="post">
            @csrf
            <div class="note-item-list">
                <div class="note-item">
                    <div class="nueradio">
                        <input type="radio" id="Red" name="color" value="red-border" @if(old('color')=='red-border') checked @endif/>
                        <label for="Red"><div class="color-bg red-bg"></div></label>
                    </div>
                </div>
                <div class="note-item">
                    <div class="nueradio">
                        <input type="radio" id="Yellow" name="color" value="yellow-border" @if(old('color')=='yellow-border') checked @endif />
                        <label for="Yellow"><div class="color-bg yellow-bg"></div></label>
                    </div>
                </div>
                <div class="note-item">
                    <div class="nueradio">
                        <input type="radio" id="Green" name="color" value="green-border" @if(old('color')=='green-border') checked @endif/>
                        <label for="Green"><div class="color-bg green-bg"></div></label>
                    </div>
                </div>

            </div>
            <span class="error text-danger">{{$errors->first('color')}}</span>
            <div class="form-group mb-3 col-md-3">
                <label>Date</label>
                <input type="date" name="date" class="form-control {{$errors->first('date')?'is-invalid':''}}" value="{{old('date')}}"/>
                <span class="{{$errors->first('date')?'error invalid-feedback':''}}">{{$errors->first('date')}}</span>
            </div>
            <div class="form-group mb-3">
                <label>Notes</label>
                <textarea name="note" class="form-control {{$errors->first('note')?'is-invalid':''}}">{{old('note')}}</textarea>
                <span class="{{$errors->first('note')?'error invalid-feedback':''}}">{{$errors->first('note')}}</span>
            </div>
            <div class="form-group mb-3">
                <button class="btn-save" name="save">Save</button>
            </div>
        </form>
    </div>
    @if($notes->count())
    <div class="note-list-card">
        @include('common.msg')
        <h2>Note List</h2>
        <div class="note-list-row">
            @foreach($notes as $note)
            <div class="note-list-col {{$note->name}}">
                <div class="d-block">
                    <span class="badge bg-success p-1">{{dateFormat($note->note_date)}}</span>
                    <p>
                        {{$note->description}}
                    </p>
                </div>
                <a href="javascript:void(0)" class="close m-2 p-2 delete-note" data-url="{{ route('admin.user.daily-traking-note-delete',['user'=>$note->noteable_id,'note'=>$note->id]) }}">×</a>
            </div>
            @endforeach
            {{-- <div class="note-list-col yellow-border">
                <div class="d-block">
                    <p>
                        In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a
                        placeholder before final copy is available.
                    </p>
                </div>
                <a href="#!" class="close m-2 p-2">×</a>
            </div>
            <div class="note-list-col green-border">
                <div class="d-block">
                    <p>
                        In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a
                        placeholder before final copy is available.
                    </p>
                </div>
                <a href="#!" class="close m-2 p-2">×</a>
            </div> --}}
        </div>
    </div>
    @endif
</div>
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('admin/css/tracking-report.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/css/recipe.css') }}">
@endpush

@push('js')
<script src="{{asset('plugins/js/sweetalert.min.js')}}"></script>
<script>
    $(document).ready(function(){
        
        $(document).on('click','.delete-note',function(e){
            e.preventDefault();
            const url = $(this).data('url');
            if(url){
                swal({
                    title:"Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
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