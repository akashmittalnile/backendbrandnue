<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use App\Models\Supplement;
use App\Models\Exercise;
use App\Models\Recipe;
use Auth;
class CommonController extends Controller
{ 
    public $supplement;
    public $exercise;
    public function __construct(Supplement $supplement,Exercise $exercise){
        $this->supplement = $supplement;
        $this->exercise = $exercise;
    }
    
    public function getSupplementList(Request $request){
        $supplements = $this->supplement->where('status',config('constant.status.active'))->select('id','name')->get();
        return dataResponse($supplements);
    }

    public function getExerciseList(Request $request){
        $exercises = $this->exercise->where('status',config('constant.status.active'))->select('id','name')->get();
        return dataResponse($exercises);
    }

    public function getSearchMealList(Request $request){
        $recipe = new Recipe;
        $user = Auth::user();
        if(isPremium($user)){
            $list = $recipe->getSearchMealList($request,[config('constant.status.active'),config('constant.status.in_active')]);
            return dataResponse($list);    
        }
        $list = $recipe->getSearchMealList($request);
        return dataResponse($list);
    }

    public function pushNo(){
        // $crewNtf = [
        //         'to'=>"d-qsNLp9REeMnENsfEdBCy:APA91bGZ2OJNr5jQhHs8gBzgteF6udaCN-sVCrHLNz-HGRUZGMCSY1IWIPbQs3NnLeJXvF_KBDK0MDMxQ0wuDPGZLjw0dshCe7gO4A9KXK7_pLRl6CwxxP1se5i4TLUZ_ssPUd576I8q",
        //         'notification'=>[
        //             'title'=>"Job paused",
        //             'body'=>'Job paused date ',
        //             'mutable_content'=>false,
        //             'sound'=>'Tri-tone'
        //         ],                    
                
        //     ];

        $crewNtf = [
            'to'=>"d-qsNLp9REeMnENsfEdBCy:APA91bGZ2OJNr5jQhHs8gBzgteF6udaCN-sVCrHLNz-HGRUZGMCSY1IWIPbQs3NnLeJXvF_KBDK0MDMxQ0wuDPGZLjw0dshCe7gO4A9KXK7_pLRl6CwxxP1se5i4TLUZ_ssPUd576I8q",
            'notification'=>[
                'title'=>"Job paused",
                'body'=>'Job paused date ',
                'mutable_content'=>false,
                'sound'=>'Tri-tone'
            ],                    
            
        ];
        
        $result = pushNotification($crewNtf);
        return dataResponse($result);
    }

    public function subscriptionPlans(){
        $plan = new \App\Models\SubscriptionPlan;
        $list = $plan->getPlan();
        $plans = [];
        if($list->count()){
            foreach($list as $row){
                $description = str_replace('</p>', '', $row->description);
                $description = explode('<p>', $description);
                $temp = [];
                if(count($description)){
                    foreach($description as $desc){

                        if(!empty($desc)){
                            array_push($temp,htmlspecialchars_decode(strip_tags($desc)));
                        }
                    }
                }
                $row->description = $temp;
                array_push($plans,$row);
            }
        }
        return dataResponse($plans);
    }

    public function getPaymentUrl(SubscriptionPlan $plan_id){
        return response()->json(['status'=>true,'url'=>route('api-web-view',$plan_id)]);
    }
}
