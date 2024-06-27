<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Instructional;
use App\Models\DigitalLibrary;
use App\Models\SubscriptionPlan;
use Validator;
use Session;

class DigitalLibraryController extends Controller
{
    public $instructional;
    public function __construct(Instructional $instructional){
        $this->middleware('auth');
        $this->instructional = $instructional;
    }

    public function digitalLibrary(){
        $digitals = DigitalLibrary::where('status',config('constant.status.active'))->get();
        
        $collection = collect($digitals);

        $recipe = $collection->filter(function($item,$key){
            return $item->id ==1;
        })->toArray();
        
        $recipe = array_shift($recipe);

        $guide = $collection->filter(function($item,$key){
            return $item->id ==2;
        })->toArray();
        
        $guide = array_shift($guide);


        $template = $collection->filter(function($item,$key){
            return $item->id ==3;
        })->toArray();
        
        $template = array_shift($template);


        $video = $collection->filter(function($item,$key){
            return $item->id ==4;
        })->toArray();
        
        $video = array_shift($video);

        return view('admin.digital.library',compact('recipe','guide','template','video'));
    }

    public function digitalLibraryEdit(DigitalLibrary $id){
        $data = $id;
        return view('admin.digital.edit-library',compact('data'));
    }

    public function digitalLibraryUpdate(Request $request,DigitalLibrary $id){
        $this->validate($request,[
            'title'=>'required|max:191',
            'description'=>'max:5000',
        ]);

        $id->title = $request->title;
        $id->description = $request->description;
        if($request->hasFile('image')){
            $path = 'uploads/digital';
            $id->image = uploadImage($request,'image',$path);
        }
        if($id->title!=$request->title){
            $id->slug = generateSlug($request->title, $id);
        }
        $id->save();
        Session::flash('success',$request->title.' data updated successfully');
        return redirect(route('admin.digital.library'));

    }
    public function digitalLibraryDeleteImage(DigitalLibrary $id){
        $image = $id->image;
        $id->image = '';
        $id->save();
        deleteImage($image);
        return successMsgResponse('');
    }

    /*Start instructional Templates*/
    public function instructionalTemplates(Request $request){
        $templates = $this->instructional->getInstructoinalTemplates($request,false);

        return view('admin.digital.template.list',compact('templates'));
    }

    public function createInstructionalTemplate(){
        //$plans = SubscriptionPlan::where('status',config('constant.status.active'))->get();
        return view('admin.digital.template.create');
    } 

    public function storeInstructionalTemplate(Request $request){
        $this->validate($request,[
            'title'=>'required|max:191',
            'description'=>'max:5000',
            'status'=>'required',
        ]);

        $instructional = new $this->instructional;
        $instructional->title = $request->title;
        $instructional->description = $request->description;
        $instructional->status = $request->status;
        $instructional->subscription_type = $request->plan;

        $instructional->type = 'template';
        if($request->hasFile('file')){
            $path = 'uploads/instructional/templates';
            $instructional->url = uploadImage($request,'file',$path);
        }
        $instructional->save();
        Session::flash('success','Instructional template created successfully');
        return redirect(route('admin.instructional.template'));
    }

    public function editInstructionalTemplate(Instructional $template){
        if($template->type!=='template'){
            abort(404);
        }
        //$plans = SubscriptionPlan::where('status',config('constant.status.active'))->get();
        return view('admin.digital.template.edit',compact('template'));
    }

    public function updateInstructionalTemplate(Request $request, Instructional $template){
        $this->validate($request,[
            'title'=>'required|max:191',
            'description'=>'max:5000',
            'status'=>'required',
        ]);
        
        $template->title = $request->title;
        $template->description = $request->description;
        $template->status = $request->status;
        $template->subscription_type = $request->plan;
        $template->type = 'template';
        if($request->hasFile('file')){
            $image = $template->url;
            $path = 'uploads/instructional/templates';
            $template->url = uploadImage($request,'file',$path);
            deleteImage($image);
        }
        $template->save();
        Session::flash('success','Instructional template updated successfully');
        return redirect(route('admin.instructional.template'));
    }

    public function showInstructionalTemplate(Instructional $template){
        return view('admin.digital.template.show',compact('template'));
    }

    public function deleteInstructionalTemplate(Instructional $template){
        if($template->type=='template'){
            $image = $template->url;
            $template->delete();
            deleteImage($image);
            Session::flash('success','Instructional template deleted successfully');
            return successMsgResponse('Instructional template deleted successfully');
        }
        return errorMsgResponse('Instructional template not found in system');
    }

    /*End instructional Templates*/

    /*Start instructional Guides*/
    public function instructionalGuides(Request $request){
        $guides = $this->instructional->getInstructoinalGuides($request,false);
        return view('admin.digital.guide.list',compact('guides'));
    }

    public function createInstructionalGuide(){
        //$plans = SubscriptionPlan::where('status',config('constant.status.active'))->get();
        return view('admin.digital.guide.create');
    }

    public function storeInstructionalGuide(Request $request){
        $this->validate($request,[
            'title'=>'required|max:191',
            'description'=>'max:5000',
            'status'=>'required',
        ]);

        $instructional = new $this->instructional;
        $instructional->title = $request->title;
        $instructional->description = $request->description;
        $instructional->status = $request->status;
        $instructional->subscription_type = $request->plan;
        $instructional->type = 'guide';
        if($request->hasFile('file')){
            $path = 'uploads/instructional/guides';
            $instructional->url = uploadImage($request,'file',$path);
        }
        $instructional->save();
        Session::flash('success','Instructional guide created successfully');
        return redirect(route('admin.instructional.guide'));
    }

    public function editInstructionalGuide(Instructional $guide){
        if($guide->type!=='guide'){
            abort(404);
        }
        $plans = SubscriptionPlan::where('status',config('constant.status.active'))->get();
        return view('admin.digital.guide.edit',compact('guide','plans'));
    }

    public function updateInstructionalGuide(Request $request, Instructional $guide){
        $this->validate($request,[
            'title'=>'required|max:191',
            'description'=>'max:5000',
            'status'=>'required',
        ]);
        
        $guide->title = $request->title;
        $guide->description = $request->description;
        $guide->status = $request->status;
        $guide->subscription_type = $request->plan;
        if($request->hasFile('file')){
            $image = $guide->url;
            $path = 'uploads/instructional/guides';
            $guide->url = uploadImage($request,'file',$path);
            deleteImage($image);
        }
        $guide->save();
        Session::flash('success','Instructional guide updated successfully');
        return redirect(route('admin.instructional.guide'));
    }

    public function showInstructionalGuide(Instructional $guide){
        return view('admin.digital.guide.show',compact('guide'));
    }

    public function deleteInstructionalGuide(Instructional $guide){
        if($guide->type=='guide'){
            $image = $guide->url;
            $guide->delete();
            deleteImage($image);
            Session::flash('success','Instructional guide deleted successfully');
            return successMsgResponse('Instructional guide deleted successfully');
        }
        return errorMsgResponse('Instructional guide not found in system');
    }

    /*End instructional Guides*/

    /*Start instructional Videos*/
    public function instructionalVideos(Request $request){
        $videos = $this->instructional->getInstructoinalVideos($request,false);
        return view('admin.digital.video.list',compact('videos'));
    }

    public function createInstructionalVideo(){
        $plans = SubscriptionPlan::where('status',config('constant.status.active'))->get();
        return view('admin.digital.video.create',compact('plans'));
    }

    public function storeInstructionalVideo(Request $request){
        $rules = [
            'title'=>'required|max:191',
            'description'=>'max:5000',
            'status'=>'required',
        ];
        if($request->ajax()){
            $validation = Validator::make($request->all(),$rules);
            if($validation->fails()){
                return errorMsgResponse($validation->errors()->first());
            }
        }else{
            $this->validate($request,$rules);
        }
        
        $instructional = new $this->instructional;
        $instructional->title = $request->title;
        $instructional->description = $request->description;
        $instructional->status = $request->status;
        $instructional->subscription_type = $request->plan;
        $instructional->type = 'video';
        
        if(isset($request->path) && !empty($request->path)){
            $instructional->url = $request->path ?? '';
        }
        /*if($request->hasFile('file')){
            $path = 'uploads/instructional/guides';
            $instructional->url = uploadImage($request,'file',$path);
        }*/

        if($request->has('embeded_video_url') && $request->filled('embeded_video_url')){
            $instructional->location_type = $request->video_type;
            $instructional->url = $request->embeded_video_url ?? '';
        }

        $instructional->save();
        Session::flash('success','Instructional video created successfully');
        if($request->ajax()){
            return response()->json(['status'=>true,'url'=>route('admin.instructional.video')]);
        }else{
            return redirect(route('admin.instructional.video'));
        }
    }

    public function editInstructionalVideo(Instructional $video){
        if($video->type!=='video'){
            abort(404);
        }
        $plans = SubscriptionPlan::where('status',config('constant.status.active'))->get();
        return view('admin.digital.video.edit',compact('video','plans'));
    }

    public function updateInstructionalVideo(Request $request, Instructional $video){
        $this->validate($request,[
            'title'=>'required|max:191',
            'description'=>'max:5000',
            'status'=>'required',
        ]);
        
        $video->title = $request->title;
        $video->description = $request->description;
        $video->status = $request->status;
        $video->subscription_type = $request->plan;
        
        if(isset($request->image) && !empty($request->image)){
            $video->url = $request->image??'';
        }

        // if($request->hasFile('file')){
        //     $image = $video->url;
        //     $path = 'uploads/instructional/videos';
        //     $video->url = uploadImage($request,'file',$path);
        //     deleteImage($image);
        // }

        if($request->has('embeded_video_url') && $request->filled('embeded_video_url')){
            $video->location_type = $request->video_type;
            $video->url = $request->embeded_video_url ?? '';
        }

        $video->save();
        Session::flash('success','Instructional video updated successfully');
        return redirect(route('admin.instructional.video'));
    }

    public function showInstructionalVideo(Instructional $video){
        return view('admin.digital.video.show',compact('video'));
    }

    public function deleteInstructionalVideo(Instructional $video){
        if($video->type=='video'){
            $image = $video->url;
            $video->delete();
            deleteImage($image);
            Session::flash('success','Instructional video deleted successfully');
            return successMsgResponse('Instructional video deleted successfully');
        }
        return errorMsgResponse('Instructional video not found in system');
    }

    public function deleteInstructionalVideoOnly(Instructional $video){
        if($video->type=='video'){
            $image = $video->url;
            $video->url = '';
            $video->save();
            deleteImage($image);
            Session::flash('success','Instructional video deleted successfully');
            return successMsgResponse('Instructional video deleted successfully');
        }
        return errorMsgResponse('Instructional video not found in system');
    }


    
    /*End instructional Videos*/
}
