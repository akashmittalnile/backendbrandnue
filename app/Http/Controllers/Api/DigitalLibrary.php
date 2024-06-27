<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Instructional;
use Auth;

class DigitalLibrary extends Controller
{
    public $instructional;
    public function __construct(Instructional $instructional){
        $this->instructional = $instructional;
    }
    
    public function getInstructoinalVideos(Request $request){
        $user = Auth::user();
        $plan_id = $user->plan->subscription_plan_id??NULL;
        $videos = $this->instructional->apiGetInstructoinalVideos($request,$plan_id);
        return dataResponse($videos);
    }

    public function getInstructoinalVideoDetail(Request $request, Instructional $video){
        return dataResponse($video);
    }

    public function getInstructoinalTemplates(Request $request){
        $user = Auth::user();
        $plan_id = $user->plan->subscription_plan_id??NULL;
        $templates = $this->instructional->apiGetInstructoinalTemplates($request,$plan_id);
        return dataResponse($templates);
    }

    public function getInstructoinalTemplateDetail(Request $request, Instructional $template){
        return dataResponse($template);
    }

    public function getInstructoinalGuides(Request $request){
        $user = Auth::user();
        $plan_id = $user->plan->subscription_plan_id??NULL;
        $guides = $this->instructional->apiGetInstructoinalGuides($request,$plan_id);
        return dataResponse($guides);
    }

    public function getInstructoinalGuideDetail(Request $request,Instructional $guide){
        return dataResponse($guide);
    }
}
