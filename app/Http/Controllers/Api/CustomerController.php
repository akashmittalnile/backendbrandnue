<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use App\Models\PushNotification;
use App\Models\FavouriteRecipe;
use Illuminate\Http\Request;
use App\Library\GetFunction;
use Illuminate\Support\Arr;
use App\Models\Tracking;
use App\Models\EliteMembershipRequest;

use App\User;
use Validator;
use Auth;
use DB;

class CustomerController extends Controller
{
    public $getFunction;
    public $tracking;

    public function __construct(Tracking $tracking,GetFunction $getFunction){
        $this->tracking     = $tracking;
        $this->getFunction  = $getFunction;
    }

    public function getHome(){
        $data = [];
        $user = Auth::user();
        $data['user_details'] = User::select('first_name','last_name','profile_image')->where(array('id'=>Auth::user()->id))->first();
        $one_day_tracking           = $this->tracking->where('user_id',$user->id)->whereNotNull('current_day_weight')->orderBy('track_date','desc')->first();
        $data['initial_weight']     = $user->current_weight;
        $data['goal_weight']        = $user->goal_weight;
        $data['current_year_weight_diary']  = $this->getFunction->getTrackingChartByMonthly($user->id);
        $is_low_to_high = false;
        $current_loss_weight = 0;
        if($data['goal_weight']>$data['initial_weight']){
            $is_low_to_high = true;
            $data['current_achievement_level']  = $this->getFunction->getLowToHighAchievementLevel($user);
            $data['current_achievement_level_text'] = 'Weight gain Achievement';
            $data['current_loss_weight_text'] = 'Weight gain to date';
        }else{

            $data['current_achievement_level']  = $this->getFunction->getAchievementLevel($user);
            $data['current_achievement_level_text'] = 'Weight loss Achievement';
            $data['current_loss_weight_text'] = 'Weight loss to date';
        }


        if(!empty($one_day_tracking)){
            if($is_low_to_high==true){

                $current_loss_weight = number_format($one_day_tracking->current_day_weight-$user->current_weight,1);
            }else{
                $current_loss_weight = number_format($user->current_weight - $one_day_tracking->current_day_weight,1) ;
                $current_loss_weight = ($current_loss_weight>=0)? $current_loss_weight : 0;
            } 

            $data['fast_time_current_month_hours']    = $this->getFunction->getCurrentMonthFastTimeHours($user->id);
            $data['current_date']       = $one_day_tracking->track_date??date('Y-m-d');
            $data['current_weight']     = $one_day_tracking->current_day_weight??0;
            $data['current_loss_weight']= $current_loss_weight;
                        
        }else{
            $data['fast_time_hours']    = 0;
            $data['current_date']       = date('Y-m-d');
            $data['current_weight']     = 0;
            $data['current_loss_weight']= 0;
           
        }
        
        return dataResponse($data);
    }

    public function profileUpdate(Request $request){
        $user = Auth::user();
        $validation = Validator::make($request->all(),[
            'first_name'=>'required|max:191',
            'last_name'=>'required|max:191',
            'email'=>'required|max:191|unique:users,email,'.$user->id,
            'phone'=>'required',
            'gender'=>'required|max:10',
            'dob'=>'required',
            'height_feet'=>'required',
            'waist_measurement'=>'required',
            'goal_waist_measurement'=>'required',
            'start_weight'=>'required',
            'goal_weight'=>'required',
        ]);

        if($validation->fails()){
            return errorMsgResponse($validation->errors()->first());
        }
        DB::beginTransaction();
        try {
            $user->name = $request->first_name.' '.$request->last_name;
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->gender = $request->gender;
            $user->dob = dbDateFormat($request->dob);
            $user->height_feet = $request->height_feet;
            $user->height_inch = $request->height_inch;
            $user->waist_measurement = $request->waist_measurement;
            $user->goal_waist_measurement = $request->goal_waist_measurement;
            $user->today_waist_measurement = $request->today_waist_measurement;
            $user->current_weight = $request->start_weight;
            $user->today_current_weight = $request->current_weight;
            $user->goal_weight = $request->goal_weight;
            if($request->hasFile('profile_image')){
                $image = $user->profile_image;
                $path = 'uploads/profile';
                $user->profile_image = uploadImage($request,'profile_image',$path);
                if(!empty($image)){

                    deleteImage($image);
                }
            }
            
            $user->save();
            $obj =  new User;
            $plan = $obj->getActivePlan($user->id);
            $elite_member_id = config('constant.elite_member_id');
            $elite_member_request = \App\Models\SubscribeMember::where(['user_id'=>$user->id,'activated_from'=>'Admin','subscription_plan_id'=>$elite_member_id])->count();

            $user->elite_member_request = $elite_member_request;
            $user->role = config('constant.role.customer');
            if($user->hasRole(config('constant.role.admin'))){
                $user->role = config('constant.role.admin');
            }
            $user->plan = [] ;
            if($plan){
                $user->plan = [
                    'id'=>$plan->id,
                    'name'=>$plan->name,
                    'price'=>$plan->price,
                    'square_payment_subscription_id'=>$plan->square_payment_subscription_id,
                    'elite_member_id' => $elite_member_id
                ];
            }
            
            DB::commit();
            return dataResponse($user);
        } catch (\Exception $e) {
            DB::rollback();
            return errorMsgResponse($e->getMessage());
        }
    }


    public function getCurrentProfile(Type $var = null){
        $user = Auth::user();
        $data = getLoginDetails($user);
        $elite = EliteMembershipRequest::where('user_id', $user->id)->get();
        $elite_request = EliteMembershipRequest::where('user_id', $user->id)
                        ->where('status', 'requested')
                        ->get();
        $data['elite_member_request_status'] = false;
        $data['elite_request_status'] = false;
        if (count($elite) > 0) {
            $data['elite_member_request_status'] = true;
        }
        if (count($elite_request) > 0) {
            $data['elite_request_status'] = true;
        }
        return dataResponse($data);
    }

    public function dailyTrakingStore(Request $request){
        $validation = Validator::make($request->all(),[
            'track_date'=>'required',
            'current_day_weight'=>'required',
            'water_intake'=>'required',
            'bowel_movement'=>'required',
            'lunch'=>'required',
            'dinner'=>'required',
            
        ]);

        if($validation->fails()){
            return errorMsgResponse($validation->errors()->first());
        }
        $user = Auth::user();
        $date = dbDateFormat($request->track_date);

        $tracking = $this->tracking->where(['user_id'=>$user->id,'track_date'=>$date])->first();
        if($tracking){
            return errorMsgResponse($request->track_date.' data already been submitted. So please update or ignore this.');
            // $tracking = new $this->tracking;
        }

        $tracking = new $this->tracking;
        $all_data = $request->all();

        DB::beginTransaction();
        try { 
            $fast_start_time = null;
            $fast_end_time = null;

            // launch check time...
            if(isset($all_data['lunch']['start_time']['hh']) && !empty($all_data['lunch']['start_time']['hh'])){
                $l_time = $all_data['lunch']['start_time']['hh'].":".$all_data['lunch']['start_time']['mm']." ".$all_data['lunch']['start_time']['ext'];
                $lunch_start_time = convertToTime($l_time);
                if(!empty($fast_start_time) && $fast_start_time>$lunch_start_time){
                    $fast_start_time = $lunch_start_time;
                }else if(empty($fast_start_time)){
                    $fast_start_time = $lunch_start_time;
                    if(empty($fast_end_time)){
                        $fast_end_time = $lunch_start_time;
                    }
                }
            }

            // dinner check time...
            if(isset($all_data['dinner']['end_time']['hh']) && !empty($all_data['dinner']['end_time']['hh'])){
                $d_time = $all_data['dinner']['end_time']['hh'].":".$all_data['dinner']['end_time']['mm']." ".$all_data['dinner']['end_time']['ext'];
                $dinner_end_time = convertToTime($d_time);
                if(!empty($fast_end_time) && $fast_end_time<$dinner_end_time){
                    $fast_end_time = $dinner_end_time;
                }else if(empty($fast_end_time)){
                    $fast_end_time = $dinner_end_time;
                    if(empty($fast_start_time)){
                        $fast_start_time = $dinner_end_time;
                    }
                }
            }

            // snack check...
            if((!empty($request->snack))){
                foreach($request->snack as $snack){
                    if(isset($snack['start_time']['hh']) && !empty($snack['start_time']['hh']) && isset($snack['start_time']['mm']) && isset($snack['start_time']['ext'])){
                       $snack_time = convertToTime($snack['start_time']['hh'].":".$snack['start_time']['mm']." ".$snack['start_time']['ext']);
                        if(!empty($fast_start_time) && $snack_time<$fast_start_time){
                            $fast_start_time = $snack_time;
                        }else if(!empty($snack_time) && empty($fast_start_time)){
                            $fast_start_time = $snack_time;
                        }

                        if(!empty($fast_end_time) && $snack_time>$fast_end_time){
                            $fast_end_time = $snack_time;
                        }else if(!empty($snack_time) && empty($fast_end_time)){
                            $fast_end_time = $snack_time;
                        }
                    }
                }
            }   
            
            $tracking->user_id = $user->id;
            $tracking->track_date = $date;
            $tracking->current_day_weight = $request->current_day_weight;
            $tracking->current_day_waist_measurement = $request->current_day_waist_measurement;
            $tracking->water_intake = $request->water_intake;
            $tracking->supplement = encryptDecrypt('encrypt', serialize($request->supplement));
            $tracking->bowel_movement = $request->bowel_movement;
            $tracking->exercise = (!empty($request->exercise)) ? encryptDecrypt('encrypt', serialize($request->exercise)):NULL;
            //$tracking->breakfast = encryptDecrypt('encrypt', serialize($request->breakfast));
            $tracking->lunch = encryptDecrypt('encrypt', serialize($request->lunch));
            $tracking->snack = encryptDecrypt('encrypt', serialize($request->snack));
            $tracking->dinner = encryptDecrypt('encrypt', serialize($request->dinner));
            $tracking->total_exercise_duration = encryptDecrypt('encrypt', serialize($request->total_exercise_duration));
            $tracking->note = $request->note ?? '';
            
            if(!empty($fast_start_time) && !empty($fast_end_time)){
                $tracking->fast_start_time = $fast_start_time;
                $tracking->fast_end_time = $fast_end_time;

                $previous_tracking = $this->tracking->where(['user_id'=>$user->id])->whereDate('track_date',date('Y-m-d',strtotime('- 1 day'.$date)))->select('fast_start_time','fast_end_time','track_date')->whereNull('reset_type')->orderBy('track_date','desc')->first();
                if(!empty($previous_tracking)){
                    $fet = str_replace(" ",'',$previous_tracking->fast_end_time);
                    $fet = str_replace("am",'',strtolower($fet));
                    $fet = str_replace("pm",'',strtolower($fet));
                    $startdate  = date('Y-m-d H:i:s',strtotime($previous_tracking->track_date.' '.$fet));
                    $enddate    = date('Y-m-d H:i:s',strtotime(dbDateFormat($request->track_date).' '.$fast_start_time));
                    $tracking->total_fast_hour = (float) sprintf('%0.2f',differenceInHours($startdate,$enddate));
                }else{
                    $previous_of_previous_tracking = $this->tracking->where(['user_id'=>$user->id])->whereDate('track_date','<',$tracking->track_date)->select('total_fast_hour','fast_end_time')->whereNull('reset_type')->orderBy('track_date','desc')->first();
                    if($previous_of_previous_tracking && !empty($previous_of_previous_tracking->fast_end_time)){
                        $fet = str_replace(" ",'',$previous_of_previous_tracking->fast_end_time);
                        $fet = str_replace("am",'',strtolower($fet));
                        $fet = str_replace("pm",'',strtolower($fet));

                        $previous_date = date('Y-m-d',strtotime('- 1 day'.$date));
                        $startdate  = date('Y-m-d H:i:s',strtotime($previous_date.' '.$fet));
                        $enddate    = date('Y-m-d H:i:s',strtotime(dbDateFormat($request->track_date).' '.$fast_start_time));
                        $tracking->total_fast_hour = (float) sprintf('%0.2f',differenceInHours($startdate,$enddate));
                    }                    
                }
            }

            // dd('save');

            $tracking->save();
            if(!empty($request->current_day_waist_measurement) || !empty($request->current_day_weight)){
                if(!empty($request->current_day_waist_measurement)){

                    $user->today_waist_measurement = $request->current_day_waist_measurement;
                }
                if(!empty($request->current_day_weight)){
                    $user->today_current_weight = $request->current_day_weight;
                }
                $user->save();
            }

            DB::commit();
            if($tracking->track_date < date('Y-m-d')){
                $this->_refreshTimeForPreviousUpdateDate($tracking);
            }

            return successMsgResponse('Daily Tracking created successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return errorMsgResponse($e->getMessage());
        }

    }

    public function getDailyTrakingList(Request $request){
        $user_id = Auth::id();
        $list = $this->tracking->where('user_id',$user_id)->paginate(config('constant.adminPerPage'));
        return dataResponse($list);
    }

    public function getDailyTrakingByDate(Request $request){
        $date = $request->date??date('m/d/Y');
        $user_id = Auth::id();
        $_date = dbDateFormat($date);
        $record = $this->tracking->where(['user_id'=>$user_id,'track_date'=>$_date])->first();
        
        return dataResponse($record);
    }


    public function dailyTrakingUpdate(Request $request,Tracking $tracking){
        $validation = Validator::make($request->all(),[
            'current_day_weight'=>'required',
            'water_intake'=>'required',
            'bowel_movement'=>'required',
            'lunch'=>'required',
            'dinner'=>'required',
            
        ]);

        if($validation->fails()){
            return errorMsgResponse($validation->errors()->first());
        }

        /*if($tracking->track_date<date('Y-m-d')){
            return errorMsgResponse('You can not update before '.date('m/d/Y').' record');
        }*/

        $user = Auth::user();
        DB::beginTransaction();
        try {
            $fast_start_time = str_replace('am', '', str_replace('pm', '', str_replace(' ', '', strtolower($tracking->fast_start_time))));
            $fast_end_time = str_replace('am', '', str_replace('pm', '', str_replace(' ', '', strtolower($tracking->fast_end_time))));

            if(isset($request->lunch['start_time']['hh']) && !empty($request->lunch['start_time']['hh'])){

                $lunch_start_time = convertToTime($request->lunch['start_time']['hh'].":".$request->lunch['start_time']['mm']." ".$request->lunch['start_time']['ext']);
                if(!empty($fast_start_time) && $fast_start_time>$lunch_start_time){
                    $fast_start_time = $lunch_start_time;
                }else if(empty($fast_start_time)){
                    $fast_start_time = $lunch_start_time;
                    if(empty($fast_end_time)){
                        $fast_end_time = $lunch_start_time;
                    }
                }
            }

            if(isset($request->dinner['end_time']['hh']) && !empty($request->dinner['end_time']['hh'])){

                $dinner_end_time = convertToTime($request->dinner['end_time']['hh'].":".$request->dinner['end_time']['mm']." ".$request->dinner['end_time']['ext']);

                if(!empty($fast_end_time) && $fast_end_time<$dinner_end_time){
                    $fast_end_time = $dinner_end_time;
                }else if(empty($fast_end_time)){
                    $fast_end_time = $dinner_end_time;
                    if(empty($fast_start_time)){
                        $fast_start_time = $dinner_end_time;
                    }
                }
            }

            if((!empty($request->snack))){
                foreach($request->snack as $snack){
                    
                    if(isset($snack['start_time']['hh']) && !empty($snack['start_time']['hh']) && isset($snack['start_time']['mm']) && isset($snack['start_time']['ext'])){
                       $snack_time = convertToTime($snack['start_time']['hh'].":".$snack['start_time']['mm']." ".$snack['start_time']['ext']);
                        if(!empty($fast_start_time) && $snack_time<$fast_start_time){
                            $fast_start_time = $snack_time;
                        }else if(!empty($snack_time) && empty($fast_start_time)){
                            $fast_start_time = $snack_time;
                        }

                        if(!empty($fast_end_time) && $snack_time>$fast_end_time){
                            $fast_end_time = $snack_time;
                        }else if(!empty($snack_time) && empty($fast_end_time)){
                            $fast_end_time = $snack_time;
                        }
                    }
                }
            }

            $tracking->current_day_weight = $request->current_day_weight;
            $tracking->current_day_waist_measurement = $request->current_day_waist_measurement;
            $tracking->water_intake = $request->water_intake;
            $tracking->supplement = encryptDecrypt('encrypt', serialize($request->supplement));
            $tracking->bowel_movement = $request->bowel_movement;
            $tracking->exercise = encryptDecrypt('encrypt', serialize($request->exercise));
            //$tracking->breakfast = encryptDecrypt('encrypt', serialize($request->breakfast));
            $tracking->lunch = encryptDecrypt('encrypt', serialize($request->lunch));
            $tracking->snack = encryptDecrypt('encrypt', serialize($request->snack));
            $tracking->dinner = encryptDecrypt('encrypt', serialize($request->dinner));
            $tracking->total_exercise_duration = encryptDecrypt('encrypt', serialize($request->total_exercise_duration));
            $tracking->note = $request->note ?? '';

            if(!empty($fast_start_time) && !empty($fast_end_time)){
                $tracking->fast_start_time = $fast_start_time;
                $tracking->fast_end_time = $fast_end_time;
                $pre_tracking = $this->tracking->where(['user_id'=>$user->id])->whereDate('track_date',date('Y-m-d',strtotime('- 1 day'.$tracking->track_date)))->select('fast_start_time','fast_end_time','track_date')->whereNull('reset_type')->orderBy('track_date','desc')->first();
                if(!empty($pre_tracking) && !empty($pre_tracking->fast_end_time) && !empty($pre_tracking->fast_start_time)){
                    $fet = str_replace(" ",'',$pre_tracking->fast_end_time);
                    $fet = str_replace("am",'',strtolower($fet));
                    $fet = str_replace("pm",'',strtolower($fet));
                    $startdate  = date('Y-m-d H:i:s',strtotime($pre_tracking->track_date.' '.$fet));
                    $enddate    = date('Y-m-d H:i:s',strtotime($tracking->track_date.' '.$fast_start_time));
                    $tracking->total_fast_hour = (float) sprintf('%0.2f',differenceInHours($startdate,$enddate));
                }else{
                    $previous_of_previous_tracking = $this->tracking->where(['user_id'=>$user->id])->whereDate('track_date','<',$tracking->track_date)->select('total_fast_hour','fast_end_time')->whereNull('reset_type')->orderBy('track_date','desc')->first();
                    if($previous_of_previous_tracking && !empty($previous_of_previous_tracking->fast_end_time)){
                        $fet = str_replace(" ",'',$previous_of_previous_tracking->fast_end_time);
                        $fet = str_replace("am",'',strtolower($fet));
                        $fet = str_replace("pm",'',strtolower($fet));

                        $previous_date = date('Y-m-d',strtotime('- 1 day'.$tracking->track_date));
                        $startdate  = date('Y-m-d H:i:s',strtotime($previous_date.' '.$fet));
                        $enddate    = date('Y-m-d H:i:s',strtotime($tracking->track_date.' '.$fast_start_time));
                        $tracking->total_fast_hour = (float) sprintf('%0.2f',differenceInHours($startdate,$enddate));
                        //\Log::info('Log message', array('startdate' => $startdate,'enddate'=>$enddate,'previous_date'=>$previous_date,'end'=>$previous_of_previous_tracking->fast_end_time));
                    }
                    
                }
            }

            $tracking->save();

            if(!empty($request->current_day_waist_measurement) || !empty($request->current_day_weight)){
                if(!empty($request->current_day_waist_measurement)){

                    $user->today_waist_measurement = $request->current_day_waist_measurement;
                }
                if(!empty($request->current_day_weight)){
                    $user->today_current_weight = $request->current_day_weight;
                }
                $user->save();
            }
            DB::commit();
            if($tracking->track_date<date('Y-m-d')){
                $this->_refreshTimeForPreviousUpdateDate($tracking);
            }
            return successMsgResponse('Daily Tracking updated successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return errorMsgResponse($e->getMessage());
        }
    }

    public function _refreshTimeForPreviousUpdateDate($tracking){
        $fast_start_time = null;
        $fast_end_time = null;
        $previous_date = '';
        $previous_fast_end_time = '';
        $previous_tracking = Tracking::whereDate('track_date','<',$tracking->track_date)->where('user_id',$tracking->user_id)->whereNull('reset_type')->orderBy('track_date','desc')->first();
        if($previous_tracking && !empty($previous_tracking->fast_end_time)){
            $fet = str_replace(" ",'',$previous_tracking->fast_end_time);
            $fet = str_replace("am",'',strtolower($fet));
            $fet = str_replace("pm",'',strtolower($fet));
            $previous_date = $previous_tracking->track_date;
            $previous_fast_end_time = $fet;
        }

        $all_trakings_from_update_dates = Tracking::whereDate('track_date','>=',$tracking->track_date)->where('user_id',$tracking->user_id)->whereNull('reset_type')->orderBy('track_date')->get();

        if($all_trakings_from_update_dates->count()){
            foreach($all_trakings_from_update_dates as $row){
                if(isset($row->lunch['start_time']['hh']) && !empty($row->lunch['start_time']['hh']) && isset($row->lunch['start_time']['mm']) && isset($row->lunch['start_time']['ext'])){
                   $lunch_time = convertToTime($row->lunch['start_time']['hh'].":".$row->lunch['start_time']['mm']." ".$row->lunch['start_time']['ext']);
                    if(!empty($fast_start_time) && $lunch_time<$fast_start_time){
                        $fast_start_time = $lunch_time;
                    }else if(!empty($lunch_time) && empty($fast_start_time)){
                        $fast_start_time = $lunch_time;
                    }

                    if(!empty($fast_end_time) && $lunch_time>$fast_end_time){
                        $fast_end_time = $lunch_time;
                    }else if(!empty($lunch_time) && empty($fast_end_time)){
                        $fast_end_time = $lunch_time;
                    }
                }
                
                if((!empty($row->snack))){
                    foreach($row->snack as $snack){
                        if(isset($snack['start_time']['hh']) && !empty($snack['start_time']['hh']) && isset($snack['start_time']['mm']) && isset($snack['start_time']['ext'])){
                           $snack_time = convertToTime($snack['start_time']['hh'].":".$snack['start_time']['mm']." ".$snack['start_time']['ext']);
                            if(!empty($fast_start_time) && $snack_time<$fast_start_time){
                                $fast_start_time = $snack_time;
                            }else if(!empty($snack_time) && empty($fast_start_time)){
                                $fast_start_time = $snack_time;
                            }

                            if(!empty($fast_end_time) && $snack_time>$fast_end_time){
                                $fast_end_time = $snack_time;
                            }else if(!empty($snack_time) && empty($fast_end_time)){
                                $fast_end_time = $snack_time;
                            }
                        }
                    }
                }


                if(isset($row->dinner['end_time']['hh']) && !empty($row->dinner['end_time']['hh'])){
                    $dinner_end_time = convertToTime($row->dinner['end_time']['hh'].":".$row->dinner['end_time']['mm']." ".$row->dinner['end_time']['ext']);
                    if(!empty($fast_end_time) && $fast_end_time<$dinner_end_time){
                        $fast_end_time = $dinner_end_time;
                    }else if(empty($fast_end_time)){
                        $fast_end_time = $dinner_end_time;
                        if(empty($fast_start_time)){
                            $fast_start_time = $dinner_end_time;
                        }
                    }
                }


                if(!empty($previous_fast_end_time) && !empty($previous_date)){
                    $row->fast_start_time = $fast_start_time;
                    $row->fast_end_time = $fast_end_time;

                    $just_previous = date('Y-m-d',strtotime('- 1 day'.$row->track_date));                    
                    $fet = str_replace(" ",'',$previous_fast_end_time);
                    $fet = str_replace("am",'',strtolower($fet));
                    $fet = str_replace("pm",'',strtolower($fet));
                    $startdate  = date('Y-m-d H:i:s',strtotime($just_previous.' '.$fet));
                    $enddate    = date('Y-m-d H:i:s',strtotime($row->track_date.' '.$fast_start_time));
                    //\Log::info('Save Message', array('startdate' => $startdate,'enddate'=>$enddate));
                    $row->total_fast_hour = (float) sprintf('%0.2f',differenceInHours($startdate,$enddate));
                }else{
                    $row->fast_start_time = $fast_start_time;
                    $row->fast_end_time = $fast_end_time;
                }
                // dd($row);
                // dd($previous_fast_end_time, $previous_date);

                $row->save();
                //$pd = $previous_date;
                //$et = $previous_fast_end_time;
                $previous_date = $row->track_date;
                $previous_fast_end_time = $fast_end_time;
                $fast_start_time = null;
                $fast_end_time = null;

                //\Log::info('Log message', array('startdate' => $pd,'enddate'=>$previous_date,'previous_date'=>$pd,'end'=>$et));
            }
        }
    }

    public function recipeAddToFavourite(Request $request,$id){
        $user = Auth::user();
        $favourite = FavouriteRecipe::where(['user_id'=>$user->id,'recipe_id'=>$id])->first();
        if($favourite){
            $favourite->delete();
            return response()->json(['status'=>true,'is_favourite'=>'false','msg'=>'Recipe removed from your favorite']);
        }
        $favourite = new FavouriteRecipe;
        $favourite->user_id = $user->id;
        $favourite->recipe_id = $id;
        $favourite->save();
        return response()->json(['status'=>true,'is_favourite'=>'true','msg'=>'Recipe added to your favorites']);
        
    }

    public function getFavouriteRecipeList(){
        $user = Auth::user();
        $favourite = new FavouriteRecipe;
        $list = $favourite->getFavouriteRecipeList($user->id);
        return dataResponse($list);
    }

    public function getMeal(){
        return dataResponse(config('constant.meals'));
    }

    public function storeAddMeal(Request $request){
        $rules = [
            'meal_type'=>'required',
            'track_date'=>'required',
            'food_type'=>'required'
        ];

        if(strtolower($request->meal_type)=='dinner'){
            $rules['end_time'] = 'required';
            $data['end_time'] = $request->end_time;
        }else if(strtolower($request->meal_type)=='lunch'){
            $rules['start_time'] = 'required';
            $data['start_time'] = $request->start_time;
        }

        $validation = Validator::make($request->all(),$rules);

        if($validation->fails()){
            return errorMsgResponse($validation->errors()->first());
        }

        $user = Auth::user();
        $date = dbDateFormat($request->track_date);

        $tracking = $this->tracking->where(['user_id'=>$user->id,'track_date'=>$date])->first();
        if(!$tracking){
            $tracking = new $this->tracking;            
        }
        
        DB::beginTransaction();
        try {
            $meal_type  = strtolower($request->meal_type);
            $record     = $tracking->$meal_type;
            
            $data['food_type']  = $record['food_type']??[];
            array_push($data['food_type'], $request->food_type);
            $tracking->user_id = $user->id;
            $tracking->track_date = $date;
            if(strtolower($request->meal_type)=='snack'){
                $temp = $record??[];
                $food = $request->food_type;
                $ff['foodName'] = $food['foodName'];
                $ff['Quantity'] = $food['Quantity'];
                if(isset($food['start_time'])){
                    $ff['start_time'] = $food['start_time'];
                }
                array_push($temp,$ff);
                $tracking->$meal_type = encryptDecrypt('encrypt', serialize($temp));
            }else{
                $tracking->$meal_type = encryptDecrypt('encrypt', serialize($data));    
            }
            
            $tracking->save();
            DB::commit();
            return successMsgResponse('Meal added to '.$meal_type.'!');
        } catch (\Exception $e) {
            DB::rollback();
            return errorMsgResponse($e->getMessage());
        }

    } 

    public function getAddedMeal(Request $request){
        if(empty($request->date)){
            return errorMsgResponse('Date is required');
        }

        $user = Auth::user();
        $date = dbDateFormat($request->date);
        $added_meal = $this->tracking->select(['lunch','snack','dinner'])->whereDate('track_date',$date)->where('user_id',$user->id)->first();
        return dataResponse($added_meal);
    }

    public function passwordUpdate(Request $request){
        $validation = Validator::make($request->all(),[
            'password_confirmation'=>'required|max:50',
            'password'=>'required|max:50|confirmed',
        ]);

        if($validation->fails()){
            return errorMsgResponse($validation->errors()->first());
        }
        $user = Auth::user();
        //!Hash::check($request->password, $user->password)
        $user->password = bcrypt($request->password);
        $user->save();
        return successMsgResponse('Password changed successfully');
    }

    public function getProfile(){
        $user = Auth::user();
        $data['user'] = getLoginDetails($user);
        $is_low_to_high = false;
        if($user->goal_weight > $user->current_weight){
            $is_low_to_high = true;
            $data['weight_loss_to_date_text'] = 'Total weight gain to date';
            $data['waist_loss_to_date_text'] = 'Total waist inches gain to date';
        }else{
            $data['weight_loss_to_date_text'] = 'Total weight loss to date';
            $data['waist_loss_to_date_text'] = 'Total waist inches loss to date';
        }
        $favourite = new FavouriteRecipe;
        $list = $favourite->getFavouriteRecipeList($user->id,false);
        $tracking = $this->tracking->where('user_id',$user->id)->whereNotNull('current_day_weight')->whereDate('track_date','<=',date('Y-m-d'))->orderBy('track_date','desc')->first();
        $weight_loss_to_date = 0;
        $waist_loss_to_date = 0;
        $pounds = $user->current_weight;
        $ounces = getOunce($user->current_weight);
        if($tracking){
            $weight_loss_to_date = ($is_low_to_high==true) ? ($tracking->current_day_weight-$user->current_weight)  : ($user->current_weight - $tracking->current_day_weight);
            $waist_loss_to_date = ($is_low_to_high==true) ? ($tracking->current_day_waist_measurement - $user->waist_measurement) : ($user->waist_measurement - $tracking->current_day_waist_measurement);

            //$pounds = $tracking->current_day_weight;
            //$ounces = getOunce($tracking->current_day_weight);

        }
        $cusrrent_date_fast_time = $this->tracking->where(['user_id'=>$user->id])->where('total_fast_hour','>',0)->whereDate('track_date','<',date('Y-m-d'))->select('total_fast_hour')->orderBy('track_date','desc')->first();
        
        $data['favourite_meal'] = $list->count();
        $data['age'] = getAge($user->dob);
        $data['weight_loss_to_date'] = ($weight_loss_to_date>=0)? $weight_loss_to_date : 0;
        $data['waist_loss_to_date']  = ($waist_loss_to_date>=0) ? $waist_loss_to_date : 0;
        $data['current_month_avg_fast_time']  = $this->getFunction->getCurrentMonthFastTimeHours($user->id);
        $data['current_date_fast_time'] =   $cusrrent_date_fast_time->total_fast_hour??0;
        $data['rfm']                    = getRelativeFatMass($tracking,$user);
        $data['pounds']                 = number_format($pounds,1);
        $data['ounces']                 = $ounces;
        return dataResponse($data);
    } 

    public function chatMsgToAdmin(Request $request){
        $user = Auth::user();
        $validation = Validator::make($request->all(),[
            'receiver_id'=>'required',
        ]);


        if($validation->fails()){
            return errorMsgResponse($validation->errors()->first());
        }
        $receiver = User::where('id',$request->receiver_id)->first();
        $msg = $request->msg;
        $admin_id = 0;
        $user_id = 0;
        $admin_total = 0;
        $user_total = 0;
        $read_user_msg = false;
        $read_admin_msg = false;
        $total_count = 0;
        if($user->hasRole(config('constant.role.customer'))==true){
            //registration_ids

            $admin_id = $request->receiver_id;
            $user_id = $user->id;
            $admin_total = 1;
            $read_user_msg = true;
            $total_count = \App\Models\ChatCount::where(['admin_id'=>$admin_id])->sum('admin_total');
            $total_count = ($total_count==0)?1:$total_count+1;
        }else if($user->hasRole(config('constant.role.admin'))==true){
            $user_id = $request->receiver_id;
            $admin_id = 1;
            $user_total = 1;
            $read_admin_msg = true;
            $total_count = \App\Models\ChatCount::where(['user_id'=>$user_id,'admin_id'=>$admin_id])->sum('total');
            $total_count = ($total_count==0)?1:$total_count+1;
        }


        if($admin_id!=0 && $user_id!=0){
            $chat_count = \App\Models\ChatCount::where(['user_id'=>$user_id,'admin_id'=>$admin_id])->first();
            if(!$chat_count){
                $chat_count = new \App\Models\ChatCount;
                $chat_count->admin_total = $admin_total;
                $chat_count->total = $user_total;
                $chat_count->admin_id = $admin_id;
                $chat_count->user_id = $user_id;
            }else{
                $chat_count->admin_total = $chat_count->admin_total+$admin_total;
                $chat_count->total = $chat_count->total+$user_total;

            }
            if($read_user_msg==true){
                $chat_count->total = 0;
            }
            if($read_admin_msg==true){
                $chat_count->admin_total = 0;
            }
            $chat_count->save();
        }
        
        $url = NULL;
        if($request->hasFile('image')){
            $path = 'uploads/chats';
            $url = uploadImage($request,'image',$path);
            if(empty($msg)){
                $msg = 'You have a new image';
            }
        }

        
        if($receiver && !empty($msg)){
            if($read_user_msg==true){
                $ids = [];
                $fcm_tokens = User::select('fcm_token')->where('status',config('constant.status.active'))->whereNotNull('fcm_token')->whereHas('roles',function($q){
                            $q->where('name',config('constant.role.admin'));
                        })->get();
                if($fcm_tokens->count()){
                    foreach($fcm_tokens as $t){
                        array_push($ids,$t->fcm_token);
                    }
                }

                if(count($ids)>0){
                    $data = [
                        'registration_ids'=>$ids,
                        'notification'=>[
                            'title'=>"New message",
                            'body'=>$msg,
                            'mutable_content'=>false,
                            'sound'=>'Tri-tone',
                            'badge'=> $total_count
                        ],                    
                        
                    ];
                    $res_notify = pushNotification($data);
                }
            }else{
                if(!empty($receiver->fcm_token)){
                    $data = [
                        'to'=>$receiver->fcm_token,
                        'notification'=>[
                            'title'=>"New message",
                            'body'=>$msg,
                            'mutable_content'=>false,
                            'sound'=>'Tri-tone',
                            'badge'=> $total_count
                        ],                    
                        
                    ];

                    $res_notify = pushNotification($data);
                }
                
            }
            
        }

        return response()->json(['status'=>true,'url'=>$url,'msg'=>'Succcess']);
    }

    public function chatMsgRead(Request $request){
        $validation = Validator::make($request->all(),[
            'receiver_id'=>'required',
        ]);

        if($validation->fails()){
            return errorMsgResponse($validation->errors()->first());
        }

        $user =  Auth::user();
        if($user->hasRole(config('constant.role.customer'))==true){
            $chat_count = \App\Models\ChatCount::where(['user_id'=>$user->id,'admin_id'=>$request->receiver_id])->first();
            $chat_count->total = 0;
            $chat_count->save();
        }else if($user->hasRole(config('constant.role.admin'))==true){
            $chat_count = \App\Models\ChatCount::where(['user_id'=>$request->receiver_id,'admin_id'=>$user->id])->first();
            $chat_count->admin_total = 0;
            $chat_count->save();
        }
        return successMsgResponse('Successful');
    }

    public function notificationList(Request $request){
        $user = Auth::user();
        $share = new \App\Models\Share;
        $list = $share->getShareNotification($request,$user->id);
        
        return dataResponse($list);
    }

    public function notificationDetail(PushNotification $data){
        $user = Auth::user();
        \App\Models\Share::where(['user_id'=>$user->id,'shareable_id'=>$data->id,'shareable_type'=>'App\Models\PushNotification'])->update(['status'=>1]);
        $arr = $data->toArray();
        $arr['created_at'] = date('h:i a, d M Y', strtotime($arr['created_at']));
        $arr['updated_at'] = date('h:i a, d M Y', strtotime($arr['updated_at']));
        // dd($arr);
        return dataResponse($arr);
    }

    public function chatAndNotificationCount(){
        $user = Auth::user();
        $chat_count = \App\Models\ChatCount::where(['user_id'=>$user->id])->first();
        if($user->id == '1'){
            $data['chat_unread_count'] = chatTotal($user->id);
        } else {
            $data['chat_unread_count'] = $chat_count->total??0;
        }
        $data['notification_unread_count'] = \App\Models\Share::where(['user_id'=>$user->id,'status'=>0,'shareable_type'=>'App\Models\PushNotification'])->count();
        $data['is_plan_expired'] = false;
        $data['is_plan_expired_msg'] = '';
        $data['status'] = $user->status;
        $data['data'] = [];
        $is_plan_expired = \App\Models\SubscribeMember::where(['user_id'=>$user->id,'status'=>'Active'])->orderBy('id','desc')->first();
        if($is_plan_expired){
            if($is_plan_expired->subscription_plan_id==1 && $is_plan_expired->renewal_date<=date('Y-m-d')){
                $is_plan_expired->renewal_date = date('Y-m-d',strtotime($is_plan_expired->renewal_date.' + 3 years'));
                $is_plan_expired->save();
            }else if($is_plan_expired->renewal_date<date('Y-m-d')){
                $data['is_plan_expired'] = true;
                $data['is_plan_expired_msg'] = 'Your plan has expired. Please upgrade or renew.';

                # free plan get...
                $plan = \App\Models\SubscriptionPlan::where('id',1)->first();

                // $elite_member_request = \App\Models\SubscribeMember::where(['user_id'=>$user->id,'activated_from'=>'Admin','subscription_plan_id'=>config('constant.elite_member_id')])->count();

                # elite and premium member...
                $elite_member_request = \App\Models\SubscribeMember::where(['user_id'=>$user->id,'activated_from'=>'Admin'])->count();

                $user->role = config('constant.role.customer');
                $user->elite_member_request = $elite_member_request;
                $user->plan = [
                    'id'=>$plan->id,
                    'name'=>$plan->name,
                    'price'=>$plan->price??"Free",
                    'square_payment_subscription_id'=>$plan->square_payment_subscription_id
                    // 'elite_member_id' => config('constant.elite_member_id')
                ];
                $data['data'] = $user;

                /*$free = \App\Models\SubscribeMember::where(['user_id'=>$user->id,'subscription_plan_id'=>$plan->id])->first();
                if(!$free){
                    $free = new \App\Models\SubscribeMember;
                    $free->user_id = $user->id;
                    $free->subscription_plan_id = $plan->id;
                    $free->activated_date = date('Y-m-d');
                    $free->status = 'Active';
                }
                $free->status = 'Active';
                $free->renewal_date = date('Y-m-d',strtotime(date('Y-m-d')." +3 years"));
                $free->save();*/
                // $is_plan_expired->status = 'Expired';

                $is_plan_expired->status = 'Upgraded';
                $is_plan_expired->save();
            } else {
                $plan = \App\Models\SubscriptionPlan::where('id',$is_plan_expired->subscription_plan_id)->first();
                $elite_member_request = \App\Models\SubscribeMember::where(['user_id'=>$user->id,'activated_from'=>'Admin'])->count();
                $user->role = config('constant.role.customer');
                $user->elite_member_request = $elite_member_request;
                $user->plan = [
                    'id'=>$plan->id,
                    'name'=>$plan->name,
                    'price'=>$plan->price??"Free",
                    'square_payment_subscription_id'=>$plan->square_payment_subscription_id
                    // 'elite_member_id' => config('constant.elite_member_id')
                ];
                $data['data'] = $user;
            }
        } else {
            $data['is_plan_expired'] = true;
            $plan = \App\Models\SubscribeMember::where(['user_id'=>$user->id])->orderBy('id','desc')->get();
            if (count($plan) > 0) {
                $data['is_plan_expired_msg'] = 'Your plan has expired. Please upgrade or renew.';
            } else {
                $data['is_plan_expired_msg'] = 'Please select a membership plan.';
            }
        }
        
        // $elite_member_excepted = \App\Models\EliteMembershipRequest::where(['status'=>'elite_member'])->first();
        // if($elite_member_excepted){
        //     $data['elite_member_excepted'] = true;
        // }
        
        return dataResponse($data);
    }

    public function downloadPdfFile(Request $request){
        $user = Auth::user();
        $tracking = new Tracking;
        $trackings = $tracking->getUserTrackingById($request,$user->id);
        $html = view('common.traking-7-days',compact('trackings'))->render();

        $mpdf = new \Mpdf\Mpdf();
        $stylesheet = file_get_contents(asset('admin/css/tracking-report.css'));

        $mpdf->WriteHTML($stylesheet,\Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->WriteHTML($html);
        
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->list_indent_first_level = 0; 
        $mpdf->SetWatermarkText('Brand NUE');
        $mpdf->showWatermarkText = true;
        $mpdf->watermarkTextAlpha = 0.1;
        $path = 'uploads/trackingpdf/';
        $filename = $user->id.'-'.time().'.pdf';
        if(!File::exists(public_path($path))) File::makeDirectory(public_path($path), 0777,true);
        $mpdf->Output(public_path($path.$filename),'F');
        
        return dataResponse(asset('public/'.$path.$filename));
    }
    
    public function cancelSubscription(Request $request){
        try {
            $subscription_id = trim($request->subscription_id);
            
            $user = Auth::user();
            if(empty($subscription_id)){
                $active = \App\Models\SubscribeMember::where(['user_id'=>$user->id,'status'=>'Active'])->first();   
                if($active && empty($active->square_payment_subscription_id)){
                    $active->status = 'Cancelled';
                    $active->modify_date = date('Y-m-d');
                    $active->save();
                    /*$free = \App\Models\SubscribeMember::where(['user_id'=>$user->id,'subscription_plan_id'=>1])->first();
                    if(!$free){
                        $free = new \App\Models\SubscribeMember;
                        $free->user_id = $user->id;
                        $free->subscription_plan_id = 1;
                        $free->activated_from = 'Online';
                        $free->activated_date = date('Y-m-d');
                        $free->renewal_date = date('Y-m-d',strtotime('+1 years'));
                    }
                    $free->status = 'Active';
                    $free->save();*/
                    return response()->json(['status'=>true,'data'=>getLoginDetails($user),'msg'=>'Your subscription has been cancelled successfully.']);
                }
                return errorMsgResponse('Subscription id is not valid');
            }

            $active = \App\Models\SubscribeMember::where(['user_id'=>$user->id,'square_payment_subscription_id'=>$subscription_id])->first();
            if(!$active){
                return successMsgResponse('You do have an active subscription. Please call 513-271-2500 for assistance.');
                //return successMsgResponse('You have not any active subscription.');
            }

            $obj = new \App\Library\PostFunction;
            $result = $obj->cancelSubscription($subscription_id);
            if($result['status']==true){
                $active->status = 'Cancelled';
                $active->modify_date = date('Y-m-d');
                $active->save();
                /*$free = \App\Models\SubscribeMember::where(['user_id'=>$user->id,'subscription_plan_id'=>1])->first();
                if(!$free){
                    $free = new \App\Models\SubscribeMember;
                    $free->user_id = $user->id;
                    $free->subscription_plan_id = 1;
                    $free->activated_from = 'Online';
                    $free->activated_date = date('Y-m-d');
                    $free->renewal_date = date('Y-m-d',strtotime('+1 years'));
                }
                $free->status = 'Active';
                $free->save();*/
                return response()->json(['status'=>true,'data'=>getLoginDetails($user),'msg'=>'Your subscription has been cancelled successfully.']);
                
            }
            return errorMsgResponse($result['msg']);
        } catch (Exception $e) {
            return errorMsgResponse($e->getMessage());   
        }        
    } 

    public function filterGraph(Request $request){
        $user = Auth::user();
        if($request->sort=='weekly'){
            $data = $this->getFunction->getTrackingChartByWeekly($user->id);
            return dataResponse($data);
        }else if($request->sort=='monthly'){
            $data = $this->getFunction->getTrackingChartByMonthly($user->id);
            return dataResponse($data);
        }else if($request->sort=='yearly'){
            $data = $this->getFunction->getTrackingChartByYearly($user->id);
            // $data = $this->getFunction->getTrackingChart($user->id);
            return dataResponse($data);
        }

        $data = $this->getFunction->getTrackingChart($user->id);
        return dataResponse($data);
        
    }

    public function cancelAppleSubscription(Request $request){
        $subscription_id = trim($request->subscription_id);
        
        $user = Auth::user();
        if(empty($subscription_id)){
            $active = \App\Models\SubscribeMember::where(['user_id'=>$user->id,'status'=>'Active'])->first();  
            if($active && empty($active->square_payment_subscription_id)){
                if($active->device!='Apple') {
                    return errorMsgResponse('You can only cancel the subscription from apple device');
                }
                $active->status = 'Cancelled';
                $active->modify_date = date('Y-m-d');
                $active->save();
                /*$free = \App\Models\SubscribeMember::where(['user_id'=>$user->id,'subscription_plan_id'=>1])->first();
                if(!$free){
                    $free = new \App\Models\SubscribeMember;
                    $free->user_id = $user->id;
                    $free->subscription_plan_id = 1;
                    $free->activated_from = 'Online';
                    $free->activated_date = date('Y-m-d');
                    $free->renewal_date = date('Y-m-d',strtotime('+1 years'));
                }
                $free->status = 'Active';
                $free->device = 'Apple';
                $free->save();*/
                return response()->json(['status'=>true,'data'=>getLoginDetails($user),'msg'=>'Your subscription has been cancelled successfully.']);
            }
            return errorMsgResponse('Subscription id is not valid');
        }else{
            $active = \App\Models\SubscribeMember::where(['user_id'=>$user->id,'status'=>'Active','square_payment_subscription_id'=>$subscription_id])->first(); 
            if($active){
                if($active->device!='Apple') {
                    return errorMsgResponse('You can only cancel the subscription from apple device');
                }
                $active->status = 'Cancelled';
                $active->modify_date = date('Y-m-d');
                $active->save();
                /*$free = \App\Models\SubscribeMember::where(['user_id'=>$user->id,'subscription_plan_id'=>1])->first();
                if(!$free){
                    $free = new \App\Models\SubscribeMember;
                    $free->user_id = $user->id;
                    $free->subscription_plan_id = 1;
                    $free->activated_from = 'Online';
                    $free->activated_date = date('Y-m-d');
                    $free->renewal_date = date('Y-m-d',strtotime('+1 years'));
                }
                $free->status = 'Active';
                $free->device = 'Apple';
                $free->save();*/
                return response()->json(['status'=>true,'data'=>getLoginDetails($user),'msg'=>'Your subscription has been cancelled successfully.']);
            }
            return errorMsgResponse('Subscription id is not valid');
        }
    }

    /**
    * 12-jul-22
    * reset the fast time
    */
    public function resetFastTime(Request $request){
        $user_id = Auth::id();
        // $list = $this->tracking->where('user_id',$user_id)->whereNotNull('total_fast_hour')->whereMonth('track_date',date('m'))->update(['total_fast_hour'=> null, 'fast_start_time' => null , 'fast_end_time' => null]);
        $list = $this->tracking->where('user_id',$user_id)
                    ->whereMonth('track_date',date('m'))
                    ->update(['total_fast_hour'=> null, 'fast_start_time' => '00:00:00', 'fast_end_time' => '00:00:00', 'reset_type' => 'Reset']);

        return successMsgResponse('The Average Fasting Time was reset.');
    }

    public function deleteDailyTraking($id){        
        $record = $this->tracking->where(['id'=>$id])->first();
        $record->delete();
        return successMsgResponse('The Average Fasting Time was reset.');
    }

}
