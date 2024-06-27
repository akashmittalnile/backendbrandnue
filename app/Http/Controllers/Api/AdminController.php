<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubscribeMember;
use App\Models\Tracking;
use Validator;
use App\User;
use Auth;

class AdminController extends Controller
{
    public $user;

    public function __construct(User $user){
        $this->user = $user;
    }

    public function dashboard(Request $request){
        $data['total_digital_library'] = digitalLibraryTotal();
        $data['total_users']  = $this->user->getCustomer($request,config('constant.status.active'))->count();
        $data['total_daily_trackers'] = Tracking::whereDate('track_date',date('Y-m-d',strtotime('- 1 day')))->count();
        $data['total_membership_plans'] = SubscribeMember::where('status','active')->count();
        return dataResponse($data);
    }
    
    public function customerChatList(Request $request){
        $customers = $this->user->getChatCountUserList($request);
        return dataResponse($customers);
    }

    public function uploadProfileImage(Request $request){
        $validation = Validator::make($request->all(),[
            'profile_image'=>'required'
        ]);

        if($validation->fails()){
            return errorMsgResponse($validation->errors()->first());
        }
        $user = Auth::user();
        if($request->hasFile('profile_image')){
            $image = $user->profile_image;
            $path = 'uploads/profile';
            $user->profile_image = uploadImage($request,'profile_image',$path);
            if(!empty($image)){
                deleteImage($image);
            }
            $user->save();
            return response()->json(['status'=>true,'msg'=>'Profile image updated successfully','url'=>$user->profile_image]);
        }

        return errorMsgResponse('Please upload image');
    }
}
