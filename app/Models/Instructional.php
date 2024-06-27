<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Instructional extends Model
{
    

    public function plan(){
        return $this->belongsTo('App\Models\SubscriptionPlan','subscription_plan_id','id')->withDefault(['name'=>NULL]);
    }
    
    public function getInstructoinalVideos($request,$status='Y'){
        $query =  $this->newQuery();
        if(!empty($status)){
            $query->where('status',$status);
        }
        $query->where(['type'=>'video']);
        $data = $query->with(['plan:id,name'])->orderBy('id','desc')->paginate(config('constant.adminPerPage'));
        return $data;
    }

    public function apiGetInstructoinalVideos($request,$plan_id){
        $query =  $this->newQuery();
        $query->where(['type'=>'video','status'=>'Y']);

        $query->InstrunctionCheck($plan_id);
        $data = $query->with(['plan:id,name'])->orderBy('id','desc')->paginate(config('constant.adminPerPage'));
        return $data;
    }

    public function getInstructoinalTemplates($request,$status = 'Y'){
        $query =  $this->newQuery();
        if(!empty($status)){
            $query->where('status',$status);
        }
        $query->where('type','template');
        $data = $query->with(['plan:id,name'])->orderBy('id','desc')->paginate(config('constant.adminPerPage'));
        return $data;
    }

    public function apiGetInstructoinalTemplates($request,$plan_id){
        $query =  $this->newQuery();
        $query->where(['type'=>'template','status'=>'Y']);
        $query->InstrunctionCheck($plan_id);
        $data = $query->with(['plan:id,name'])->orderBy('id','desc')->paginate(config('constant.adminPerPage'));
        return $data;
    }

    public function getInstructoinalGuides($request,$status = 'Y'){
        $query =  $this->newQuery();
        if(!empty($status)){
            $query->where('status',$status);
        }
        $query->where('type','guide');
        $data = $query->with(['plan:id,name'])->orderBy('id','desc')->paginate(config('constant.adminPerPage'));
        return $data;
    }

    public function apiGetInstructoinalGuides($request,$plan_id){
        DB::enableQueryLog();
        $query =  $this->newQuery();
        $query->where(['type'=>'guide','status'=>'Y']);
        $query->whereOr(['subscription_type'=>null]);
        $query->InstrunctionCheck($plan_id);
        $data = $query->with(['plan:id,name'])->orderBy('id','desc')->paginate(12);
        //print_r(DB::getQueryLog());
        return $data;
    }

    public function scopeInstrunctionCheck($query,$plan_id){
        return $query->where(function($qry) use($plan_id){
            if(in_array($plan_id,premiumIds())){
                $qry->where('subscription_type','P');
            }else if(in_array($plan_id,eliteIds())){
                $qry->where('subscription_type','E');
            }else if(in_array($plan_id,standardIds())){
                $qry->where('subscription_type','S');
            }
            
            $qry->orWhereNull('subscription_type');
        });
    }
}
