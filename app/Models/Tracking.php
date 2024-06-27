<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tracking extends Model
{
    public function getSupplementAttribute($value){
        if(empty($value)){
            return [];
        }
        $data = encryptDecrypt('decrypt', $value);
        return unserialize($data);
    }

    public function getExerciseAttribute($value){
        if(empty($value)){
            return [];
        }
        $data = encryptDecrypt('decrypt', $value);
        return unserialize($data);
    }

    public function getBreakfastAttribute($value){
        if(empty($value)){
            return ['start_time'=>[],'food_type'=>[]];
        }
        $data = encryptDecrypt('decrypt', $value);
        return unserialize($data);
    }

    public function getLunchAttribute($value){
        if(empty($value)){
            return ['start_time'=> [],'food_type'=>[]];
        }
        $data = encryptDecrypt('decrypt', $value);
        $data = unserialize($data);
        // if(isset($data['start_time']) && empty($data['start_time'])){
        //     $data['start_time'] = (object)[];
        // }
        return $data;
    }

    public function getSnackAttribute($value){
        if(empty($value)){
            //return ['start_time'=>(object)[],'food_type'=>[]];
            return [];
        }
        $data = encryptDecrypt('decrypt', $value);
        $data = unserialize($data);
        
        /*if(isset($data['start_time']) && empty($data['start_time'])){
            $data['start_time'] = (object)[];
        }*/
        return $data;
    }

    public function getDinnerAttribute($value){
        if(empty($value)){
            return ['end_time'=> [],'food_type'=>[]];
        }
        $data = encryptDecrypt('decrypt', $value);
        $data = unserialize($data);
        
        if(isset($data['end_time']) && empty($data['end_time'])){
            $data['end_time'] = [];
        }
        return $data;
    }
    public function getTotalExerciseDurationAttribute($value){
        $data = encryptDecrypt('decrypt', $value);
        return unserialize($data);
    }

    public function getFastStartTimeAttribute($value){
        if(!empty($value)){
            return date("H:i A",strtotime($value));
        }
        return $value;
    }

    public function getFastEndTimeAttribute($value){
        if(!empty($value)){
            return date("H:i A",strtotime($value));
        }
        return $value;
    }

    public function getUserTrackingById($request,$user_id){
        $query = $this->newQuery();
        $query->where('user_id',$user_id);
        if($request->filled('f')){
            $query->whereDate('track_date','>=',date('Y-m-d',strtotime($request->f)));
        }

        if($request->filled('t')){
            $query->whereDate('track_date','<=',date('Y-m-d',strtotime($request->t)));
        }
        $data = $query->orderBy('track_date','desc')->paginate(config('constant.adminPerPage'));
        return $data;
    }
}
