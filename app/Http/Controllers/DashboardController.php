<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubscribeMember;
use App\Models\Tracking;
use Validator;
use App\User;
use Auth;

class DashboardController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function dashboard(Request $request){ 
        
        $user = new User;
        $total_digital_library = digitalLibraryTotal();
        $total_users  = $user->getCustomer($request,config('constant.status.active'))->count();
        $total_daily_trackers = Tracking::whereDate('track_date',date('Y-m-d',strtotime('- 1 day')))->count();
        $total_membership_plans = SubscribeMember::where('status','active')->count();
        
        return view('admin.dashboard',compact('total_digital_library','total_users','total_daily_trackers','total_membership_plans'));
    }

    public function profile(){
        $user = Auth::user();
        return view('admin.profile',compact('user'));
    }

    public function profileUpdate(Request $request){
        $this->validate($request,[
            'name'=>'required|max:191',
            'phone'=>'required',
            'gender'=>'required',
            'dob'=>'required'
        ]);

        $user = Auth::user();
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->gender = $request->gender;
        $user->dob = $request->dob??NULL;
        if($request->hasFile('profile_image')){
            $path = 'uploads/profile';
            $user->profile_image = uploadImage($request,'profile_image',$path);
        }
        $user->save();
        return back()->with('success','Profile updated successfully');
    }

    public function changePassword(){
        
        return view('admin.change-password');
    }

    public function changePasswordUpdate(Request $request){
        $this->validate($request,[
            'password'=>'required|max:191',
            'password_confirmation'=>'required|max:191|same:password'
        ]);
        $user = Auth::user();
        $user->password = bcrypt($request->password);
        $user->save();
        return back()->with('success','Password updated successfully');
    }

    public function getChartData(Request $request){
        $year = $request->year??date('Y');

        $plans = \App\Models\SubscriptionPlan::where('status','Y')->whereIn('id',[config('constant.elite_member_id'),config('constant.yearly_elite_member_id'),config('constant.premium_member_id'),config('constant.yearly_premium_member_id')])->get();
        $data['d3chart'] = [];
        foreach($plans as $plan){
            $temp = [];
            $temp['name'] = $plan->name;
            $temp['values'] = $this->_getSubscriptionTransaction($year,$plan);
            array_push($data['d3chart'], $temp);
        }
        return json_encode($data);
    }
    public function _getSubscriptionTransaction($year,$plan){
        $data = [];
        $date = date($year.'-01');
        while($date<=date($year.'-12-d')){
            $obj = new \App\Models\SubscribeMember;
            $total = $obj->getSubscribePriceByYearMonth($plan->id,$year,date('m',strtotime($date)));
            array_push($data, ['x'=>$date,'y'=>sprintf('%0.2f',$total)]);
            $date = date('Y-m',strtotime($date.' + 1 month'));
        }
        return $data;
    }
}
