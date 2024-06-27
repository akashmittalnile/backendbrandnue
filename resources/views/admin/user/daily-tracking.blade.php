@extends('layouts.app')
@section('content')
<div class="main-panel">
    <div class="tracking-report-section">
        <div class="row">
            <div class="col-md-12">
                <a href="{{ route('admin.user.list') }}" class="plan-text pull-right">Back</a>
            </div>
        </div>
        <div class="tracking-report-user">
            <div class="row">
                <div class="col-md-6">
                    <div class="bn-user-info">
                        <div class="bn-user-text">
                            <h2>{{$user->name}}</h2>
                            <div class="plan-text">
                                @if($plan)
                                    {{ucwords($plan->name)}}
                                @else
                                    N/A
                                @endif
                            </div>
                            <div class="add-note-btn">
                                <a href="{{ route('admin.user.daily-traking-note',$user) }}">Add profile Note <span class="badge bg-danger" style="position: absolute;top: -11px;right: -11px;">{{$user->notes->count()}}</span></a>
                            </div>
                            <div class="bn-user-point-item">
                                <ul class="bn-contact-list">
                                    <li>
                                        <a href="#"><i class="las la-phone"></i> <span>{{$user->phone}}</span></a>
                                    </li>
                                    <li>
                                        <a href="#"><i class="las la-envelope"></i> <span>{{$user->email}}</span></a>
                                    </li>
                                </ul>
                            </div>
                            <div class="bn-user-point-item">
                                <ul class="bn-contact-list">
                                    <li>
                                        <a href="#"><i class="las la-calendar-alt"></i> <span>Start Date: {{dateFormat($user->created_at)}}</span></a>
                                    </li>
                                    {{-- <li>
                                        <a href="#"><i class="las la-calendar-alt"></i> <span>End Date: 16-06-20121</span></a>
                                    </li> --}}
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="bn-user-info">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="bn-weight-achivement">
                                    <h2>{{$weight_loss_achievement_text}}</h2>
                                    <div class="bn-weight-achivement-body">
                                        <div class="bn-weight-achivement-info">
                                            <div class="bn-weight-box">
                                                <div class="bn-weight-box-icon">
                                                    <img src="{{ asset('admin/images/n-40.svg') }}" />
                                                </div>
                                                <div class="bn-weight-box-content">
                                                    <h3>{{$percentage}}% to Goal</h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="bn-starting-weight">
                                            <div class="bn-starting-icon">
                                                <i class="las la-weight"></i>
                                            </div>
                                            <div class="bn-starting-content">
                                                <div class="bn-starting-text">Starting Weights</div>
                                                <div class="bn-starting-value">Pounds: {{number_format(intval($user->current_weight),1)}}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="bn-starting-weight">
                                            <div class="bn-starting-icon">
                                                <i class="las la-clock"></i>
                                            </div>
                                            <div class="bn-starting-content">
                                                <div class="bn-starting-text">Average Fastime</div>
                                                <div class="bn-starting-value">{{$fast_time_current_month_hours}}hrs</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bn-weight-footer">
                            <div class="bn-weight-text">
                                {{$weight_loss_to_date_text}}
                            </div>
                            <div class="bn-weight-value">
                                {{number_format($weight_loss_to_date,1)}} lbs
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tracking-report-filter">
            <form action="">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Form Date</label>
                            <input type="date" name="f" class="form-control" value="{{request()->f}}"/>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>To Date</label>
                            <input type="date" name="t" class="form-control" value="{{request()->t}}"/>
                        </div>
                    </div>
                    <div class="col-md-4">
                            <div class="form-group mt-4">
                                <button class="btn btn-primary">Search</button>
                                <a href="{{ route('admin.user.daily-traking',$user) }}" class="btn btn-danger">Reset</a>
                            </div>
                        </div>
                </div>
            </form>
        </div>
        <div class="tracking-report-table table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        
                        <th style="white-space: nowrap;">Date</th>
                        <th style="white-space: nowrap;">Weight</th>
                        <th>Supplement(s)</th>
                        <th style="white-space: nowrap;">Food</th>
                        <th style="white-space: nowrap;">Water</th>
                        <th>Bowel Movement</th>
                        {{-- <th>Notes</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @forelse($trackings as $tracking)
                    <tr>
                        
                        <td style="white-space: nowrap;">{{dateFormat($tracking->track_date)}}</td>
                        <td style="white-space: nowrap;">
                            @if(!empty($tracking->current_day_weight))
                                {{$tracking->current_day_weight}} lbs
                            @else
                                N/A
                            @endif
                        </td>

                        <td>
                            
                                @if($tracking->supplement)
                                    {{join(", ",Arr::pluck($tracking->supplement,'name'))}}
                                @else
                                    N/A
                                @endif
                                
                            
                        </td>
                        
                        
                        <td style="white-space: nowrap;">
                            @if(!empty($tracking->breakfast) && isset($tracking->breakfast['food_type']) && !empty($tracking->breakfast['food_type']))
                                @php
                                    $breakfast_list = $tracking->breakfast;
                                @endphp
                                <div class="table-food-info text-center">
                                    <h3>Break Fast{{-- : 
                                        @if(isset($breakfast_list['start_time']['hh']))
                                            {{join(':',$breakfast_list['start_time'])}}
                                        @endif --}}
                                    </h3>
                                    @if(isset($breakfast_list['food_type']) && count($breakfast_list['food_type'])>0)
                                        @foreach($breakfast_list['food_type'] as $breakfast)
                                            <p class="text-center"><b>{{$breakfast['foodName']??"N/A"}}:</b> {{$breakfast['Quantity']??0}} Ounce</p>
                                        @endforeach
                                    @else
                                        N/A
                                    @endif
                                    
                                </div>
                            @endif
                            @if(!empty($tracking->lunch) && isset($tracking->lunch['food_type']) && !empty($tracking->lunch['food_type']))
                                @php
                                    $lunch_list = $tracking->lunch;
                                @endphp
                                <div class="table-food-info text-center">
                                    <h3>Lunch{{-- : 
                                        @if(isset($lunch_list['start_time']['hh']))
                                            {{join(':',$lunch_list['start_time'])}}
                                        @endif --}}
                                    </h3>
                                    @if(isset($lunch_list['food_type']) && count($lunch_list['food_type'])>0)
                                        @foreach($lunch_list['food_type'] as $lunch)
                                            <p class="text-center"><b>{{$lunch['foodName']??"N/A"}}:</b> {{$lunch['Quantity']??0}} Ounce</p>
                                        @endforeach
                                    @else
                                        N/A
                                    @endif
                                    
                                </div>
                            @endif
                            @if(!empty($tracking->snack) && isset($tracking->snack) && !empty($tracking->snack))
                                @php
                                    $snack_list = $tracking->snack;
                                    if(isset($snack_list[0]['foodName']) && empty($snack_list[0]['foodName'])){
                                        continue;
                                    }
                                @endphp
                                <div class="table-food-info text-center">
                                    <h3>Snack{{-- : 
                                        @if(isset($snack_list['start_time']['hh']))
                                            {{join(':',$snack_list['start_time'])}}
                                        @endif --}}
                                    </h3>
                                    @if(isset($snack_list) && count($snack_list)>0)
                                        @foreach($snack_list as $snack)
                                            <p class="text-center"><b>{{$snack['foodName']??"N/A"}}:</b> 
                                                @if(isset($snack['Quantity']) && !empty($snack['Quantity']))
                                                    {{$snack['Quantity']??0}} Ounce
                                                @endif
                                            </p>
                                        @endforeach
                                    @else
                                        N/A
                                    @endif
                                    
                                </div>
                            @endif
                            @if(!empty($tracking->dinner) && isset($tracking->dinner['food_type']) && !empty($tracking->dinner['food_type']))
                                @php
                                    $dinner_list = $tracking->dinner;
                                @endphp
                                <div class="table-food-info text-center">
                                    <h3>Dinner{{-- : 
                                        @if(isset($dinner_list['start_time']['hh']))
                                            {{join(':',$dinner_list['start_time'])}}
                                        @endif --}}
                                    </h3>
                                    @if(isset($dinner_list['food_type']) && count($dinner_list['food_type'])>0)
                                        @foreach($dinner_list['food_type'] as $dinner)
                                            <p class="text-center"><b>{{$dinner['foodName']??"N/A"}}:</b> {{$dinner['Quantity']??0}} Ounce</p>
                                        @endforeach
                                    @else
                                        N/A
                                    @endif
                                    
                                </div>
                            @endif
                        </td>
                        
                        <td style="white-space: nowrap;">{{$tracking->water_intake}} ounce</td>
                        <td>{{$tracking->bowel_movement=='Y'?"Yes":'No'}}</td>
                        {{-- <td><p>In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form.</p></td> --}}
                    </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="text-center text-success">No Data Available</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="row">
            <div class="col-md-12">
                {{$trackings->appends(Request::except('page'))->links()}}
            </div>
        </div>
    </div>
</div>
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('admin/css/tracking-report.css') }}">
@endpush