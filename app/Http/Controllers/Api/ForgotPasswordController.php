<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Password;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Mail\DefaultMail;
use Validator;
use App\User; 

class ForgotPasswordController extends Controller
{
    public function passwordResetRequest(Request $request){
        $validator = Validator::make($request->all(),[
            'email'=>'required|email|max:191',
        ]);

        if($validator->fails()){
            return response()->json(['status'=>false,'msg'=>$validator->errors()->first()]);
        }

        $user = User::where(['email'=>$request->email])->first();
        if(!$user){
            return response()->json(['status'=>false,'msg'=>'Email id not found in our system']);   
        }
        $token = uniqueCode();
        $user->remember_token = $token;
        $user->save();
        $this->_sendMail($user);
        return response()->json(['status'=>true,'msg'=>'You reset password code has been sent to your email id']);

    }

    public function passwordUpdate(Request $request){
        if($request->status=='otp'){
            $rules['otp'] = 'required';
        }else{
            $rules['otp'] = 'required';
            $rules['password'] = 'required|max:50';
            $rules['password_confirmation']='required|max:50|same:password';
        }
        $validator = Validator::make($request->all(),$rules);

        if($validator->fails()){
            return errorMsgResponse($validator->errors()->first());
        }

        $user = User::where('remember_token',$request->otp)->first();
        if($user && !empty($request->password)){
            $user->password = bcrypt($request->password);
            $user->remember_token = NULL;
            $user->save();
            return successMsgResponse('Password updated successfully');
        }else if($user){
            return successMsgResponse('Success');
        }

        return errorMsgResponse('OTP is not valid');

    }

    private function _sendMail($user)
    {
        $js_data = [
            'name' => $user->name,
            'otp' => $user->remember_token,
            'to_mail' => $user->email,
        ];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://admin.brandnueweightloss.com/demo/testmail.php',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>  $js_data,
            CURLOPT_HTTPHEADER => array(),
        ));
        $create_response = curl_exec($curl);
        curl_close($curl);
        return $create_response;
    }

    private function _sendResetPasswordMail($user){
        $msg  = "<p></p>";
        $msg .= "<p>Dear ".$user->name.",</p>";
        $msg .= "<p>You are receiving this email because we received a password reset request for your account.</p>";
        $msg .= "<p></p>";
        $msg .= "<p><b>Your reset password otp is : </b>". $user->remember_token."</p>";
        $msg .= "<p></p>";
        $msg .= "<p>If you did not request a password reset, no further action is required.</p>";
        $msg .= "<p></p>";
        $msg .= "<p>Best Regards</p>";
        $msg .= "<p></p>";
        $msg .= "<p>The Brand Nue Team</p>";

        $data['from_email'] = config('constant.defaultEmail');
        $data['site_title'] = config('constant.defaultTitle');
        $data['subject']    = 'Reset Password Code';
        $data['view']       = 'common.default';
        $data['mail_to']    = $user->email;
        $data['content']    = $msg;
        Mail::to($data['mail_to'])->send(new DefaultMail( $data ));
    }
}
