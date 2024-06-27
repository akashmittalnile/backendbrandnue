<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\User;
use JWTAuth;
class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['customerLogin','logout']]);
        $this->guard = "api";
    }

    public function customerLogin(Request $request){
        $validator = Validator::make($request->all(),[
            'email'=>'required|max:191|exists:users',
            'password'=>'required|max:191',
            'fcm_token'=>'required'
        ],
        [
             'email.exists' => ' This user does not exist'
        ]);

        if($validator->fails()){
            return errorMsgResponse($validator->errors()->first());
        }

        $credentials = request(['email', 'password']);

        if (!$token = auth($this->guard)->setTTL(60*24*365)->attempt($credentials)) {

            return errorMsgResponse('Credentails does not match');
        }
        

        $user = auth($this->guard)->user();
        if($user->status=='N'){
            return errorMsgResponse('Your account is temporary inactive please contact the owner for same');
        }
        if(!empty($user->fcm_token)){
            /*JWTAuth::setToken($token);
            JWTAuth::invalidate();*/
            return response()->json(['status'=>false,'token'=>$token,'msg'=>'You seems to be logged in another device. Please Click on below button to logout from all devices.']);
        }
        if($user->fcm_token!=$request->fcm_token){
            $user->fcm_token = $request->fcm_token;
            $user->save();
        }
        return $this->respondWithToken($token);

        /*if($user->hasRole(config('constant.role.customer'))){
            return $this->respondWithToken($token);
        }else{
            return errorMsgResponse('You are not authorized to access administrator');   
        }*/
        
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        $data = auth($this->guard)->user();
        $obj =  new User;
        $plan = $obj->getActivePlan($data->id);
        
        $elite_member_id = config('constant.elite_member_id');
        $elite_member_request = \App\Models\SubscribeMember::where(['user_id'=>$data->id,'activated_from'=>'Admin','subscription_plan_id'=>$elite_member_id])->count();
        $data->last_login = date('Y-m-d H:i:s');
        $data->save();
        $data->elite_member_request = $elite_member_request;
        $data->role = config('constant.role.customer');
        if($data->hasRole(config('constant.role.admin'))){
            $data->role = config('constant.role.admin');
        }
        $data->plan = [] ;
        if($plan){
            $data->plan = [
                'id'=>$plan->id,
                'name'=>$plan->name,
                'price'=>$plan->price,
                'square_payment_subscription_id'=>$plan->square_payment_subscription_id,
                'elite_member_id' => $elite_member_id,
                'device'=>$plan->device,
            ];
        }
        return response()->json([
            'status'=>true,
            'msg'=>'Success',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth($this->guard)->factory()->getTTL() * 60,
            'data' => $data
        ]);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $user = Auth::user();
        $user->fcm_token = NULL;
        $user->save();
        auth($this->guard)->logout();
        return successMsgResponse('Successfully logged out');
    }
}
