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
    <div class="chat-section">
        @include('admin.chat.customers')
        <div class="chat-panel">
            <div class="chat-panel-header">
                <div class="chat-panel-avatar">
                    <img src="{{ asset('admin/images/avatar-fch_9.png') }}" class="rounded-circle" alt="image" />
                </div>
                <div class="chat-panel-content" data-url="{{ route('admin.customer.ajax.chat.list',$user) }}" id="ajax-chat-url" data-id="{{$user->id}}">
                    <h3>{{$user->name}}</h3>
                    {{-- <p>User</p> --}}
                </div>
            </div>
            <div class="chat-panel-body ">
                <div class="messages-card">
                	@include('admin.chat.message-box')
                </div>
            </div>
            <div class="chat-panel-footer">
            	<div class="chat-action-item">
	                <div class="chat-form-group">
	                    <input type="text" name="message" placeholder="Message" class="form-control message" id="message-input" />
	                    <div class="upload-media">
                            <input type="file" id="upload-file">
                            <label for="upload-file">
                                <div class="upload-media">
                                    <i class="fa fa-camera" aria-hidden="true"></i>
                                </div> 
                            </label>
                        </div>
	                    <button type="button" class="btnSend" data-url="{{ route('admin.customer.chat.post',$user) }}">Send</button>
	                </div>
	            </div>
            </div>
        </div>
    </div>
</div>
<div class="modal zoom-modal NUE-modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-body">
                <div class="image-zoom">
                  <div class="zoom--actions">
                    <a href="#" class="zoom-in"><i class="las la-plus-circle"></i> Zoom In</a>
                    <a href="#" class="zoom-out"><i class="las la-minus-circle"></i> Zoom Out</a>
                  </div>
                  <div class="zoom-modal-media">
	                  <div class="zoom--img">
	                    <img src="">
	                  </div>
	              </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('css')
<link rel="stylesheet" href="{{ asset('admin/css/chat.css') }}">
{{-- <link rel="stylesheet" href="{{ asset('plugins/fancybox/css/jquery.fancybox.min.css') }}"> --}}
@endpush
@push('js')
<script src="{{ asset('plugins/js/moment.min.js') }}"></script>
{{-- <script src="{{ asset('plugins/fancybox/js/jquery.fancybox.min.js') }}"></script> --}}
<script type="module">

  // Import the functions you need from the SDKs you need
  import { getAuth, signInAnonymously } from "https://www.gstatic.com/firebasejs/9.1.3/firebase-auth.js"
  import { initializeApp } from "https://www.gstatic.com/firebasejs/9.1.3/firebase-app.js";

  import { getFirestore, collection, getDocs, addDoc, orderBy,query} from "https://www.gstatic.com/firebasejs/9.1.3/firebase-firestore.js";

  // TODO: Add SDKs for Firebase products that you want to use

  // https://firebase.google.com/docs/web/setup#available-libraries


  // Your web app's Firebase configuration

  // For Firebase JS SDK v7.20.0 and later, measurementId is optional

  const firebaseConfig = {
    apiKey: "AIzaSyADZnINeiDcYx8Ora6LpXeVsogrB-t3pCo",
    authDomain: "brandnue-bcfc6.firebaseapp.com",
    databaseURL: "https://brandnue-bcfc6-default-rtdb.firebaseio.com",
    projectId: "brandnue-bcfc6",
    storageBucket: "brandnue-bcfc6.appspot.com",
    messagingSenderId: "824207574439",
    appId: "1:824207574439:web:8248d972c280cea0886282",
    measurementId: "G-D5MHKWHSEQ"
  };


  // Initialize Firebase
  const receiver_id = $("#ajax-chat-url").data('id');
  const group_id = "1"+receiver_id;
  const app = initializeApp(firebaseConfig);

    //let defaultStorage = getStorage(app);
    let defaultFirestore = getFirestore(app);
    const auth = getAuth(app);
    signInAnonymously(auth)
      .then((result) => {
      	console.log('result here');
        console.log(result);
      })
      .catch((error) => {
      	console.log('error',error);
        const errorCode = error.code;
        const errorMessage = error.message;
        // ...
      });
    
    window.getClientChat = async function(group_id,ajax_call=false) {
      const chatCol = query(collection(defaultFirestore, 'Chat/'+group_id+'/Messages'),orderBy('createdAt','asc'));
      const chatSnapshot = await getDocs(chatCol);
      const chatList = chatSnapshot.docs.map(doc => doc.data());
      
    	showAllMessages(chatList,ajax_call);
      //return chatList;
    }
    getClientChat(group_id);
    
	window.sendNewMessage = async function(group_id,message,image=''){
		const chatCol = collection(defaultFirestore, 'Chat/'+group_id+'/Messages');
		let data = {userId:1,message:message,createdAt: new Date()};
		if(image){
			data = {...data,image:image};
		}
		const add = await addDoc(chatCol,data);
		return true;
	}
</script>
<script>
	const receiver_id = $("#ajax-chat-url").data('id');
  	const group_id = "1"+receiver_id;
	$(document).ready(function(){
		//Aug 10, 2021 10:02 AM
		$(document).on('keypress','#message-input',function(e){
			if(e.which == 13) {
				let button = $('.btnSend');
				let url = button.data('url');
				let message = $('#message-input');
				let text = message.val().trim();
		        if(!text){
		        	message.focus();
		        	return false;
		        }else{
		        	sendMessage(button,url,message);
		        }
		    }
		});

		$(document).on('click','.btnSend',function(){
			let message = $('#message-input');
			let self = $(this);
			const url = $(this).data('url');
			let image = $('#upload-file').val();
			let text = message.val().trim();
			if(!text){
				text = image;
			}

			if(!text){
				message.focus();
				return false;
			}else{
				sendMessage(self,url,message);
			}
			
		})
	});

	function sendMessage(self,url,message){
		const check = $.type(self);
		check=='object'? self.prop('disabled',true) :'';
		//self.prop('disabled',true);
		let time = moment().format('MMM DD, YYYY HH:mm A');
		let image ='';
		if($('#upload-file').val()){
			image = URL.createObjectURL($('#upload-file')[0].files[0]);
		}
		showMessage(message.val(),time,image);
		let formData = new FormData();
        formData.append('message',message.val());
        formData.append('time',time);
        formData.append('image',$('#upload-file')[0].files[0] );
        if(!image){
        	let resp = sendNewMessage(group_id,message.val());
			// alert('group_id');
        	if(resp){
        		message.val('').focus();
        		check=='object'?self.prop('disabled',false):'';
        	}
        }

        if(url){
			$.ajax({
				type:'post',
				url : url,
				data:formData,
				headers: {'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')},
				contentType: false,
	            cache: false,
	            processData:false,
				success : function(res){
					if(res.status==false){
						alert('res.msg');
						return false;
					}
					if(res.url){
						sendNewMessage(group_id,message.val(),res.url);
						message.val('').focus();
						$('#upload-file').val('');
						check=='object'?self.prop('disabled',false):'';
					}
					/*else{
						sendNewMessage(group_id,message.val());
					}*/
					
				}
			});
        }
		
	}

	function showMessage(message,time,image){
		let msg = `
					<div class="message-item outgoing-message">
						<div class="message-item-card">
						    <div class="message-options">
						        <div class="avatar"><img alt="" src="${base_url}/public/admin/images/avatar-fch_9.png" /></div>
						    </div>
						    <div class="message-wrapper">
						        <div class="message-content">
						        	${image?`<div class="message-images"><a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#exampleModal" data-image="${image}"><img alt="" src="${image}" /></a></div>`:''}
						            
						            <p>${message}</p>
						        </div>
						        <div class="time-text">${time}</div>
						    </div>
						</div>
					</div>
				`;
		$('.messages-card').append(msg);
		
		$(".chat-panel-body").stop().animate({ scrollTop: $(".chat-panel-body")[0].scrollHeight}, 1000);
	}

	function getChatDetails(){
		const url = $('#ajax-chat-url').data('url');
		$.ajax({
			url : url,
			//headers: {'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')},
			dataType: 'json',
			success : function(res){
				if(res.status==true){
					$('.messages-card').html(res.html);
				}else{
					alert(res.msg);
				}
			}
		});
	}

	function showAllMessages(list,ajax_call=false){
		if(list.length==0) return false;
		let html = `${list.map(row => admin(row,ajax_call)).join('')}`;
		$('.messages-card').html(html);
		if(ajax_call==false){
			$(".chat-panel-body").stop().animate({ scrollTop: $(".chat-panel-body")[0].scrollHeight}, 1000);
		}
	}

	function admin(row,ajax_call){
		let html = '';
		if(row.userId!=1){
			html = `
				<div class="message-item">
				    <div class="message-item-card">
				        <div class="message-options">
				            <div class="avatar"><img alt="" src="${base_url}/public/admin/images/avatar-fch_9.png" /></div>
				        </div>
				        <div class="message-wrapper">
				            <div class="message-content">
				            	${ row.image !== undefined ? `<div class="message-images"><a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#exampleModal" data-image="${base_url}/${row.image}"><img alt="" src="${base_url}/${row.image}" /></a></div>`:''}
				                
				                <p>${row.message}</p>
				            </div>
				            <div class="time-text">${moment(moment.unix(row.createdAt.seconds).toDate()).format('MMM DD, YYYY HH:mm A')}</div>
				        </div>
				    </div>
				</div>
			`;
		}else{
			html = `
				<div class="message-item outgoing-message">
				    <div class="message-item-card">
				        <div class="message-options">
				            <div class="avatar"><img alt="" src="${base_url}/public/admin/images/avatar-fch_9.png" /></div>
				        </div>
				        <div class="message-wrapper">
				            <div class="message-content">
				                ${ row.image !== undefined ? `<div class="message-images"><a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#exampleModal" data-image="${base_url}/${row.image}"><img alt="" src="${base_url}/${row.image}" /></a></div>`:''}
				                <p>${row.message}</p>
				            </div>
				            <div class="time-text">${moment(moment.unix(row.createdAt.seconds).toDate()).format('MMM DD, YYYY HH:mm A')}</div>
				        </div>
				    </div>
				</div>
			`;
		}
		return html;
	}


	setInterval(function() {
	    getClientChat(group_id,true);
	}, 5000);


	$(document).on('show.bs.modal',function(e){
	    let a = e.relatedTarget;
	    $('.zoom--img img').attr('src',a.getAttribute('data-image')).removeAttr('style');
	})
	$('.zoom--actions .zoom-in').on('click', function () {
	        var img = $(this).parents('.image-zoom').find('.zoom--img img');
	        var width = img.width();
	        var newWidth = width + 100;
	        img.width(newWidth);
	    }
	);
	$('.zoom--actions .zoom-out').on('click', function () {
	        var img = $(this).parents('.image-zoom').find('.zoom--img img');
	        var width = img.width();
	        var newWidth = width - 100;
	        if(newWidth>50)
	        img.width(newWidth);
	    }
	);
</script>
@endpush