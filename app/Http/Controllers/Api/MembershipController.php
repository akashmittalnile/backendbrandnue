<?php

namespace App\Http\Controllers\Api;

use App\Models\EliteMembershipRequest;
use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\SubscribeMember;
use App\Library\PostFunction;
use Illuminate\Http\Request;
use Validator;
use Auth;

class MembershipController extends Controller
{
    public $plan;
    public $member;

    public function __construct(SubscribeMember $member,SubscriptionPlan $plan){
        $this->member = $member;
        $this->plan = $plan;
        $this->postFunction = new PostFunction();
    }

    public function subscribeMembership(Request $request,SubscriptionPlan $plan){
        $user = Auth::user();
        if(empty($plan->price)){
            return $this->_freeMembership($plan,$user);
        }
        $validation = Validator::make($request->all(),[
            'payment_token'=>'required'
        ]);
        if($validation->fails()){
            return errorMsgResponse($validation->errors()->first());
        }
        return $this->_membership($plan,$user,$request->payment_token);
    }

    private function _freeMembership($plan,$user){
        $current_membership = $this->member->where(['user_id'=>$user->id,'status'=>'Active'])->first();
        if($current_membership && $current_membership->subscription_plan_id!=1){
            return errorMsgResponse('Please cancel your previous subscription, Then you can upgrade the membership');
        }else if($current_membership && $current_membership->subscription_plan_id==1){
            $member = $current_membership;
        }else{
            $member = new $this->member;
        }
        
        $member->user_id = $user->id;
        $member->subscription_plan_id = $plan->id;
        $member->activated_date = date('Y-m-d');
        $member->renewal_date = date('Y-m-d',strtotime('+ 10 years'));
        $member->status = 'Active';
        $member->activated_from = 'Online';
        $member->save();
        $user->elite_member_request = $this->member->where(['user_id'=>$user->id,'activated_from'=>'Admin','subscription_plan_id'=>config('constant.elite_member_id')])->count();
        $user->plan = [
                'id'=>$plan->id,
                'name'=>$plan->name,
                'price'=>"Free",
                'square_payment_subscription_id'=>'',
                'elite_member_id' => config('constant.elite_member_id'),
                'device'=> 'Android',
            ];
        return response()->json(['status'=>true,'msg'=>'Membership activated successfully','data'=>$user]);
    }

    private function _membership($plan,$user,$payment_token){
        $current_membership = $this->member->where(['user_id'=>$user->id,'status'=>'Active'])->first();
        if($current_membership && $current_membership->subscription_plan_id!=1){
            return errorMsgResponse('Please cancel your previous subscription, Then you can upgrade the membership');
        }

        $membership = $this->member->where(['user_id'=>$user->id,'status'=>'Active', 'subscription_plan_id'=>$plan->id])->whereNotNull('square_payment_subscription_id')->first();
        if(!empty($membership)){
            return errorMsgResponse('Please cancel your previous subscription, Then you can upgrade the membership');
        }

        $member = new $this->member;
        $member->user_id = $user->id;
        $member->subscription_plan_id = $plan->id;
        $member->activated_date = date('Y-m-d');
        $member->renewal_date = ($plan->subscription_interval==12) ? date('Y-m-d',strtotime('+ 1 years')) : date('Y-m-d',strtotime('+ 1 month'));
        $member->status = 'Active';
        $member->activated_from = 'Online';
        $member->save();
        $user->elite_member_request = $this->member->where(['user_id'=>$user->id,'activated_from'=>'Admin','subscription_plan_id'=>config('constant.elite_member_id')])->count();
        $user->plan = [
            'id'=>$plan->id,
            'name'=>$plan->name,
            'price'=>$plan->price,
            'square_payment_subscription_id'=>'',
            'elite_member_id' => config('constant.elite_member_id'),
            'device'=> 'Android',
        ];
        $square_user = $this->postFunction->searchSquarePaymentCustomerByEmail($user);
        if($square_user['status']==false){
            $square_user = $this->postFunction->createSquareCustomer($user);
        }
        
        if($square_user['status']==true){
            $square_user_card = $this->postFunction->createSquareCustomerCard($user,$square_user['square_payment_user_id'],$payment_token);
            if($square_user_card['status']==true){
                $subscribe = $this->postFunction->createSquareCustomerSubscription($user,$square_user['square_payment_user_id'],$plan->square_payment_plan_id,$square_user_card['card_id']);
                if($subscribe['status']==true){
                    $member->status = ucfirst(strtolower($subscribe['subscription_status']));
                    $member->square_payment_subscription_id = $subscribe['subscription_id'];
                    $member->save();
                    $user->plan = [
                        'id'=>$plan->id,
                        'name'=>$plan->name,
                        'price'=>$plan->price,
                        'square_payment_subscription_id'=>$subscribe['subscription_id'],
                        'elite_member_id' => config('constant.elite_member_id'),
                        'device'=> 'Android',
                    ];
                    
                    if($member->id && !empty($current_membership)){
                        $current_membership->status = 'Upgraded';
                        $current_membership->save();
                    }

                    $transaction = new \App\Models\SubscribeMemberTransaction;
                    $transaction->subscribe_member_id = $member->id;
                    $transaction->payment_status = $subscribe['subscription_status'];
                    $transaction->price = $plan->price;
                    $transaction->data = serialize($subscribe);
                    $transaction->save();
                    return response()->json(['status'=>true,'msg'=>'Membership activated successfully','data'=>$user]);
                }else{
                    $member->status = 'Pending';
                    $member->save();
                    return response()->json($subscribe);
                }
            } else{
                $member->status = 'Pending';
                $member->save();
                return response()->json($square_user_card);
            }
        }else{
            $member->status = 'Pending';
            $member->save();
            return response()->json($square_user);
        }
    }

    public function elitMembershipRequest(Request $request){
        $validation = Validator::make($request->all(),[
            'name'=>'required|max:191',
            'phone'=>'required|max:12',
            'email'=>'required|email',
            'state'=>'required|max:191',
            'city'=>'required|max:191',
            'message'=>'required|max:2000',
            'plan_id' => 'required'
        ]);

        $input = $request->all();

        if($validation->fails()){
            return errorMsgResponse($validation->errors()->first());
        }
        // dd($request->all());
        $user = Auth::user();
        $elite_member_req = EliteMembershipRequest::where('user_id',$user->id)->whereNotIn('status',['elite_member'])->first();
        if($elite_member_req){
            $elite_member_request_status = config('constant.elite_member_request_status');
            return errorMsgResponse('You have already been requested for elite member and your current status is '.$elite_member_request_status[$elite_member_req->status].', So please contact to adminstrator.');
        }

        $lareadyRequest = $this->member->where(['user_id'=>$user->id,'activated_from'=>'Admin','subscription_plan_id'=>$request->plan_id])->first();
        if($lareadyRequest){
            return errorMsgResponse('You have already been requested for elite member, So please purchase elite membership or contact to adminstrator');
        }
        $member_req = new EliteMembershipRequest;
        $member_req->user_id = $user->id;
        $member_req->name = $request->name;
        $member_req->phone = $request->phone;
        $member_req->email = $request->email;
        $member_req->state = $request->state;
        $member_req->city = $request->city;
        $member_req->message = $request->message;
        $member_req->save();
        
        $current_membership = $this->member->where(['user_id'=>$user->id,'status'=>'Active'])->first();
        if(!$current_membership){
            $customerRole = \App\Models\Role::where('name', 'CUSTOMER')->first();
            $user->roles()->attach($customerRole);
            /*$current_membership = new SubscribeMember; 
            $current_membership->user_id = $user->id;
            $current_membership->subscription_plan_id = 1;
            $current_membership->request_plan_id = $input['plan_id'];
            $current_membership->activated_date = date('Y-m-d');
            $current_membership->renewal_date = date('Y-m-d',strtotime('+ 10 years'));
            $current_membership->status = 'Active';
            $current_membership->activated_from = 'Online';
            $current_membership->save();*/
            $plan = SubscriptionPlan::where('id',1)->first();
        }else{
            $current_membership->request_plan_id = $input['plan_id'];
            $current_membership->save();
            $plan = SubscriptionPlan::where('id',$current_membership->subscription_plan_id)->first();
        }

        $user->elite_member_request = $this->member->where(['user_id'=>$user->id,'activated_from'=>'Admin','subscription_plan_id'=>$request->plan_id])->count();
        $user->plan = [
                'id'=>$plan->id,
                'name'=>$plan->name,
                'price'=>$plan->price??"Free",
                'square_payment_subscription_id'=>'',
                'elite_member_id' => $request->plan_id,
            ];
        return response()->json(['status'=>true,'msg'=>'Your request has been sent successfully. An Administrator will contact you shortly.','data'=>$user]);        
    }

    public function getElitMembershipRequest(){
        $user = Auth::user();
        $current_status_msg = '';
        $current_status_key = '';
        $elite_member_activate_key = 'elite_member';;
        $member_req = EliteMembershipRequest::where('user_id',$user->id)->first();
        $is_requested = false;
        $elite_member_request_status = config('constant.elite_member_request_status');
        if($member_req){
            $is_requested = true;
            $current_status_msg = $elite_member_request_status[$member_req->status];
            $current_status_key = $member_req->status;
        }

        return response()->json([
            'is_requested'=>$is_requested,
            'current_status_key'=>$current_status_key,
            'current_status_msg'=>ucfirst($current_status_msg),
            'elite_member_activate_key'=>$elite_member_activate_key,
            'elite_member_request_status_list'=>$elite_member_request_status,
        ]);
    }

    public function activeElitMembershipRequest(){
        $user = Auth::user();
        return dataResponse(getLoginDetails($user));
    }

    public function activateApplePurchasedPlan(Request $request, SubscriptionPlan $plan){
        $validation = Validator::make($request->all(),[
            'device'=>'required',
            'subscription_token'=>'required'
        ]);

        if($validation->fails()){
            return errorMsgResponse($validation->errors()->first());
        }

        $user = Auth::user();
        $current_membership = $this->member->where(['user_id'=>$user->id,'status'=>'Active'])->first();
        if($current_membership && $current_membership->subscription_plan_id!=1){
            return errorMsgResponse('Please cancel your previous subscription, Then you can upgrade the membership');
        }

        $membership = $this->member->where(['user_id'=>$user->id,'square_payment_subscription_id'=>$request->subscription_token])->first();
        if(!empty($membership)){
            return response()->json(['status'=>false,'msg'=>'']);
        }

        $member = new $this->member;
        $member->user_id = $user->id;
        $member->subscription_plan_id = $plan->id;
        $member->activated_date = date('Y-m-d');
        $member->renewal_date = ($plan->subscription_interval==12) ? date('Y-m-d',strtotime('+ 1 years')) : date('Y-m-d',strtotime('+ 1 month'));
        $member->status = 'Active';
        $member->activated_from = 'Online';
        $member->square_payment_subscription_id = $request->subscription_token;
        $member->device = $request->device;
        $member->save();

        $user->elite_member_request = $this->member->where(['user_id'=>$user->id,'activated_from'=>'Admin','subscription_plan_id'=>config('constant.elite_member_id')])->count();
        
        $user->plan = [
            'id'=>$plan->id,
            'name'=>$plan->name,
            'price'=>$plan->price,
            'square_payment_subscription_id'=>$request->subscription_token,
            'elite_member_id' => config('constant.elite_member_id'),
            'device' => $request->device,
        ];
        
        if($member->id && !empty($current_membership)){
            $current_membership->status = 'Upgraded';
            $current_membership->device = $request->device;
            $current_membership->save();
        }

        $transaction = new \App\Models\SubscribeMemberTransaction;
        $transaction->subscribe_member_id = $member->id;
        $transaction->payment_status = 'ACTIVE';
        $transaction->price = $plan->price;
        $transaction->data = NULL;
        $transaction->save();

        $update = $this->member->where(['user_id'=>$user->id])->update(['device' => $request->device]);

        return response()->json(['status'=>true,'msg'=>'Membership activated successfully','data'=>$user]);
    }

}
