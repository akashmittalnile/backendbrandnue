@extends('layouts.app')
@section('content')
<div class="main-panel">
    {{-- <div class="heading-section">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h4 class="heading-title">Consultation Services</h4>
            </div>
        </div>
    </div> --}}
    @include('common.msg')
    <div class="chat-section">
        @include('admin.chat.customers')
        <div class="chat-panel">
            <div class="no-chat-panel-body">
               
            </div>
        </div>
    </div>
</div>
@endsection
@push('css')
<link rel="stylesheet" href="{{ asset('admin/css/chat.css') }}">
@endpush