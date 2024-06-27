<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
class Recipe extends Model
{
    public function images(){
        return $this->morphMany('App\Models\Image', 'imageable');
    }

    public function recipeImage(){
        return $this->hasOne('App\Models\Image', 'imageable_id')->where('type','recipe_image');
    }

    public function recipeImages(){
        return $this->morphMany('App\Models\Image', 'imageable')->where('type','recipe_image');
    }

    public function recipeAudios(){
        return $this->morphMany('App\Models\Image', 'imageable')->where('type','recipe_audio');
    }

    public function recipeVideos(){
        return $this->morphMany('App\Models\Image', 'imageable')->where('type','recipe_video');
    }

    public function getRecipe($request,$is_premium=['N']){
        $query = $this->newQuery();
        $query->select('*');
        $query->where(['status'=>config('constant.status.active')]);
        $query->whereIn('is_premium',$is_premium);
        if($request->filled('category')){
            $query->where('category_id',$request->category);
        }
        if($request->filled('user_id')){
            $query->addSelect(DB::raw('(select count(favourite_recipes.recipe_id)  from favourite_recipes where favourite_recipes.recipe_id=recipes.id and favourite_recipes.user_id='.$request->user_id.' ) as  is_favourite'));
            
        }else{
            $query->addSelect(DB::raw('false as is_favourite'));
        }

        if($request->filled('search')){
            $query->where(function($qry) use($request){
                $qry->where('meal_title','like','%'.$request->search.'%')->orWhere('meal_keyword','like','%'.$request->search.'%');
            });
        }
        
        $query->with(['recipeImage:imageable_id,url']);
        $result = $query->orderBy('meal_title', 'ASC')->paginate(config('constant.adminPerPage'));
        return $result;
    }

    public function getSearchMealList($request,$is_premium=['N']){
        $query =  $this->newQuery();
        $query->select('id','meal_title');
        $query->where(['status'=>config('constant.status.active')]);
        $query->whereIn('is_premium',$is_premium);
        if($request->filled('meal')){
            $query->where('meal_title','like','%'.$request->meal.'%');
        }
        $query->orderBy('meal_title');
        $result = $query->limit(10)->get();
        return $result;
    }

    public function adminGetRecipe($request){
        $query = $this->newQuery();
        if($request->filled('c')){
            $query->where('category_id',$request->c);
        }
        if($request->filled('s')){
            $query->where('meal_title','like','%'.$request->s.'%')->orWhere('meal_keyword','like','%'.$request->s.'%');
        }
        $result = $query->orderBy('id','desc')->with(['recipeImage'])->paginate(config('constant.adminPerPage'));
        return $result;
    }

    public function getDraftRecipe($request,$category=''){
        $query = $this->newQuery();
        $query->where('status',config('constant.status.in_active'));
        if(!empty($category)){
            $query->where('category_id',$category);
        }
        if($request->filled('s')){
            $query->where('meal_title','like','%'.$request->s.'%')->orWhere('meal_keyword','like','%'.$request->s.'%');
        }
        $result = $query->with(['recipeImage']);
        return $result;
    } 

    public function getPublishRecipe($request,$category=''){
        $query = $this->newQuery();
        $query->where('status',config('constant.status.active'));
        if(!empty($category)){
            $query->where('category_id',$category);
        }
        if($request->filled('s')){
            $query->where('meal_title','like','%'.$request->s.'%')->orWhere('meal_keyword','like','%'.$request->s.'%');
        }
        $result = $query->with(['recipeImage']);
        return $result;
    }

    public function getPremiumRecipe($request,$category=''){
        $query = $this->newQuery();
        $query->where(['is_premium'=>config('constant.status.active'),'status'=>config('constant.status.active')]);
        if(!empty($category)){
            $query->where('category_id',$category);
        }
        if($request->filled('s')){
            $query->where('meal_title','like','%'.$request->s.'%')->orWhere('meal_keyword','like','%'.$request->s.'%');
        }
        $result = $query->with(['recipeImage']);
        return $result;
    }

    public function getFreeRecipe($request,$category=''){
        $query = $this->newQuery();
        $query->where(['is_premium'=>config('constant.status.in_active'),'status'=>config('constant.status.active')]);
        if(!empty($category)){
            $query->where('category_id',$category);
        }
        if($request->filled('s')){
            $query->where('meal_title','like','%'.$request->s.'%')->orWhere('meal_keyword','like','%'.$request->s.'%');
        }
        $result = $query->with(['recipeImage']);
        return $result;
    }

    public function recipeDetail($recipe_id,$user_id=''){
        $query = $this->newQuery();
        $query->where('id',$recipe_id);
        $query->select('*');
        if(!empty($user_id)){
            $query->addSelect(DB::raw('(select count(favourite_recipes.recipe_id)  from favourite_recipes where favourite_recipes.recipe_id=recipes.id and favourite_recipes.user_id='.$user_id.' ) as  is_favourite'));
            
        }else{
            $query->addSelect(DB::raw('false as is_favourite'));
        }
        $result = $query->with(['recipeImages:imageable_id,url','recipeAudios:imageable_id,url','recipeVideos:imageable_id,url']);
        
        return $result;
    }
}
