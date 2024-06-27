<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
class SubscriptionPlan extends Model
{
    
    public function getPlan(){
        $query = $this->newQuery();
        $query->where('status',config('constant.status.active'));
        $query->select('*',DB::raw('ifnull(price,"Free") as price'));
        $result = $query->orderBy('name','desc')->get();
        return $result;
    }

    public function totalMemberUser(){
        return $this->hasMany('App\Models\SubscribeMember','subscription_plan_id','id')->where('status','Active');
    }

    public function getSubscribeMember($request){
        $query = $this->newQuery();
        $query->select('users.*','subscription_plans.name as plan_name','subscribe_members.id as subscribe_member_id','subscription_plans.price');
        $query->whereIn('subscription_plans.id',[3,2]);
        $query->where('subscribe_members.status','Active');
        $query->join('subscribe_members','subscription_plans.id','=','subscribe_members.subscription_plan_id');
        $query->join('users','subscribe_members.user_id','=','users.id');
        $data = $query->orderBy('users.id','desc')->paginate(config('constant.adminPerPage'));
        return $data;
    }
}
