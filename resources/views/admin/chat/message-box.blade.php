@if($chats && count($chats)>0)
	@foreach($chats as $chat)
		@if($chat['userId']!=1)
        <div class="message-item">
            <div class="message-item-card">
                <div class="message-options">
                    <div class="avatar"><img alt="" src="{{ asset('admin/images/avatar-fch_9.png') }}" /></div>
                </div>
                <div class="message-wrapper">
                    <div class="message-content">
                        @if(isset($chat['image']) && !empty($chat['image']))
                        <div class="message-images"><img alt="" src="{{ asset($chat['image']) }}" /></div>
                        @endif
                        <p>{{$chat['message']??''}}</p>
                    </div>
                    <div class="time-text">{{$chat['time']}}</div>
                </div>
            </div>
        </div>
        @else
        <div class="message-item outgoing-message">
            <div class="message-item-card">
                <div class="message-options">
                    <div class="avatar"><img alt="" src="{{ asset('admin/images/avatar-fch_9.png') }}" /></div>
                </div>
                <div class="message-wrapper">
                    <div class="message-content">
                        @if(isset($chat['image']) && !empty($chat['image']))
                        <div class="message-images"><img alt="" src="{{ asset($chat['image']) }}" /></div>
                        @endif
                        <p>{{$chat['message']??''}} </p>
                    </div>
                    <div class="time-text">{{$chat['time']}}</div>
                </div>
            </div>
        </div>
        @endif
    @endforeach
@endif