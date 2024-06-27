@extends('layouts.app')

@push('css')
<link rel="stylesheet" type="text/css" href="{{ asset('admin/css/users.css') }}"> 
@endpush

@section('content')
<div class="main-panel">
    <div class="heading-section">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h4 class="heading-title">Logs</h4>
            </div>
        </div>
    </div>
    <div class="user-data-section">
       <!--  <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#home" class="btn btn-success">Info Query Logs</a></li>
            <li><a data-toggle="tab" href="#menu1" class="btn btn-danger">Error Query Logs</a></li>
        </ul> -->

        <div class="tab-content">
            <div id="home" class="tab-pane fade">
                <div class="box-header">                
                    <a href="{{ route('admin.clean.logs', 'file=laravel') }}" class="btn btn-red">
                        <span class="fa fa-history"></span> Clean file
                    </a>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="box-body">
                            <div class="table-container">
                                <table id="table-log" class="table table-striped table-hover table-sm" data-ordering-index="0">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Context</th>
                                        </tr>
                                    </thead>
                                    @forelse($data['info_logs'] as $key => $log)
                                        <tr>
                                            <td class="date">{{{$log['date']}}}</td>
                                            <td class="text">
                                                {{{$log['text']}}}
                                            </td>
                                        </tr>
                                    @empty
                                    @endforelse
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="menu1" class="tab-pane fade show active">
                <div class="box-header">
                    <a href="{{ route('admin.clean.logs', 'laravel') }}" class="btn btn-red">
                        <span class="fa fa-history"></span> Clean file
                    </a>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="box-body">
                            <div class="table-container">
                                <table id="table-log" class="table table-striped table-hover table-sm" data-ordering-index="0">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Context</th>
                                        </tr>
                                    </thead>
                                    @forelse($data['error_log'] as $key => $log)
                                        <tr>
                                            <td class="date">{{{$log['date']}}}</td>
                                            <td class="text">                                        
                                                {{{$log['text']}}}                                       
                                            </td>
                                        </tr>
                                    @empty
                                    @endforelse
                                </table>
                            </div>
                        </div>
                    </div>
                </div>              
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('admin/css/payment.css') }}">
@endpush