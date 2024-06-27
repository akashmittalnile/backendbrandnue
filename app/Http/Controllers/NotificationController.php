<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PushNotification;
use Illuminate\Support\Str;
use Validator;
use App\User;

class NotificationController extends Controller
{
    public $notify;
    public $user;

    public function __construct(PushNotification $notify,User $user){
        $this->middleware('auth');
        $this->notify = $notify;
        $this->user = $user;
    }

    public function index(){
        $notifications = $this->notify->orderBy('id','desc')->paginate(config('constant.adminPerPage'));
        return view('admin.notifications.list',compact('notifications'));
    }

    public function create(){
        $plans = \App\Models\SubscriptionPlan::where('status',config('constant.status.active'))->get();
        return view('admin.notifications.create',compact('plans'));
    }

    public function store(Request $request){
        $this->validate($request,[
            'title'=>'required|max:191',
            'description'=>'required|max:2000',
            'plan'=>'required'
        ]);


        $notify = new $this->notify;
        $notify->title = $request->title;
        $notify->subscription_plan_id = $request->plan;
        $notify->data = $request->description;
        $notify->status = ($request->status==config('constant.status.active'))?config('constant.status.active'):config('constant.status.in_active');
        if($request->hasFile('image')){
            $path = 'uploads/notification';
            $notify->image = uploadImage($request,'image',$path);
        }
        $notify->save();

        $user_ids = \App\Models\SubscribeMember::where(['subscription_plan_id'=>$request->plan,'status'=>'Active'])->pluck('user_id')->toArray();
        
        $users = $this->user->getCustomer($request,config('constant.status.active'))->whereIn('id',$user_ids)->get();
        
        if($users->count() && $notify->status == config('constant.status.active')){
            foreach($users as $row){
                $share = new \App\Models\Share;
                $share->user_id = $row->id;     
                $notify->shares()->save($share);

                if(!empty($row->fcm_token)){
                    $data = [
                        'to'=>$row->fcm_token,
                        'notification'=>[
                            'title'=>"New message",
                            'body'=>$request->title,
                            'mutable_content'=>false,
                            'sound'=>'Tri-tone'
                        ],                    
                        
                    ];
                    $res_notify = pushNotification($data);
                }
            }
        }

        return redirect(route('admin.notification.list'))->with('success','Notification created successfully');
    }

    public function edit(PushNotification $data){
        if($data->status==config('constant.status.active')){
            \Session::flash('error','<b>'.$data->title.'</b>  notification already published, So you can not edit.');
            return redirect(route('admin.notification.list'));
        }
        $plans = \App\Models\SubscriptionPlan::where('status',config('constant.status.active'))->get();
        return view('admin.notifications.edit',compact('data','plans'));
    }

    public function update(Request $request,PushNotification $data){  

        $data->title = $request->title;
        $data->data = $request->description;
        $data->subscription_plan_id = $request->plan;
        $data->status = ($request->status==config('constant.status.active'))?config('constant.status.active'):config('constant.status.in_active');
        if($request->hasFile('image')){
            $path = 'uploads/notification';
            $data->image = uploadImage($request,'image',$path);
        }
        $data->save();
        $user_ids = \App\Models\SubscribeMember::where(['subscription_plan_id'=>$request->plan,'status'=>'Active'])->pluck('user_id')->toArray();
        
        $users = $this->user->getCustomer($request,config('constant.status.active'))->whereIn('id',$user_ids)->get();
        
        if($users->count() && $data->status == config('constant.status.active')){
            foreach($users as $row){
                $share = new \App\Models\Share;
                $share->user_id = $row->id;     
                $data->shares()->save($share);

                if(!empty($row->fcm_token)){
                    $fcm = [
                        'to'=>$row->fcm_token,
                        'notification'=>[
                            'title'=>"New message",
                            'body'=>$request->title,
                            'mutable_content'=>false,
                            'sound'=>'Tri-tone'
                        ],                    
                        
                    ];
                    $res_notify = pushNotification($fcm);
                }
            }
        }
        return redirect(route('admin.notification.list'))->with('success','Notification updated successfully');
    }

    public function show(PushNotification $data){
        return view('admin.notifications.show',compact('data'));
    }

    public function delete(PushNotification $data){
        $data->delete();
        \App\Models\Share::where(['shareable_id'=>$data->id,'shareable_type'=>'App\Models\PushNotification'])->delete();
        \Session::flash('success', 'Notification Deleted successfully'); 
        return successMsgResponse('Deleted successfully');
    }

}
