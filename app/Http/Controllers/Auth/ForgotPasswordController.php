<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use App\User;

use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use App\Mail\MailResetPasswordNotification;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    public function showLinkRequestForm(){
        return view('auth.passwords.email');
    }

    public function sendResetLinkEmail(Request $request){
        $this->validate($request,['email' => 'required|email']);
        $user = User::where('email',$request->email)->first();
        if(!$user){
            return back()->withErrors(['email'=>"We can't find a user with that email address."]);
        }

        $token = Password::broker()->createToken($user); //create reset password token
        
        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        // $response = $this->broker()->sendResetLink(
        //     $request->only('email')
        // );

        try {
            // $send = Mail::to($user->email)->send(new MailResetPasswordNotification($user, $token));
            $response = $this->sendMail($user, $token);
            $code = json_decode($response);
            if($code->flag){
                return redirect()->back()->with('success', 'We have sent an activation link to your registered email Id.');
            }
            return back()->withErrors(['email'=>'We have not sent an activation link to your registered email Id.']);
        } catch (\Swift_TransportException $e) {
            // dd($e);
            return back()->withErrors(['email'=>'We have not sent an activation link to your registered email Id.']);
        }
    }

    public function sendMail($user, $token){
        $html = view('mails.resetPasswordToken', compact('user', 'token'))->render();
        $js_data = [
            'html' => $html,
            'to_mail' => $user->email,
            'subject' => 'Reset Password Email'
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
            CURLOPT_HTTPHEADER => array(                
            ),
        ));
        $create_response = curl_exec($curl);
        curl_close($curl);
        return $create_response;
    }
}
