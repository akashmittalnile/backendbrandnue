<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SubscribeMemberTransaction;
use App\User;

class SubscribeMember extends Model
{

    public function planName(){
        return $this->belongsTo('App\Models\SubscriptionPlan','subscription_plan_id','id');
    }

    /*Get total subscription price by plan id*/
    public function getSubscribeTotalPrice($plan_id){ 
        $query = $this->newQuery();
        $query->where('subscribe_members.subscription_plan_id',$plan_id);
        $query->join('subscribe_member_transactions','subscribe_members.id','=','subscribe_member_transactions.subscribe_member_id');
        $data = $query->sum('subscribe_member_transactions.price');
        return $data;
    }

    /*Get total subscription price by plan id with particular year month*/
    public function getSubscribePriceByYearMonth($plan_id,$year,$month){
        $query = $this->newQuery();
        $query->where('subscribe_members.subscription_plan_id',$plan_id);
        $query->whereYear('subscribe_members.activated_date',$year);
        $query->whereMonth('subscribe_members.activated_date',$month);
        $query->join('subscribe_member_transactions','subscribe_members.id','=','subscribe_member_transactions.subscribe_member_id');
        $data = $query->sum('subscribe_member_transactions.price');
        return $data;
    }

    /**
     * 05-dec-22
     * code by sanjay
     */
    public function transaction(){
        return $this->hasOne(SubscribeMemberTransaction::class, 'subscribe_member_id', 'id');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
}
