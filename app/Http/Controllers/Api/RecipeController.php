<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Recipe;
use Auth;
class RecipeController extends Controller
{
    public $recipe;
    public function __construct(Recipe $recipe){
        $this->recipe = $recipe;
    }

    public function getRecipe(Request $request){
        $user = Auth::user();
        
        $request->request->add(['user_id'=>$user->id??'']);
        if(isPremium($user)){
            $recipe = $this->recipe->getRecipe($request,[config('constant.status.active'),config('constant.status.in_active')]); 
            return dataResponse($recipe);    
        }else if(isElite($user)){
            $recipe = $this->recipe->getRecipe($request,[config('constant.status.active'),config('constant.status.in_active')]); 
            return dataResponse($recipe);    
        }
        $recipe = $this->recipe->getRecipe($request); 
        return dataResponse($recipe);
    }

    public function getRecipeDetails($id){
        $user = Auth::user();
        $recipe = $this->recipe->recipeDetail($id,$user->id)->first();
        return dataResponse($recipe);
    }

    public function categoryList(){
        $categories = \App\Models\Category::where('status',config('constant.status.active'))->select('id','name')->get();
        return dataResponse($categories);
    }

} 
