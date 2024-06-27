<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SubscribeMember;

class SubscribeMemberTransaction extends Model
{
    public function member(){
        return $this->belongsTo(SubscribeMember::class, 'subscribe_member_id');
    }
}
