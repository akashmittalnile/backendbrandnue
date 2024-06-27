<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EliteMembershipRequest;
use App\Models\SubscriptionPlan;
use Validator;

class MembershipController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function elitMembershipRequestList(Request $request){
                
        $elite = new EliteMembershipRequest;
        $elit_member_requests = $elite->getEliteMembershipList($request);
        return view('admin.membership.elite-membership-request-list',compact('elit_member_requests'));
    }

    public function getModal(EliteMembershipRequest $member){
        $seen = $member->read_status;
        $member->read_status = 0; 
        $member->save();
        $member->load('eliteMemberByAdmin');

        $html = view('admin.membership.elite-membership-request-detail',compact('member'))->render();
        return response()->json(['status'=>true,'html'=>$html,'seen'=>$seen]);
    }

    public function changeElitMembershipRequest(Request $request,EliteMembershipRequest $member){
        $purchase = \App\Models\SubscribeMember::where(['user_id'=>$member->user_id,'status'=>'Active','activated_from'=>'Online'])->whereNotIn('subscription_plan_id',[1])->first();
        if($purchase){
            return errorMsgResponse('You can not edit the start and end date, because he purchased the subscription Online.');
        }

        $rules['status'] = 'required';
        if($request->status=='elite_member'){
            $rules['start_date'] = 'required';
            $rules['end_date'] = 'required';
        }
        $validation = Validator::make($request->all(),$rules);
        if($validation->fails()){
            return errorMsgResponse($validation->errors()->first());
        }
        $member->status = $request->status;
        $member->response_date = date('Y-m-d');
        $member->save();
        if($request->status=='elite_member'){
            $current_plan = \App\Models\SubscribeMember::where(['user_id'=>$member->user_id,'status'=>'Active','subscription_plan_id'=>1])->first();
            if($current_plan){
                $current_plan->status = 'Upgraded';
                $current_plan->save();
            }
            
            $obj = \App\Models\SubscribeMember::where(['user_id'=>$member->user_id,'activated_from'=>'Admin'])->first();

            if(!$obj){
                $obj = new \App\Models\SubscribeMember;
                $obj->user_id = $member->user_id;
                // $obj->subscription_plan_id = config('constant.elite_member_id');
                $obj->subscription_plan_id = $current_plan->request_plan_id ?? config('constant.elite_member_id');
                $obj->request_plan_id = $current_plan->request_plan_id ?? config('constant.elite_member_id');
                $obj->status = 'Active';
                $obj->activated_from = 'Admin';
            }
            
            $obj->activated_date = date('Y-m-d',strtotime($request->start_date));
            $obj->renewal_date = date('Y-m-d',strtotime($request->end_date));
            $obj->save();
        }
        return response()->json(['status'=>true,'response'=>'Response Date: '.dateFormat(date('Y-m-d'))]);
    }

    public function getPlanList(){
        $plans = [];
        $records = SubscriptionPlan::all();
        if($records->count()){
            foreach($records as $row){
                $description = str_replace('</p>', '', $row->description);
                $description = explode('<p>', $description);
                $temp = [];
                if(count($description)){
                    foreach($description as $desc){

                        if(!empty($desc)){
                            array_push($temp,htmlspecialchars_decode(strip_tags($desc)));
                        }
                    }
                }
                $row->description = $temp;
                array_push($plans,$row);
            }
        }
        return view('admin.membership.list',compact('plans'));
    }

    public function paymentList(Request $request){
        $plan = new SubscriptionPlan;
        $member = new \App\Models\SubscribeMember;
        $subscribe_members = $plan->getSubscribeMember($request);
        //$plans = $plan->getPlan();

        $data['subscribe_members']  = $subscribe_members;
        $data['premium_payment']    = $member->getSubscribeTotalPrice(2);
        $data['elit_payment']       = $member->getSubscribeTotalPrice(3);
        $data['total_payment']      = $data['premium_payment']+$data['elit_payment'];
        
        return view('admin.payments.list',$data);
    }

    public function editPlan(SubscriptionPlan $plan){
        return view('admin.membership.edit',compact('plan'));
    }

    public function updatePlan(Request $request,SubscriptionPlan $plan){
        $plan->description = $request->description;
        $plan->save();
        \Session::flash('success','Plan Description updated successfully');
        return back();
    }
    
}
