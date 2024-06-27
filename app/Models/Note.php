<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    
    public function noteable(){
        return $this->morphTo();
    }

    public function getNoteList($user_id){
        $query = $this->newQuery();
        $query->where('noteable_id',$user_id);
        $data = $query->get();
        return $data;
    }
}
