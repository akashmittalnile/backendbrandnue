<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplement extends Model
{
    public function getSupplementList(){
        $query = $this->newQuery();
        $data = $query->orderBy('name')->paginate(config('constant.adminPerPage'));
        return $data;
    }
}
