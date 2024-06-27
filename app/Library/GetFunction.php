<?php
namespace App\Library;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Models\Tracking;
use App\User;
use Auth;
class GetFunction{

    public $tracking;

    public function __construct(Tracking $tracking){
        $this->tracking = $tracking;
    }

	public static function getRoleLayout(){
		if(Auth::user()->hasRole('ADMIN')){
            $data['layouts']    = 'layouts.admin.app';
            $data['guard']      = 'admin';
        }
        elseif(Auth::user()->hasRole('VENDOR')){
            $data['layouts']    = 'layouts.vendor.app';
            $data['guard']      = 'vendor';
        }elseif(Auth::user()->hasRole('SUB_ADMIN')){
            $data['layouts']    = 'layouts.admin.app';
            $data['guard']      = 'admin';
        }
        else{
            abort(401);
        }
        return $data;
	}

    

    public static function uploadImage($request,$file_name,$path){
        if(!File::exists(public_path($path))) File::makeDirectory(public_path($path), 0777,true);

        $file = $request->file($file_name);
        $ext = '.'.$file->getClientOriginalExtension();
        if(empty($edit_file_name)){
            $edit_file_name = str_replace($ext, time() . $ext, str_replace(" ","-",$file->getClientOriginalName()));
        }
        if($file->move(public_path($path),$edit_file_name)){
            return 'public/'.$path.'/'.$edit_file_name;
        }
        
        return NULL;
    }

    public function getTrackingChart($user_id,$year=''){ 
        $data = [];
        /*$month['jan']    = 0;
        $month['feb']    = 0;
        $month['mar']    = 0;
        $month['apr']    = 0;
        $month['may']    = 0;
        $month['jun']    = 0;
        $month['jul']    = 0;
        $month['aug']    = 0;
        $month['sep']    = 0;
        $month['oct']    = 0;
        $month['nov']    = 0;
        $month['dec']    = 0;*/
        $month= [];
        $data['labels']  = array_keys($month);
        DB::statement("SET SQL_MODE=false");
        $trackings = $this->tracking->where('user_id',$user_id)->selectRaw('year(track_date) as track_year,month(track_date) as track_month, avg(current_day_weight) as current_day_weight');
        $trackings->groupBy('track_month');
        if(!empty($year)){
            $trackings = $trackings->having('track_year',$year);
        }else{
            $trackings = $trackings->having('track_year',date('Y'));

        }
        $trackings = $trackings->get();
        if($trackings->count()){
            foreach($trackings as $row){
                $year_month = strtotime($row->track_year.'-'.$row->track_month);
                $month_name = strtolower(date('M',$year_month));
                $month[$month_name] = (float)sprintf('%0.2f',$row->current_day_weight);
                array_push($data['labels'],$month_name);
            }
        }
        $data['datasets'] = array_values($month);
        return $data;
    }

    public function getTrackingChartByWeekly($user_id){ 
        $data = [];
        $data['labels']  = [];
        $days = [];
       
        $trackings = $this->tracking->where('user_id',$user_id)->select('current_day_weight','track_date');
        $trackings->orderBy('track_date','desc')->limit(7);
        $trackings = $trackings->get();
        
        if($trackings->count()){
            $trackings = $trackings->toArray();
            $collections  = collect($trackings)->sortBy('track_date');
            foreach($collections as $row){
                $month_name = date('M-d',strtotime($row['track_date']));
                $weight = (float)sprintf('%0.2f',$row['current_day_weight']);
                array_push($days,$weight);
                array_push($data['labels'],$month_name);
            }
        }
        $data['datasets'] = array_values($days);
        return $data;
    }

    public function getTrackingChartByMonthly($user_id){ 
        $data = [];
        $data['labels']  = [];
        $days = [];
        $trackings = $this->tracking->where('user_id',$user_id)->select('current_day_weight','track_date');
        $trackings->whereYear('track_date',date('Y'))->whereDate('track_date','<=',date("Y-m-d"))->whereDate('track_date','>=',date('Y-m-d',strtotime('-30 days')));
        $trackings = $trackings->orderBy('track_date')->get();
        if($trackings->count()){
            $a_weight = '';
            foreach($trackings as $row){
                if(empty($a_weight)){
                    $a_weight = $row->current_day_weight;
                }
            }

            foreach($trackings as $row){
                $month_name = date('M-d',strtotime($row->track_date));
                if(!empty($row->current_day_weight)){
                    $weight = (float)sprintf('%0.2f',$row->current_day_weight);
                } else {
                    $weight = (float)sprintf('%0.2f',$a_weight);
                }
                array_push($days,$weight);
                array_push($data['labels'],$month_name);
            }
        }
        $data['datasets'] = array_values($days);
        return $data;
    }

    public function getTrackingChartByYearly($user_id){ 
        $data = [];
        $data['labels']  = [];
        $years = [];
        DB::statement("SET SQL_MODE=false");
        $trackings = $this->tracking->where('user_id',$user_id)->selectRaw('year(track_date) as track_year, avg(current_day_weight) as current_day_weight');
        $trackings->groupBy('track_year');
        
        $trackings = $trackings->orderBy('track_year')->get();
        if($trackings->count()){
            foreach($trackings as $row){
                $y = $row->track_year;
                $weight = $row->current_day_weight;
                array_push($years,(float)sprintf('%0.2f', $weight));
                array_push($data['labels'],$y);
            }
        }
        $data['datasets'] = array_values($years);
        return $data;
    }
 
    public function getAchievementLevel($user){
        
        $initial_weight     = $user->current_weight??0;
        $goal_weight        = $user->goal_weight;
        // dd([$initial_weight, $goal_weight]);
        $diff_initial_goal  = $initial_weight - $goal_weight;
        $one_level_value    = ($diff_initial_goal / config('constant.achievement.level'));
        $data = [];
        $data['percentage'] = 0;
        $data['percentage_level'] = "Let's Start!";
        $data['next_achievement_percentage'] = 0;
        $data['next_achievement_weight'] = $one_level_value;
        $data['data'] = [];
        $tracking = $this->tracking->where('user_id',$user->id)->whereNotNull('current_day_weight')->whereDate('track_date','<=',date('Y-m-d'))->orderBy('track_date','desc')->first();
        if($tracking){
            $current_day_till_weight_loss = $initial_weight - $tracking->current_day_weight;
            $current_day_till_weight_loss = ($current_day_till_weight_loss>=0) ? $current_day_till_weight_loss : 0;
            //$percentage = (($current_day_till_weight_loss/$one_level_value) * 20); // previous
            $percentage = (($current_day_till_weight_loss/$one_level_value) * 100);
            $one_level_value_up = $one_level_value;
            $data['next_achievement_percentage'] =  floor(($percentage>=0)?$percentage:0);
            $data['percentage'] = ($data['percentage'] > 100)? 100 : $data['percentage'];
            $data['next_achievement_weight'] = number_format($one_level_value-$current_day_till_weight_loss,1);
            
            for ($i=1; $i <= config('constant.achievement.level'); $i++) { 
                $temp = [];
                $temp_i = $i+1;
                if(floor($current_day_till_weight_loss/$one_level_value)>=$i){
                    $one_level_value_up = $one_level_value_up+$one_level_value;
                    $one_level_value_down = $one_level_value_up-$one_level_value;
                    $temp = config('constant.achievement.data')[$i];
                    $temp['status'] = true;
                    $data['next_achievement_percentage'] = number_format(((($current_day_till_weight_loss-$one_level_value_down)/$one_level_value)*100),1);
                    $data['next_achievement_weight'] = number_format($one_level_value_up-$current_day_till_weight_loss,1);
                    $data['percentage_level'] = config('constant.achievement.data')[$i]['title'];
                    $data['percentage'] = $data['percentage']+ 20;
                }else{
                    $temp = config('constant.achievement.data')[$i];
                    $temp['status'] = false;
                }
                array_push($data['data'], $temp);
            }
        }else{
            for ($i=1; $i <= config('constant.achievement.level'); $i++) { 
                $temp = config('constant.achievement.data')[$i];
                $temp['status'] = false;
                array_push($data['data'], $temp);
            }
        }
        $data['data'] = array_reverse($data['data']);
        return $data;
    }

    public function getLowToHighAchievementLevel($user){
        $initial_weight     = $user->current_weight; // 45
        $goal_weight        = $user->goal_weight; // 80
        $diff_initial_goal  = $goal_weight - $initial_weight; // 35
        $one_level_value    = ($diff_initial_goal / config('constant.achievement.level')); // 7
        $data = [];
        $data['percentage'] = 0;
        $data['percentage_level'] = "Let's Start!";
        $data['next_achievement_percentage'] = 20;
        $data['next_achievement_weight'] = number_format($initial_weight + $one_level_value,1); // 52
        $data['data'] = [];
        $tracking = $this->tracking->where('user_id',$user->id)->whereNotNull('current_day_weight')->whereDate('track_date','<',date('Y-m-d'))->orderBy('track_date','desc')->limit(1)->first();
        if($tracking){
            $current_day_weight = $tracking->current_day_weight - $initial_weight ; //55 - 45 = 10
            
            $percentage = (($current_day_weight/$one_level_value) * 20); // 28

            $data['percentage'] = floor(($percentage>=0)?$percentage:0); // 28
            $data['percentage'] = ($data['percentage'] > 100)? 100 : $data['percentage'];

            for ($i=1; $i <= config('constant.achievement.level'); $i++) { 
                $temp = [];
                
                if(floor($current_day_weight/$one_level_value)>=$i){ // 1.42
                    $temp = config('constant.achievement.data')[$i];
                    $temp['status'] = true;
                    $data['next_achievement_percentage'] = $data['next_achievement_percentage'] + 20;
                    $data['next_achievement_weight'] = number_format($data['next_achievement_weight'] + $one_level_value,1);
                    $data['percentage_level'] = config('constant.achievement.data')[$i]['title'];
                }else{
                    $temp = config('constant.achievement.data')[$i];
                    $temp['status'] = false;
                }
                array_push($data['data'], $temp);
            }
        }else{
            for ($i=1; $i <= config('constant.achievement.level'); $i++) { 
                $temp = config('constant.achievement.data')[$i];
                $temp['status'] = false;
                array_push($data['data'], $temp);
            }
        }
        $data['data'] = array_reverse($data['data']);
        return $data;
    }

    public function getCurrentMonthFastTimeHours($user_id){
        DB::statement("SET SQL_MODE=false");
        $trackings = $this->tracking->where('user_id',$user_id)->whereNotNull('total_fast_hour')->whereMonth('track_date',date('m'))->selectRaw('avg(total_fast_hour) as total_fast_hour,month(track_date) as track_month');
        $result = $trackings->groupBy('track_month')->first();
        // dd($result);
        if(!empty($result) && !empty($result->total_fast_hour)){
            return (float)sprintf('%0.1f',$result->total_fast_hour);
            // $total_fast_hour = (int) $result->total_fast_hour;
            // return date('H.i', mktime(0,$total_fast_hour));
        }

        return 0 ;
    }

    public static function chatTotal($id){
        $data = \App\Models\ChatCount::sum('admin_total');
        return $data;
    }

    public static function eliteMemberRequestTotal(){
        $data = \App\Models\EliteMembershipRequest::sum('read_status');
        return $data;
    }

    public static function digitalLibraryTotal(){
        $recipe_total = \App\Models\Recipe::where('status',config('constant.status.active'))->count();
        $instructional_total = \App\Models\Instructional::where('status',config('constant.status.active'))->count();
        $total = $recipe_total + $instructional_total;
        return $total;
    }

    public function getUserTrackingForAdmin($user_id){
        $data = [];
        $user = User::where('id',$user_id)->first();
        $is_low_to_high = false;

        if($user){
            if($user->goal_weight>$user->current_weight){
                $data['weight_loss_achievement_text'] = 'Weight Gain Achievement';
                $data['weight_loss_to_date_text'] = 'Weight gain to date';
                $is_low_to_high = true;
                $goal_result = $this->getLowToHighAchievementLevel($user) ;
            }else{
                $data['weight_loss_to_date_text'] = 'Weight loss to date';
                $data['weight_loss_achievement_text'] = 'Weight Loss Achievement';
                $goal_result = $this->getAchievementLevel($user);
            }
        }
        $weight_loss_to_date = 0;
        $tracking = $this->tracking->where('user_id',$user->id)->whereNotNull('current_day_weight')->orderBy('track_date','desc')->first();
        
        if($tracking && $user && !empty($tracking->current_day_weight)){

            if($is_low_to_high==true){
                $weight_loss_to_date = $tracking->current_day_weight - $user->current_weight;
            }else{
                $weight_loss_to_date = $user->current_weight - $tracking->current_day_weight;
            }
            
        }
        $data['starting_weight_pounds'] = $user->current_weight??0;
        $data['starting_weight_ounce']  = getOunce($user->current_weight??0);
        $data['fast_time_current_month_hours']  = $this->getCurrentMonthFastTimeHours($user_id);
        $data['weight_loss_to_date'] = $weight_loss_to_date;
        $data['next_achievement_percentage'] = $goal_result['next_achievement_percentage']??0;
        $data['percentage'] = $goal_result['percentage']??0;
        return $data;
    }

    public static function getTotalAmountBySubscription($id,$price){ // individual subscription
        $count = \App\Models\SubscribeMemberTransaction::where('subscribe_member_id')->count();
        $total = ($count*$price);
        return priceFormat($total);
    }

    public static function getLoginDetails($user){
        $obj =  new User;
        $plan = $obj->getActivePlan($user->id);
        $elite_member_id = config('constant.elite_member_id');
        $elite_member_request = \App\Models\SubscribeMember::where(['user_id'=>$user->id,'activated_from'=>'Admin','subscription_plan_id'=>$elite_member_id])->count();
        $user->elite_member_request = $elite_member_request;

        if($user->hasRole('ADMIN')){
            $user->role = config('constant.role.admin');
        } else {
            $user->role = config('constant.role.customer');
        }
        
        $user->plan = [] ;
        if($plan){
            $user->plan = [
                'id'=>$plan->id,
                'name'=>$plan->name,
                'price'=>$plan->price??"Free",
                'square_payment_subscription_id'=>$plan->square_payment_subscription_id,
                'elite_member_id' => $elite_member_id,
                'device'=>$plan->device,
            ];
        }
        return $user;
    }
 
    public static function isPremium($user){
        $is_active = \App\Models\SubscribeMember::where(['user_id'=>$user->id??0,'status'=>'Active'])->whereIn('subscription_plan_id',[config('constant.premium_member_id'),config('constant.yearly_premium_member_id')])->first();
        if($is_active){
            return true;
        }
        return false;
    }

    public static function isElite($user){
        $is_active = \App\Models\SubscribeMember::where(['user_id'=>$user->id??0,'status'=>'Active'])->whereIn('subscription_plan_id',[config('constant.elite_member_id'),config('constant.yearly_elite_member_id')])->first();
        if($is_active){
            return true;
        }
        return false;
    }
    
}
