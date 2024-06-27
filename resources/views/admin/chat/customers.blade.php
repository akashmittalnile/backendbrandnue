<div class="user-sidebar">
    <div class="chat-header">
        <h2>Chats</h2>
        <div class="header-user-avatar"></div>
    </div>
    <div class="sidebar-body">
        <div class="search-user-chat">
            <div class="search-group">
                <input type="text" name="" class="form-control" id="search-customer" />
                <i class="search-form-icon las la-search"></i>
            </div>
        </div>
        @php
            $admin = Auth::id();
        @endphp
        <ul class="user-list">
            @forelse($customers as $customer)
            <li>
                <a href="{{ route('admin.customer.chat.detail',$customer) }}">
                    <div class="user-item-info">
                        <div class="user-item-avatar">
                            <img src="{{ asset('admin/images/avatar-fch_9.png') }}" alt="image" />
                        </div>
                        <div class="user-item-content">
                            <h3>
                                {{$customer->name}}
                                @if($customer->admin_id==$admin && $customer->total>0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{$customer->total}}
                                </span>
                                @endif
                            </h3>
                            <p>{{$customer->email}}</p>
                        </div>
                    </div>
                </a>
            </li>
            @empty
            <li>
                <div class="user-item-info">
                    <div class="user-item-content">
                        <h3>No elite member created yet</h3>
                    </div>
                </div>
            </li>
            @endforelse
            
        </ul>
    </div>
</div>
@push('js')
<script>
    $.extend($.expr[':'], { //definizione di :conaints() case insesitive
        'containsi': function(elem, i, match, array)
        {
            return (elem.textContent || elem.innerText || '').toLowerCase()
                    .indexOf((match[3] || "").toLowerCase()) >= 0;
        }
    });
    $(document).ready(function(){
        $(document).on('keyup','#search-customer',function(){
            let text = $(this).val();
            if(!text){
                $(".user-list li").show();
            }else{
                $(".user-list li").hide();
                $(".user-list li:containsi("+text+")").show();
            }
        });
    })
</script>
@endpush