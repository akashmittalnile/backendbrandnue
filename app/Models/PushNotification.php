<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PushNotification extends Model
{
    public function shares(){
        return $this->morphMany('App\Models\Share', 'shareable');
    }

    public function plan(){
        return $this->belongsTo('\App\Models\SubscriptionPlan','subscription_plan_id','id')->withDefault(['name'=>'N/A']);
    }

    public $timestamps = true;

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];
}
