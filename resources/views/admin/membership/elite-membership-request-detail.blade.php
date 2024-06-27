<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Current Status 
        </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="viewdetails-info">
                <div class="viewdetails-form-group">
                    <div class="row">
                        @if(!empty($member->eliteMemberByAdmin))
                        <div class="col-md-3 viewdetails-text">
                            <h2>{{config('constant.elite_member_request_status')[$member->status]}}</h2>
                        </div>
                        <select id="current-status-change" style="display:none;">
                            @foreach(config('constant.elite_member_request_status') as $key=>$value)
                                <option value="{{$key}}" @if($member->status==$key) selected @endif>{{$value}}</option>
                            @endforeach
                        </select>
                        <div class="col-md-2">
                            {{dateFormat($member->eliteMemberByAdmin->activated_date)}}
                        </div>
                        <div class="col-md-2 text-center"><b>To</b></div>
                        <div class="col-md-2">
                            {{dateFormat($member->eliteMemberByAdmin->renewal_date)}}
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-danger btn-sm edit-become-member-by-admin" data-start="{{$member->eliteMemberByAdmin->activated_date??''}}" data-end="{{$member->eliteMemberByAdmin->renewal_date??''}}">Edit</button>
                            <button class="btn btn-info btn-sm cancel-become-member-by-admin" style="display:none;">Cancel</button>
                        </div>
                        @else
                        <div class="col-md-8">
                            <select id="current-status-change" class="form-select">
                                <option value="">Select Status</option>
                                @foreach(config('constant.elite_member_request_status') as $key=>$value)
                                    <option value="{{$key}}" @if($member->status==$key) selected @endif>{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 elite-member-status" style="display:none">
                            <div class="form-group">
                                <button class="btn-Search elite-member-button" data-status="{{$member->status}}" data-url="{{route('admin.membership.elite-member.post',$member)}}" data-id="{{$member->id}}">Submit</button>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="row mt-3 become-elite-member-status" style="display:none">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Start Date</label>
                                <input type="date" class="form-control" name="start_date" placeholder="Start Date" id="_start_date">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>End Date</label>
                                <input type="date" class="form-control" name="end_date" placeholder="End Date" id="_end_date">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mt-4">
                                <button class="btn-Search become-elite-member-button" data-status="{{$member->status}}" data-url="{{route('admin.membership.elite-member.post',$member)}}" data-id="{{$member->id}}">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="viewdetails-text">
                    <h2>{{$member->name}}</h2> 
                    <div class="viewdetails-point-item">
                        <ul class="viewdetails-list">
                            <li><a href="#"><i class="las la-phone"></i> <span>{{$member->phone}}</span></a></li>
                            <li><a href="#"><i class="las la-envelope"></i> <span>{{$member->email}}</span></a></li>
                        </ul>
                        <ul class="viewdetails-list">
                            <li><a href="#"><i class="las la-map-marker-alt"></i> <span>{{$member->city}}, {{$member->state}}</span></a></li>
                            <li><a href="#"><i class="las la-calendar"></i> <span>{{dateFormat($member->created_at)}}</span></a></li>
                        </ul>
                    </div>
                    <div class="viewdetails-text">
                        <h2>Message</h2>
                        <p>{{$member->message}}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
