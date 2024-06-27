<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Recipe;
use App\Models\Image;
use Validator;
use Session;
use DB;

class RecipeController extends Controller
{
    public $recipe;
    public function __construct(Recipe $recipe){
        $this->middleware('auth');
        $this->recipe = $recipe;
    }

    public function recipeList(Request $request){
        
        $draft_recipe = $this->recipe->getDraftRecipe($request)->count();
        $publish_recipe = $this->recipe->getPublishRecipe($request)->count();
        $premium_recipe = $this->recipe->getPremiumRecipe($request)->count();
        $free_recipe = $this->recipe->getFreeRecipe($request)->count();
        $categories = Category::where('status',config('constant.status.active'))->orderBy('name')->get();
        $recipes = $this->recipe->adminGetRecipe($request);
        return view('admin.digital.recipe-list',compact('recipes','draft_recipe','publish_recipe','premium_recipe','free_recipe','categories'));
    }

    public function createRecipe(){
        $categories = Category::where('status',config('constant.status.active'))->orderBy('name')->get();
        return view('admin.digital.add-recipe',compact('categories'));
    }

    public function recipeListWithCondition(Request $request,$status){
        $categories = Category::where('status',config('constant.status.active'))->orderBy('name')->get();
        if($status=='DRAFT'){
            $recipes = $this->recipe->getDraftRecipe($request,$request->c)->paginate(config('constant.adminPerPage'));
            return view('admin.digital.draft-recipe-list',compact('recipes','categories'));
        }else if($status=='PUBLISH'){
            $recipes = $this->recipe->getPublishRecipe($request,$request->c)->paginate(config('constant.adminPerPage'));
            return view('admin.digital.publish-recipe-list',compact('recipes','categories'));
        }else if($status=='PREMIUM'){
            $recipes = $this->recipe->getPremiumRecipe($request,$request->c)->paginate(config('constant.adminPerPage'));
            return view('admin.digital.premium-recipe-list',compact('recipes','categories'));
        }else if($status=='FREE'){
            $recipes = $this->recipe->getFreeRecipe($request,$request->c)->paginate(config('constant.adminPerPage'));
            return view('admin.digital.free-recipe-list',compact('recipes','categories'));
        }
        abort(404);
    }

    public function storeRecipe(Request $request){
        
        $validate = Validator::make($request->all(),[
            'meal_title'=>'required|max:191',
            'meal_keyword'=>'required|max:191'
        ]);

        if($validate->fails()){
            return errorMsgResponse($validate->errors()->first());
        }
        DB::beginTransaction();
        try {
            $recipe = new $this->recipe;
            $recipe->meal_title = $request->meal_title;
            $recipe->meal_keyword = $request->meal_keyword;
            $recipe->is_premium = $request->premium??config('constant.status.in_active');
            $recipe->image_description = $request->image_description;
            $recipe->video_description = $request->video_description;
            $recipe->audio_description = $request->audio_description;
            $recipe->category_id = $request->category_name;
            $recipe->status = $request->status;
            $recipe->save();
            if($recipe->id){
                if($request->hasFile('image')){
                    $path = 'uploads/recipe';
                    $image = new Image;
                    $image->url = uploadImage($request,'image',$path);
                    $image->type = 'recipe_image';
                    $recipe->images()->save($image);
                }

                /*if($request->hasFile('recipe_video')){
                    $path = 'uploads/recipe';
                    $video = new Image;
                    $video->url = uploadImage($request,'recipe_video',$path);
                    $video->type = 'recipe_video';
                    $recipe->images()->save($video);
                }*/

                if($request->hasFile('recipe_audio')){
                    $path = 'uploads/recipe';
                    $audio = new Image;
                    $audio->url = uploadImage($request,'recipe_audio',$path);
                    $audio->type = 'recipe_audio';
                    $recipe->images()->save($audio);
                }

                if($request->filled('video_type') && $request->video_type=='External' && !empty($request->embeded_video_url)){
                    $video = new Image;
                    $video->url = $request->embeded_video_url;
                    $video->type = 'recipe_video';
                    $video->file_location = 'External';
                    $recipe->images()->save($video);
                }

                if($request->filled('video_type') && $request->video_type=='Local' && !empty($request->recipe_video_url)){
                    $video = new Image;
                    $video->url = $request->recipe_video_url;
                    $video->type = 'recipe_video';
                    $video->file_location = 'Local';
                    $recipe->images()->save($video);
                }
            }
            DB::commit();
            return response()->json(['status'=>true,'url'=>route('admin.recipe.list'),'msg'=>'Recipe created successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return errorMsgResponse($e->getMessage());
        }
        
    }

    public function recipeEdit(Recipe $recipe){
        $categories = Category::where('status',config('constant.status.active'))->orderBy('name')->get();
        return view('admin.digital.edit-recipe',compact('recipe','categories'));
    }

    public function recipeDetail(Recipe $recipe){
        return view('admin.digital.recipe-detail',compact('recipe'));
    }


    public function updateRecipe(Request $request,Recipe $recipe){
        $validate = Validator::make($request->all(),[
            'meal_title'=>'required|max:191',
            'meal_keyword'=>'required|max:191'
        ]);
        
        if($validate->fails()){
            return errorMsgResponse($validate->errors()->first());
        }
        DB::beginTransaction();
        try {
            $recipe->meal_title = $request->meal_title;
            $recipe->meal_keyword = $request->meal_keyword;
            $recipe->is_premium = $request->premium??config('constant.status.in_active');
            $recipe->image_description = $request->image_description;
            $recipe->video_description = $request->video_description;
            $recipe->audio_description = $request->audio_description;
            $recipe->category_id = $request->category_name;
            if($request->filled('video_type') && $request->video_type=='External' && !empty($request->embeded_video_url)){
                $video = new Image;
                $video->url = $request->embeded_video_url;
                $video->type = 'recipe_video';
                $video->file_location = 'External';
                $recipe->images()->save($video);
            }

            if($request->filled('video_type') && $request->video_type=='Local' && !empty($request->recipe_video_url)){
                $video = new Image;
                $video->url = $request->recipe_video_url;
                $video->type = 'recipe_video';
                $video->file_location = 'Local';
                $recipe->images()->save($video);
            }

            if(!empty($request->status)){
                $recipe->status = $request->status;
            }
            if($request->status==config('constant.status.in_active')){
                $recipe->is_premium = config('constant.status.in_active');
            }
            $recipe->save();
            DB::commit();
            
            return response()->json(['status'=>true,'url'=>route('admin.recipe.list'),'msg'=>'Recipe updated successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return errorMsgResponse($e->getMessage());
        }
        
    }

    public function uploadFile(Request $request,Recipe $recipe){
        if($request->hasFile('image')){
            $path = 'uploads/recipe';
            $url = uploadImage($request,'image',$path);
            $image = new Image;
            $image->url = $url;
            $image->type = 'recipe_image';
            $recipe->images()->save($image);
            return response()->json(['status'=>true,'url'=>asset($url),'id'=>$image->id]);
        }else if($request->type=='video'){
            
            $image = new Image;
            $image->url = $request->url;
            $image->type = 'recipe_video';
            $recipe->images()->save($image);
            return response()->json(['status'=>true,'id'=>$image->id]);
        }else if($request->hasFile('audio')){
            $path = 'uploads/recipe';
            $url = uploadImage($request,'audio',$path);
            $audio = new Image;
            $audio->url = $url;
            $audio->type = 'recipe_audio';
            $recipe->images()->save($audio);
            return response()->json(['status'=>true,'url'=>asset($url),'id'=>$audio->id]);
        }
        return errorMsgResponse('Please upload only image,video and audio');
    }

    public function deleteFile(Request $request){
        if($request->type=='recipe_image'){
            $image = Image::where(['id'=>$request->id,'type'=>$request->type])->first();
            if($image){
                $url = $image->url;
                $image->delete();
                deleteImage($url);
                return successMsgResponse('Image Deleted successfully');
            }else{
                return successMsgResponse('Record not found');
            }
        }else if($request->type=='recipe_video'){
            $image = Image::where(['id'=>$request->id,'type'=>$request->type])->first();
            if($image){
                $url = $image->url;
                $image->delete();
                if(strtolower($image->file_location)=='local'){
                    deleteImage($url);
                }
                return successMsgResponse('Video Deleted successfully');
            }else{
                return successMsgResponse('Record not found');
            }
        }else if($request->type=='recipe_audio'){
            $image = Image::where(['id'=>$request->id,'type'=>$request->type])->first();
            if($image){
                $url = $image->url;
                $image->delete();
                deleteImage($url);
                return successMsgResponse('Audio Deleted successfully');
            }else{
                return successMsgResponse('Record not found');
            }
        }
        return successMsgResponse('Record not found');
    }

    public function recipeDelete($id){
        $res = Recipe::find($id);
        $res->delete();

        $gallaryimg = Image::where(['id'=>$id,'type'=>'App\Models\Recipe'])->delete();
        return redirect()->back()->with('success', 'Action successfully.');
    }
}
