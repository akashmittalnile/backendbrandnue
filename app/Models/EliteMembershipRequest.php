<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EliteMembershipRequest extends Model
{
    public function user(){
        return $this->belongsTo('App\User','user_id','id');
    }

    public function eliteMemberByAdmin(){
        return $this->hasOne('App\Models\SubscribeMember','user_id','user_id')->where('activated_from','Admin');
    }

    public function getEliteMembershipList($request){
        $query = $this->newQuery();
        if(!empty($request->status)){
            $query->where('status',$request->status);
        }

        if($request->filled('f')){
            $query->whereDate('created_at','>=',$request->f);
        }

        if($request->filled('t')){
            $query->whereDate('created_at','<=',$request->t);
        }

        $data = $query->orderBy('id','desc')->paginate(config('constant.adminPerPage'));
        return $data;
    }
}
