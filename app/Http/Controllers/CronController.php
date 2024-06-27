<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubscribeMember;
use App\Models\SubscriptionPlan;
use App\Library\PostFunction;

use Square\SquareClient;
use Square\LocationsApi;

use DB;

class CronController extends Controller
{
    
    public function checkSubscription(){
        $subscriptions = SubscribeMember::where(['device'=>'Android','status'=>'Active'])->whereDate('renewal_date','<',date('Y-m-d'))->get();
        
        if($subscriptions->count()){
            foreach($subscriptions as $subscription){
                /*Check for Elite member make by Admin*/
                if(empty($subscription->square_payment_subscription_id) && $subscription->subscription_plan_id!=config('constant.standard_member_id')){
                    $subscription->status = 'Cancelled';
                    $subscription->modify_date = date('Y-m-d');
                    $subscription->save();
                    $free = SubscribeMember::where(['user_id'=>$subscription->user_id,'subscription_plan_id'=>1])->first();
                    if(!$free){
                        $free = new SubscribeMember;
                        $free->user_id = $subscription->user_id;
                        $free->subscription_plan_id = 1;
                        $free->activated_from = 'Admin';
                        $free->activated_date = date('Y-m-d');
                        $free->renewal_date = date('Y-m-d',strtotime('+1 years'));
                    }

                    if($free->renewal_date<=date('Y-m-d')){
                        $free->renewal_date = date('Y-m-d',strtotime('+1 years'));   
                    }

                    $free->status = 'Active';
                    $free->save();
                    \Log::info('Elite Member',['cancelled_id'=>$subscription->id]);
                    echo "Elite Member";
                }else if($subscription->subscription_plan_id==config('constant.standard_member_id') && $subscription->renewal_date<=date('Y-m-d')){
                    $subscription->renewal_date = date('Y-m-d',strtotime('+1 years'));   
                    $subscription->save();
                    echo "Free Member";
                }else{

                    //echo $subscription->square_payment_subscription_id;
                    $list = (new PostFunction)->searchSubscription($subscription->square_payment_subscription_id);
                    if($list['status']==true && strtolower($list['subscriptionStatus'])=='active'){
                        $subscription->renewal_date = $list['chargedThroughDate'];
                        $subscription->save();
                    }else if($list['status']==true && strtolower($list['subscriptionStatus'])==strtolower('CANCELED')){
                        $subscription->modify_date = $list['canceledDate'];
                        $subscription->status = 'Cancelled';
                        $subscription->save();

                        $free = SubscribeMember::where(['user_id'=>$subscription->user_id,'subscription_plan_id'=>1])->first();
                        if(!$free){
                            $free = new SubscribeMember;
                            $free->user_id = $subscription->user_id;
                            $free->subscription_plan_id = 1;
                            $free->activated_from = 'Admin';
                            $free->activated_date = date('Y-m-d');
                            $free->renewal_date = date('Y-m-d',strtotime('+1 years'));
                        }

                        if($free->renewal_date<=date('Y-m-d')){
                            $free->renewal_date = date('Y-m-d',strtotime('+1 years'));   
                        }

                        $free->status = 'Active';
                        $free->save();
                    }
                }
            }
        }
    }


    /**
     * Only active subcription check the status
     */
    public function checkInvoice(){
        $client = new SquareClient([
            'accessToken' => config('constant.squareAccessToken'),
            'environment' => config('constant.squareEnvironment'),
        ]);

        $active = \App\Models\SubscribeMember::where(['status'=>'Active', 'device'  => 'Android'])->whereNotNull('square_payment_subscription_id')->get();
        if ($active->isNotEmpty()) {
            foreach ($active as $k => $member) {
                // code...
                $plan = \App\Models\SubscriptionPlan::find($member->subscription_plan_id);
                $api_response = $client->getSubscriptionsApi()->retrieveSubscription($member->square_payment_subscription_id);
                if ($api_response->isSuccess()) {
                    $result = $api_response->getResult()->getSubscription();
                    $invoice = $result->getInvoiceIds();
                    if(!is_array($invoice)){
                        $invoice = [$invoice];
                    }

                    if (!empty($invoice) && is_array($invoice)) {
                        $in_id = reset($invoice);
                        $apiresponse = $client->getInvoicesApi()->getInvoice($in_id);
                        if ($apiresponse->isSuccess()) {
                            $res = $apiresponse->getResult()->getInvoice();
                            $st = $res->getstatus();
                            if ($st != 'PAID') {
                                // code...
                                $member->status = 'Upgraded';
                                $member->save();

                                $free = new \App\Models\SubscribeMember;
                                $free->user_id = $member->user_id;
                                $free->subscription_plan_id = 1;
                                $free->activated_from = 'Online';
                                $free->activated_date = date('Y-m-d');
                                $free->renewal_date = date('Y-m-d',strtotime('+1 years'));
                                $free->status = 'Active';
                                $free->save();
                                \Log::info('Active Member',['user_id'=>$member->user_id]);
                            }
                        }
                    }            
                }
            }
        }

        // dd($active);
    }


    /**
     * Only upgrade subscription check the status
     */
    public function checkMembership(){
        $client = new SquareClient([
            'accessToken' => config('constant.squareAccessToken'),
            'environment' => config('constant.squareEnvironment'),
        ]);

        $query = (new SubscribeMember)->newQuery();
        $query->where(function($q){
            $q->whereNotIn('user_id', function($query){
                $query->select(DB::raw('user_id'))
                    ->from('subscribe_members')
                    ->where(['status'=>'Active', 'device'  => 'Android'])
                    ->whereNotNull('square_payment_subscription_id');                    
            });
        });

        $query->whereIn( 'id', function($query){
            $query->selectRaw('MAX(id)')->from('subscribe_members')->where(['status'=>'Upgraded', 'device'  => 'Android'])->whereNotNull('square_payment_subscription_id')->groupBy('user_id');
        });

        $active = $query->get();

        if ($active->isNotEmpty()) {
            foreach ($active as $k => $member) {
                // code...
                $plan = \App\Models\SubscriptionPlan::find($member->subscription_plan_id);
                $api_response = $client->getSubscriptionsApi()->retrieveSubscription($member->square_payment_subscription_id);
                if ($api_response->isSuccess()) {
                    $result = $api_response->getResult()->getSubscription();
                    $invoice = $result->getInvoiceIds();
                    if(!is_array($invoice)){
                        $invoice = [$invoice];
                    }

                    if (!empty($invoice) && is_array($invoice)) {
                        $in_id = reset($invoice);
                        $apiresponse = $client->getInvoicesApi()->getInvoice($in_id);
                        if ($apiresponse->isSuccess()) {
                            $res = $apiresponse->getResult()->getInvoice();
                            $dt = date('Y-m-d', strtotime($res->getcreatedAt()));
                            $st = $res->getstatus();
                            // dd([$dt, date('Y-m-d',strtotime($dt.'+ 1 month'))]);
                            // echo "<pre>";
                            // print_r($res);
                            // echo "<br>";
                            if ($st == 'PAID') {
                                $active_m = \App\Models\SubscribeMember::where(['user_id'=>$member->user_id,'status'=>'Active'])->update(['status' => 'Cancelled', 'modify_date' => date('Y-m-d')]);
                                // code...
                                $member->activated_date = $dt;
                                $member->renewal_date = ($plan->subscription_interval==12) ? date('Y-m-d',strtotime($dt.'+ 1 years')) : date('Y-m-d',strtotime($dt.'+ 1 month'));
                                $member->status = 'Active';
                                $member->save();
                                \Log::info('Upgraded Member',['user_id'=>$member->user_id]);
                                
                                /*if($active_m && empty($active_m->square_payment_subscription_id)){                                    
                                    $active_m->status = 'Cancelled';
                                    $active_m->modify_date = date('Y-m-d');
                                    $active_m->save();
                                }*/
                            }
                        }
                    }            
                }
            }
        }
    }
}
