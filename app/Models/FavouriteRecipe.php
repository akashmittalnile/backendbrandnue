<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
class FavouriteRecipe extends Model
{
    public function getFavouriteRecipeList($user_id,$pagination=true){
        $query = $this->newQuery();
        $query->where('favourite_recipes.user_id',$user_id);
        $query->select('recipes.*');
        $query->join('recipes','favourite_recipes.recipe_id','recipes.id');
        $query->addSelect(DB::raw('1 as is_favourite , (select url from images where images.imageable_id=recipes.id  and type="recipe_image" limit 1) as recipe_image') );
        if($pagination){
            $result = $query->paginate(config('constant.adminPerPage'));
            return $result;
        }
        return $query;
        
    }
}
