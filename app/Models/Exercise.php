<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    public function getExerciseList(){
        $query = $this->newQuery();
        $data = $query->orderBy('name')->paginate(config('constant.adminPerPage'));
        return $data;
    }
}
