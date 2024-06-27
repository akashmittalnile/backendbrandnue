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
                            <h3>Break Fast
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
                            <h3>Lunch
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
                            <h3>Snack
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
                            <h3>Dinner
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