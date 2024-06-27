<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Role;
use App\User;
use Validator;
use DB;

class RegisterController extends Controller
{
    public $user;
    public function __construct(User $user){
        $this->user = $user;
    }
    
    public function customerStore(Request $request){
        
        $validation = Validator::make($request->all(),[
            'first_name'=>'required|max:191',
            'last_name'=>'required|max:191',
            'email'=>'required|max:191|unique:users',
            'password'=>'required|confirmed',
            'password_confirmation'=>'required',
            'phone'=>'required',
            'gender'=>'required|max:10',
            'dob'=>'required',
            'height_feet'=>'required',
            'waist_measurement'=>'required',
            'goal_waist_measurement'=>'required',
            'current_weight'=>'required',
            'goal_weight'=>'required',
        ]);

        if($validation->fails()){
            return errorMsgResponse($validation->errors()->first());
        }

        if($request->current_weight == $request->goal_weight){
            return errorMsgResponse('Current weight and Goal weight not be same.');
        }

        DB::beginTransaction();
        try {
            $user = new $this->user;
            $user->name = $request->first_name.' '.$request->last_name;
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->phone = $request->phone;
            $user->gender = $request->gender;
            $user->dob = dbDateFormat($request->dob);
            $user->height_feet = $request->height_feet;
            $user->height_inch = $request->height_inch;
            $user->waist_measurement = $request->waist_measurement;
            $user->goal_waist_measurement = $request->goal_waist_measurement;
            $user->today_waist_measurement = $request->waist_measurement;
            $user->current_weight = $request->current_weight;
            $user->today_current_weight = $request->current_weight;
            $user->goal_weight = $request->goal_weight;
            $user->status = config('constant.status.active');
            if($request->hasFile('profile_image')){
                $path = 'uploads/profile';
                $user->profile_image = uploadImage($request,'profile_image',$path);
            }
            $user->save();
            $customerRole = Role::where('name', 'CUSTOMER')->first();
            $user->roles()->attach($customerRole);
            // \App\Models\SubscribeMember::insert(['user_id'=>$user->id,'subscription_plan_id'=>1,'activated_date'=>date('Y-m-d'),'renewal_date'=>date('Y-m-d',strtotime('+ 1 years')),'status'=>'Active','created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')]);
            DB::commit();
            
            return successMsgResponse('Account created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return errorMsgResponse($e->getMessage());
        }
    }

    

    /*public function createSquareCustomerCard(){
        $client = new SquareClient([
            'accessToken' => config('constant.squareAccessToken'),
            'environment' => config('constant.squareEnvironment'),
        ]);
        $cardsApi = $client->getCardsApi();
        $body_idempotencyKey = (string) Str::uuid();
        $body_sourceId = 'ccof:iRC9lPpmdkWEELfS4GB';
        $body_card = new \Square\Models\Card;
        $body_card->setId('id0');
        $body_card->setCardBrand(\Square\Models\CardBrand::INTERAC);
        $body_card->setLast4('0004');
        $body_card->setExpMonth(236);
        $body_card->setExpYear(60);
        $body_card->setCardholderName('Amelia Earhart');
        $body_card->setBillingAddress(new Address);
        $body_card->getBillingAddress()->setAddressLine1('500 Electric Ave');
        $body_card->getBillingAddress()->setAddressLine2('Suite 600');
        $body_card->getBillingAddress()->setAddressLine3('address_line_34');
        $body_card->getBillingAddress()->setLocality('New York');
        $body_card->getBillingAddress()->setSublocality('sublocality8');
        $body_card->getBillingAddress()->setAdministrativeDistrictLevel1('NY');
        $body_card->getBillingAddress()->setPostalCode('10003');
        $body_card->getBillingAddress()->setCountry(Country::US);
        $body_card->setCustomerId('Y571GMNBK8T6S8M39R0T85WZC4');
        $body_card->setReferenceId('user-id-1');
        $body = new \Square\Models\CreateCardRequest(
            $body_idempotencyKey,
            $body_sourceId,
            $body_card
        );
        //$body->setVerificationToken('STORE');

        $apiResponse = $cardsApi->createCard($body);

        if ($apiResponse->isSuccess()) {
            $createCardResponse = $apiResponse->getResult();
            dd($createCardResponse);
        } else {
            $errors = $apiResponse->getErrors();
            dd($errors);
        }
    }*/
}
