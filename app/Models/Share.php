<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Share extends Model
{
    public function shareable(){
        return $this->morphTo();
    }
    public function getShareNotification($request,$login_id){
        $query = $this->newQuery();
        $query->select('push_notifications.id','shares.id as share_id','push_notifications.title','push_notifications.image','push_notifications.data','shares.status','push_notifications.created_at');
        $query->where(['shares.user_id'=>$login_id,'shares.shareable_type'=>'App\Models\PushNotification','push_notifications.status'=>config('constant.status.active')]);
        $query->join('push_notifications','shares.shareable_id','=','push_notifications.id');
        $data = $query->orderBy('shares.id','desc')->get()->toArray();

        $lengths = array_map( function($item) {
            $item['created_at'] = date('H:i:s a, d M Y', strtotime($item['created_at']));
            // $item['created_at'] = Carbon::parse($item['created_at'])->format('h:i a, d M Y');;
            return $item;
        } , $data);

        // dd($lengths);
        return $lengths;
    }

    // public function getCreatedAtAttribute()
    // {
    //     return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)->format('Y-m-d');
    // }

    // public function getUpdatedAtAttribute()
    // {
    //     return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->updated_at)->format('Y-m-d');
    // }
}
