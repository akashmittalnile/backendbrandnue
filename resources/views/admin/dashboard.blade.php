@extends('layouts.app')
@section('content')
<div class="main-panel">
    <div class="overview-section">
        <div class="row">
            <div class="col">
                <div class="overview-item">
                    <div class="overview-item-icon">
                        <img src="{{ asset('admin/images/digital-library.svg') }}" />
                    </div>
                    <div class="overview-item-content">
                        <h2>{{$total_digital_library}}</h2>
                        <p>Digital Library</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="overview-item">
                    <div class="overview-item-icon">
                        <img src="{{ asset('admin/images/forminquiries.svg') }}" />
                    </div>
                    <div class="overview-item-content">
                        <h2>{{$total_daily_trackers}}</h2>
                        <p>Daily Tracker</p>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="overview-item">
                    <div class="overview-item-icon">
                        <img src="{{ asset('admin/images/users.svg') }}" />
                    </div>
                    <div class="overview-item-content">
                        <h2>{{$total_users}}</h2>
                        <p>Total Users</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="overview-item">
                    <div class="overview-item-icon">
                        <img src="{{ asset('admin/images/membershipplans.svg') }}" />
                    </div>
                    <div class="overview-item-content">
                        <h2>{{$total_membership_plans}}</h2>
                        <p>Membership Plans</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="chat-section">
        <div class="chart-header">
            
            {{-- <div class="chart-heading">
                <h2>Membership Payments</h2>
            </div>
            <div class="chart-filter">
                <div class="row">
                    <div class="col-md-3">
                        <label>Selet Year</label>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <select name="year" id="" class="form-control">
                                <option value="">Select</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <button>Search</button>
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>

        <div class="chart-body">
            <div class="chat-media" id="line-chart-demo">
                {{-- <img src="{{ asset('admin/images/chart.png') }}" /> --}}
            </div>
        </div>
    </div>
</div>
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('admin/css/d3-instant-charts.css') }}">
@endpush
@push('js')
    <script src="{{ asset('plugins/js/d3.v5.min.js') }}"></script>
    <script src="{{ asset('admin/js/d3-instant-charts.js') }}"></script>
    <script>
        $(document).ready(function(){
            $('#line-chart-demo').lineChart({
                jsonUrl: '{{ route('admin.dashboard.charts',['year'=>request('year')]) }}'
            });
        });
    </script>
@endpush