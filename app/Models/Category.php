<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public function getCategoryList($request){
        $query = $this->newQuery();
        $data = $query->orderBy('name')->paginate(config('constant.adminPerPage'));
        return $data;
    }
}
