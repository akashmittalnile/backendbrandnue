<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Database;
use App\Models\SubscriptionPlan;
use App\Models\SubscribeMember;
use App\Library\GetFunction;
use Illuminate\Support\Arr;
use App\Models\Tracking;
use App\Models\Role;
use App\Models\Note;
use App\User;
use Validator;
use Auth;
use DB;
use Config;

use Excel;
use App\Exports\CustomerExport;

class UserController extends Controller
{
    public $user, $plan;
    public function __construct(User $user, SubscriptionPlan $plan,GetFunction $getFunction){
        $this->middleware('auth');
        $this->getFunction  = $getFunction;
        $this->user = $user;
        $this->plan = $plan;
    }  

    public function userList(Request $request){
        $users = $this->user->getAdminUserList($request);
        
        if($request->filled('export_to')){
            $final = [];
            foreach ($users as $key => $user) {
                $final[] = [
                    $user->name,
                    $user->phone,
                    $user->email,
                    $user->device,
                    $user->plan_name ?? '',                    
                    date('M d, Y',strtotime($user->updated_at))
                ];
            }
            
            return Excel::download(new CustomerExport($final), "Customer-" . time() . '.xls');
        }

        $plans = $this->plan->getPlan();
        // $total_users = $this->user->getCustomer($request)->count();
        $total_users = $this->user->getAllCustomer($request)->count();
        $non_member_users = $this->user->getNonMemberCustomer($request)->count();
        $total_plans = $this->plan->where('status',config('constant.status.active'))->with('totalMemberUser')->get();
        
        return view('admin.user.list',compact('users','plans','total_users','total_plans', 'non_member_users'));
    }

    public function userCreate(){
        $plans = $this->plan->getPlan();
        return view('admin.user.create',compact('plans'));
    }

    public function userStore(Request $request){
        $rules = [
            'first_name'=>'required|max:191',
            'last_name'=>'required|max:191',
            'email'=>'required|max:191|unique:users',
            'phone'=>'required',
            'gender'=>'required|max:10',
            'dob'=>'required',
            'height_feet'=>'required',
            'waist_measurement'=>'required',
            'current_weight'=>'required',
            'goal_weight'=>'required',
        ];
        if($request->plan==config('constant.elite_member_id')){
            $rules['start_date'] = 'required';
            $rules['end_date'] = 'required';
        }
        $this->validate($request,$rules);

        
        DB::beginTransaction();
        try {
            $user = new $this->user;
            $user->name = $request->first_name.' '.$request->last_name;
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->password = bcrypt('user@brandnue');
            $user->phone = $request->phone;
            $user->gender = $request->gender;
            $user->dob = date('Y-m-d',strtotime($request->dob)); //dbDateFormat($request->dob);
            $user->height_feet = $request->height_feet;
            $user->height_inch = $request->height_inch;
            $user->waist_measurement = $request->waist_measurement;
            $user->today_waist_measurement = $request->waist_measurement;
            $user->goal_waist_measurement = $request->waist_measurement;
            $user->current_weight = $request->current_weight;
            $user->goal_weight = $request->goal_weight;
            $user->status = $request->status;
            if($request->hasFile('profile_image')){
                $path = 'uploads/profile';
                $user->profile_image = uploadImage($request,'profile_image',$path);
            }
            $user->save();
            $customerRole = Role::where('name', 'CUSTOMER')->first();
            $user->roles()->attach($customerRole);
            if(!empty($request->plan)){
                $start_date = date('Y-m-d');
                if ($request->plan == '2') {
                    $end_date = date('Y-m-d',strtotime('+ 1 month'));
                } else {
                    $end_date = date('Y-m-d',strtotime('+ 1 years'));
                }
                
                if($request->plan==config('constant.elite_member_id')){
                    $start_date = $request->start_date;
                    $end_date = $request->end_date;
                }

                SubscribeMember::insert(['user_id'=>$user->id,'subscription_plan_id'=>$request->plan,'activated_date'=>$start_date,'renewal_date'=>$end_date,'activated_from'=>'Admin','status'=>'Active','created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')]);
            }
            DB::commit();
            \Session::flash('success','Account created successfully.');
            return redirect(route('admin.user.list'));
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error',$e->getMessage());
        }
    }
    
    public function editUser(Request $request,User $user){
        $user->load('plan');
        $plans = $this->plan->getPlan();
        return view('admin.user.edit',compact('user', 'plans'));
    }

    public function updateUser(Request $request,User $user){
        $rules = [
            'first_name'=>'required|max:191',
            'last_name'=>'required|max:191',
            'email'=>'required|max:191|unique:users,email, '.$user->id,
            'phone'=>'required',
            'gender'=>'required|max:10',
            'dob'=>'required',
            'height_feet'=>'required',
            'waist_measurement'=>'required',
            'current_weight'=>'required',
            'goal_weight'=>'required',
        ];
        if(in_array($request->plan,[config('constant.elite_member_id'),config('constant.yearly_elite_member_id')])){
            $rules['start_date'] = 'required';
            $rules['end_date'] = 'required';
        }
        $this->validate($request,$rules);

        
        DB::beginTransaction();
        try {            
            $user->name = $request->first_name.' '.$request->last_name;
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->gender = $request->gender;
            $user->dob = date('Y-m-d',strtotime($request->dob)); //dbDateFormat($request->dob);
            $user->height_feet = $request->height_feet;
            $user->height_inch = $request->height_inch;
            $user->waist_measurement = $request->waist_measurement;
            $user->current_weight = $request->current_weight;
            $user->goal_weight = $request->goal_weight;
            
            $user->save();
            
            if(!empty($request->plan) && in_array($request->plan,[config('constant.elite_member_id'),config('constant.yearly_elite_member_id')])){
                $start_date = $request->start_date;
                $end_date = $request->end_date;
                SubscribeMember::where(['user_id'=>$user->id,'status'=>'Active','activated_from'=>'Admin'])->update(['activated_date'=>$start_date,'renewal_date'=>$end_date, 'subscription_plan_id'=>$request->plan]);
            }
            
            DB::commit();
            \Session::flash('success','User account updated successfully.');
            return redirect(route('admin.user.list'));
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error',$e->getMessage());
        }
    }

    public function userShow(User $user){
        return view('admin.user.show',compact('user'));
    }

    public function getChatUserList(Request $request){
        //$customers = $this->user->getCustomer($request,config('constant.status.active'))->with(['chatCount:user_id,admin_id,total'])->get();
        $customers = $this->user->getChatCountUserList($request);
        //dd($customers);
        return view('admin.chat.list',compact('customers'));
    }

    public function getChatUserDetail(Request $request,User $user){
        // $database = app('firebase.database');
        $admin = Auth::user();
        /*$admin_user = $admin->id.$user->id;
        $ref = env('FIREBASE_DATABASE_NAME')."/".$admin_user;*/
        
        //$chats = $database->getReference($ref)->orderByKey()->getValue();
        $chats = '';
        \App\Models\ChatCount::where(['user_id'=>$user->id,'admin_id'=>$admin->id])->update(['admin_total'=>0]);
        $customers = $this->user->getChatCountUserList($request);
        return view('admin.chat.chat-detail',compact('customers','chats','user'));
    }

    public function postChat(Request $request,User $user){
        $message = $request->message;
        try {
            $url = NULL;
            if($request->hasFile('image')){
                $path = 'uploads/chats';
                $url = uploadImage($request,'image',$path);
                $postData['image'] = $url;
                if(empty($message)){
                    $message = 'You have a new image';
                }
            }
            

            $chat_count = \App\Models\ChatCount::where(['user_id'=>$user->id,'admin_id'=>1])->first();
            if(!$chat_count){
                $chat_count = new \App\Models\ChatCount;
                $chat_count->admin_total = 0;
                $chat_count->total = 1;
                $chat_count->admin_id = 1;
                $chat_count->user_id = $user->id;
            }else{
                $chat_count->admin_total = 0;
                $chat_count->total = $chat_count->total+1;

            }
            $chat_count->save();

            $total_count = \App\Models\ChatCount::where(['user_id'=>$user->id,'admin_id'=>1])->sum('total');
            $pushNTF = '';
            if(!empty($user->fcm_token) && !empty($message)){
                $data = [
                    'to'=>$user->fcm_token,
                    'notification'=>[
                        'title'=>"New message",
                        'body'=>$message,
                        'mutable_content'=>false,
                        'sound'=>'Tri-tone',
                        'badge'=> $total_count
                    ],                    
                    
                ];
                $res_notify = pushNotification($data);
                $pushNTF = $res_notify['data'];
            }

            return response()->json(['status'=>true,'msg'=>'Success','ntf'=>$pushNTF,'url'=>$url]);    
        } catch (\Exception $e) {
            return errorMsgResponse($e->getMessage());
        }
    }

    public function ajaxChatList(User $user){
        $admin = Auth::user();
        $admin_user = $admin->id.$user->id;
        try {
            $database = app('firebase.database');
            $ref = env('FIREBASE_DATABASE_NAME')."/".$admin_user;
            //$ref = env('FIREBASE_DATABASE_NAME')."/15";
            $chats = $database->getReference($ref)->orderByKey()->getValue();
            // dd($chats);
            $html = view('admin.chat.message-box',compact('chats'))->render();
            return response()->json(['status'=>true,'html'=>$html]);
        } catch (\Exception $e) {
            return errorMsgResponse($e->getMessage());
        }

    }

    /*Admin panel start*/
    public function adminList(Request $request){
        $users = $this->user->whereHas('roles',function($q){
            $q->where('name',config('constant.role.admin'));
        })->paginate(config('constant.adminPerPage'));
        
        return view('admin.admin.list',compact('users'));
    }

    public function adminCreate(){
        return view('admin.admin.create');
    }

    public function adminStore(Request $request){
        $this->validate($request,[
            'first_name'=>'required|max:191',
            'last_name'=>'required|max:191',
            'email'=>'required|max:191|unique:users',
            'phone'=>'required',
            'gender'=>'required|max:10',
            'dob'=>'required',
            'status'=>'required'
        ]);

        
        DB::beginTransaction();
        try {
            $user = new $this->user;
            $user->name = $request->first_name.' '.$request->last_name;
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->phone = $request->phone;
            $user->gender = $request->gender;
            $user->dob = date('Y-m-d',strtotime($request->dob)); //dbDateFormat($request->dob);
            $user->status = $request->status;
            if($request->hasFile('profile_image')){
                $path = 'uploads/profile';
                $user->profile_image = uploadImage($request,'profile_image',$path);
            }
            $user->save();
            $adminRole = Role::where('name', 'ADMIN')->first();
            $user->roles()->attach($adminRole);
            
            // $subscribemember = new SubscribeMember();
            // $subscribemember->user_id = $user->id;
            // $subscribemember->subscription_plan_id = '4';
            // $subscribemember->activated_date = '2024-03-11';
            // $subscribemember->renewal_date = '2030-03-11';
            // $subscribemember->status = 'Active';
            // $subscribemember->activated_from = 'Online';
            // $subscribemember->device = 'Android';
            // $subscribemember->created_at = date('Y-m-d H:i:s');
            // $subscribemember->save();

            DB::commit();
            \Session::flash('success','Account created successfully.');
            return redirect(route('admin.admin.list'));
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error',$e->getMessage());
        }
    }

    public function adminEdit(User $user){
        $plans = $this->plan->getPlan();
        return view('admin.admin.edit',compact('user'));
    }

    public function adminUpdate(Request $request,User $user){
        $rules = [
            'first_name'=>'required|max:191',
            'last_name'=>'required|max:191',
            'email'=>'required|max:191|unique:users,email, '.$user->id,
            'phone'=>'required',
            'gender'=>'required|max:10',
            'dob'=>'required',
            'status'=>'required'
        ];

        if(!empty($request->password) || !empty($request->password_confirmation)){
            $rules['password'] = 'required';
            $rules['password_confirmation'] = 'required|same:password';
        }

        $this->validate($request,$rules,['password.required'=>'The new password field is required.','password_confirmation.required'=>'The confirmation password field is required.','password_confirmation.same'=>'The confirmation password must match to new password.']);

        
        DB::beginTransaction();
        try {
            
            $user->name = $request->first_name.' '.$request->last_name;
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->gender = $request->gender;
            $user->dob = date('Y-m-d',strtotime($request->dob)); //dbDateFormat($request->dob);
            $user->status = $request->status;
            if(!empty($request->password)){
                $user->password = bcrypt($request->password);
            }
            if($request->hasFile('profile_image')){
                $path = 'uploads/profile';
                $user->profile_image = uploadImage($request,'profile_image',$path);
            }
            $user->save();
            
            DB::commit();
            \Session::flash('success','Account updated successfully.');
            return redirect(route('admin.admin.list'));
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error',$e->getMessage());
        }
    }

    public function adminShow(User $user){
        return view('admin.admin.show',compact('user'));
    }

    public function adminDelete(User $user){
        if($user->id==1){
            return errorMsgResponse('You can not delete the primary admin!');
        }
        $image = $user->profile_image;
        $user->delete();
        if(!empty($image)){
            deleteImage($image);
        }
        \Session::flash('success','Admin deleted successfully.');
        return successMsgResponse('Admin deleted successfully.');
    }
    /*Admin panel end*/


    public function userDailyTracking(Request $request,User $user){
        $data = $this->getFunction->getUserTrackingForAdmin($user->id);
        $tracking = new Tracking;
        $trackings = $tracking->getUserTrackingById($request,$user->id);
        $user->load('notes');
        $data['user'] = $user;
        $data['trackings'] = $trackings;
        $data['plan'] = $this->user->getActivePlan($user->id);
        return view('admin.user.daily-tracking',$data);
    }

    public function userDailyTrackingNote(Request $request,User $user){
        $note = new Note;
        $notes = $note->getNoteList($user->id);
        return view('admin.user.tracking-note',compact('notes','user'));
    }

    public function userDailyTrackingNoteStore(Request $request,User $user){
        $this->validate($request,[
            'color'=>'required',
            'note'=>'required',
            'date'=>'required'
        ]);
        $note = new Note;
        $note->name = $request->color;
        $note->description = $request->note;
        $note->note_date = $request->date;
        $user->notes()->save($note);
        return back()->with('success','Profile note has been added.');
    }

    public function userDailyTrackingNoteDelete(User $user,Note $note){
        $note->delete();
        \Session::flash('success','Profile note has been deleted.');
        return successMsgResponse('Profile note has been deleted.');
    } 

    public function passwordReset(User $user){
        $user->password = bcrypt(config('constant.defaultPassword'));
        $user->save();
        \Session::flash('success','<b>'.$user->name.'</b> password has been reset to default password <b>'.config('constant.defaultPassword').'</b>');
        return successMsgResponse('Successful');
    }

    public function deleteUser(User $user){
        DB::beginTransaction();
        try {
            $active_plan = \App\Models\SubscribeMember::where(['user_id'=>$user->id,'status'=>'Active'])->first();
            // $active_plan = \App\Models\SubscribeMember::where(['user_id'=>$user->id,'status'=>'Active','activated_from'=>'Online'])->first();
            if($active_plan && $active_plan->subscription_plan_id!=config('constant.standard_member_id')){
                return errorMsgResponse('User has a active subscription plan, Please cancel the subscription first.');
            }

            \App\Models\ChatCount::where('admin_id',$user->id)->orWhere('user_id',$user->id)->delete();
            \App\Models\EliteMembershipRequest::where('user_id',$user->id)->delete();
            \App\Models\FavouriteRecipe::where('user_id',$user->id)->delete();
            \App\Models\Note::where(['noteable_id'=>$user->id,'noteable_type'=>"App\User"])->delete();
            \App\Models\Share::where(['user_id'=>$user->id])->delete();
            \App\Models\SubscribeMember::where(['user_id'=>$user->id])->delete();
            \App\Models\Tracking::where(['user_id'=>$user->id])->delete();
            \App\Models\UserInfo::where(['user_id'=>$user->id])->delete();
            $user->delete();
            DB::commit();
            \Session::flash('success','User has been deleted successfully');
            return successMsgResponse('User has been deleted successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return errorMsgResponse($e->getMessage());
        }
    }

    public function changeUserStatus(User $user, $status){
        DB::beginTransaction();
        try {
            $active_plan = \App\Models\SubscribeMember::where(['user_id'=>$user->id,'status'=>'Active','activated_from'=>'Online'])->first();
            if($active_plan && $active_plan->subscription_plan_id!=config('constant.standard_member_id')){
                return errorMsgResponse('User has a active subscription plan, Please cancel the subscription first.');
            }

            $user->status = $status;
            $user->save();
            DB::commit();
            \Session::flash('success','User status changed successfully');
            return successMsgResponse('User status changed successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return errorMsgResponse($e->getMessage());
        }
    }

    public function cancelUserSubscription(User $user,$subscription_id=''){
        $subscription_id = trim($subscription_id);
        
        if(empty($subscription_id)){
            $active = \App\Models\SubscribeMember::where(['user_id'=>$user->id,'status'=>'Active'])->first();
            if($active && $active->subscription_plan_id==1) {
                return errorMsgResponse('You have a fee subscription. This subscription can not be cancelled.');
            }
            if($active && empty($active->square_payment_subscription_id)){
                $active->status = 'Cancelled';
                $active->modify_date = date('Y-m-d');
                $active->save();
                /*$free = \App\Models\SubscribeMember::where(['user_id'=>$user->id,'subscription_plan_id'=>1])->first();
                if(!$free){
                    $free = new \App\Models\SubscribeMember;
                    $free->user_id = $user->id;
                    $free->subscription_plan_id = 1;
                    $free->activated_from = 'Admin';
                    $free->activated_date = date('Y-m-d');
                    $free->renewal_date = date('Y-m-d',strtotime('+1 years'));
                }
                $free->status = 'Active';
                $free->save();*/
                \Session::flash('success','Your subscription has been cancelled successfully');
                return response()->json(['status'=>true,'msg'=>'Your subscription has been cancelled successfully.']);
            }
            \Session::flash('error','You have not any active subscription');
            return errorMsgResponse('You have not any active subscription.');
        }
        $active = \App\Models\SubscribeMember::where(['user_id'=>$user->id,'square_payment_subscription_id'=>$subscription_id])->first();
        if(!$active){
            \Session::flash('error','You have not any active subscription');
            return errorMsgResponse('You have not any active subscription.');
        }
        $obj = new \App\Library\PostFunction;
        $result = $obj->cancelSubscription($subscription_id);
        if($result['status']==true){
            $active->status = 'Cancelled';
            $active->modify_date = date('Y-m-d');
            $active->save();
            /*$free = \App\Models\SubscribeMember::where(['user_id'=>$user->id,'subscription_plan_id'=>1])->first();
            if(!$free){
                $free = new \App\Models\SubscribeMember;
                $free->user_id = $user->id;
                $free->subscription_plan_id = 1;
                $free->activated_from = 'Online';
                $free->activated_date = date('Y-m-d');
                $free->renewal_date = date('Y-m-d',strtotime('+1 years'));
            }
            $free->status = 'Active';
            $free->save();*/
            \Session::flash('success','Your subscription has been cancelled successfully');
            return response()->json(['status'=>true,'msg'=>'Your subscription has been cancelled successfully.']);
            
        }
        return errorMsgResponse($result['msg']);
    }
}
