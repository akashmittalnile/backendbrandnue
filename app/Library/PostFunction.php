<?php
namespace App\Library;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\UserInfo;

use Square\Models\ListLocationsResponse;
use Square\Models\CreateCustomerRequest;
use Square\Models\CreateSubscriptionRequest;
use Square\Models\SubscriptionSource;
use Square\Models\CreateCardRequest;
use Square\Exceptions\ApiException;
use Square\Http\ApiResponse;
use Square\Models\Currency;
use Square\Models\Address;
use Square\Models\Country;
use Square\Models\Money;
use Square\SquareClient;
use Square\LocationsApi;
use Square\Models\Card;

class PostFunction{

	public static function pushNotification($data_array){
		try {
		    $client = new \GuzzleHttp\Client();
		    $result = $client->request('post',config('constant.fcm.url'),[
		          'headers' => [
		          'Content-Type'  => 'application/json',
		          'Authorization' => config('constant.fcm.secretKey'),
		        ],
		        'json'=>$data_array
		        
		      ]);
		    $response = $result->getBody()->getContents();
		    $json_data = json_decode($response);
		    return ['status'=>true,'data'=>$json_data];
		} catch (\Exception $e) {
		    $response = $e->getResponse();
		    $responseBodyAsString = $response->getBody()->getContents();
		    $json_data = json_decode($responseBodyAsString);
		    return ['status'=>false,'data'=>$json_data];
		}

		// try{
		// 	// dd($data_array);
        //     $notificationdata = [
        //         "title" => $data_array['notification']['title'],
        //         "description" => $data_array['notification']['body'],
        //         'mutable_content'=>false,
        //         'sound'=>'Tri-tone',
        //         'badge'=> $data_array['notification']['badge'] ?? ''
        //     ];  
        //     $data['data'] =  $notificationdata;
        //     $data['token'] = $data_array['to']; 
        //     $payload['message'] = $data;
        //     $payload = json_encode($payload);
        //     $result =  sendnotificationusinghttpv1($data);
		// 	return dataResponse($result);
            
        // }catch(Exception $e){
        //     return response(["status"=>"false","error"=>$e->getMessage()]);
        // }
	}

	public static function uploadMultipleImage($file,$path){
        if(!File::exists(public_path($path))) File::makeDirectory(public_path($path), 0777,true);
        $ext = '.'.$file->getClientOriginalExtension();
        if(empty($edit_file_name)){
            $edit_file_name = str_replace($ext, time() . $ext, str_replace(" ","-",$file->getClientOriginalName()));
        }
        if($file->move(public_path($path),$edit_file_name)){
            return 'public/'.$path.'/'.$edit_file_name;
        }
        
        return NULL;
    }

    public function createSquareCustomer($user){
    	$client = new SquareClient([
    	    'accessToken' => config('constant.squareAccessToken'),
    	    'environment' => config('constant.squareEnvironment'),
    	]);
    	$customersApi = $client->getCustomersApi();

    	$body = new CreateCustomerRequest;
    	$body->setIdempotencyKey((string) Str::uuid());
    	$body->setGivenName($user->name);
    	$body->setFamilyName($user->name);
    	/*$body->setCompanyName('Nile');*/
    	$body->setNickname($user->name);
    	$body->setEmailAddress($user->email);
    	/*$body->setAddress(new Address);
    	$body->getAddress()->setAddressLine1('Vasundhara');
    	$body->getAddress()->setAddressLine2('Sector 13');
    	$body->getAddress()->setAddressLine3('Near balaji mandir');
    	$body->getAddress()->setLocality('gzb');
    	$body->getAddress()->setSublocality('');
    	$body->getAddress()->setAdministrativeDistrictLevel1('India');
    	$body->getAddress()->setPostalCode('201012');
    	$body->getAddress()->setCountry(Country::IN);
    	$body->getAddress()->setFirstName('Kamlesh');
    	$body->getAddress()->setLastName('Kumar');*/
    	$body->setPhoneNumber($user->phone);
    	$body->setReferenceId((string) Str::uuid());
    	/*$body->setNote('Custome notes');*/

    	$user_info = UserInfo::where('user_id',$user->id)->first();
    	if($user_info && !empty($user_info->square_payment_user_id)){
    		return ['status'=>true,'square_payment_user_id'=>$user_info->square_payment_user_id,'msg'=>''];
    	}
    	if(!$user_info){
    		$user_info = new UserInfo;
    		$user_info->user_id = $user->id;
    	}
    	try {
    	    $result = $customersApi->createCustomer($body);
    	    if ($result->isSuccess()) {
    	        $customer = $result->getResult()->getCustomer();
    	        $user_info->square_payment_user_id = $customer->getId();
    	        $user_info->save();
    	        return ['status'=>true,'square_payment_user_id'=>$customer->getId(),'msg'=>''];
    	    } else {
    	    	$user_info->square_payment_user_id_error = serialize($result->getErrors());
    	    	$user_info->save();
    	    	Log::error('Create Customer Error for '.$user->email,$result->getErrors());
    	    	return ['status'=>false,'square_payment_user_id'=>'','msg'=>$result->getErrors()];    	        
    	    }
    	} catch (ApiException $e) {
    		Log::error('Create Customer Error for '.$user->email,['error'=>$e->getMessage()]);
    		$user_info->square_payment_user_id_error = serialize([$e->getMessage()]);
    	    $user_info->save();
    		return ['status'=>false,'square_payment_user_id'=>'','msg'=>['error'=>$e->getMessage()]];
    	    
    	} 

    	/*$apiResponse = $customersApi->createCustomer($body);

    	if ($apiResponse->isSuccess()) {
    	    $createCustomerResponse = $apiResponse->getResult();
    	    print_r($createCustomerResponse);
    	    dd($createCustomerResponse->customer);
    	    if(empty($createCustomerResponse->errors)){
    	        $user_info = new \App\Models\UserInfo;
    	        $user_info->user_id = $user->id;
    	        $user_info->square_payment_user_id = $createCustomerResponse->customer->id;
    	        $user_info->save();
    	        return true;
    	    }
    	    
    	    
    	} else {
    	    $errors = $apiResponse->getErrors();
    	    
    	    return false;
    	}*/
    }

    public function createSquareCustomerCard($user,$customer_id,$source_id){
    	$client = new SquareClient([
    	    'accessToken' => config('constant.squareAccessToken'),
    	    'environment' => config('constant.squareEnvironment'),
    	]);
    	$cardsApi = $client->getCardsApi();
    	$body_idempotencyKey = (string) Str::uuid();
    	$body_sourceId = $source_id;
    	$body_card = new Card;
    	$body_card->setId('id0');
    	// $body_card->setCardBrand(Models\CardBrand::INTERAC);
    	// $body_card->setLast4('last_42');
    	// $body_card->setExpMonth(236);
    	// $body_card->setExpYear(60);
    	// $body_card->setCardholderName('Amelia Earhart');
    	// $body_card->setBillingAddress(new Models\Address);
    	// $body_card->getBillingAddress()->setAddressLine1('500 Electric Ave');
    	// $body_card->getBillingAddress()->setAddressLine2('Suite 600');
    	// $body_card->getBillingAddress()->setAddressLine3('address_line_34');
    	// $body_card->getBillingAddress()->setLocality('New York');
    	// $body_card->getBillingAddress()->setSublocality('sublocality8');
    	// $body_card->getBillingAddress()->setAdministrativeDistrictLevel1('NY');
    	// $body_card->getBillingAddress()->setPostalCode('10003');
    	// $body_card->getBillingAddress()->setCountry(Models\Country::US);
    	$body_card->setCustomerId($customer_id);
    	//$body_card->setReferenceId('user-id-1');
    	$body = new CreateCardRequest(
    	    $body_idempotencyKey,
    	    $body_sourceId,
    	    $body_card
    	);
    	//$body->setVerificationToken('verification_token0');

    	$user_info = UserInfo::where('user_id',$user->id)->first();
    	if(!$user_info){
    		$user_info = new UserInfo;
    		$user_info->user_id = $user->id;
    	}

    	try {
    		$apiResponse = $cardsApi->createCard($body);

    		if ($apiResponse->isSuccess()) {
    		    $createCardResponse = $apiResponse->getResult()->getCard();
    		    //print_r($createCardResponse);
    			return ['status'=>true,'card_id'=>$createCardResponse->getId(),'msg'=>''];
    		} else {
    		    $errors = $apiResponse->getErrors();
    		    $statusCode = $apiResponse->getStatusCode();
    		    Log::error('Create Card Error for '.$user->email,['errors'=>$errors,'error_code'=>$statusCode]);
    		    $user_info->square_payment_user_card_error = serialize(['errors'=>$errors,'error_code'=>$statusCode]);
    		    $user_info->save();
    		    return ['status'=>false,'msg'=>$errors[0]->getDetail(),'error_code'=>$statusCode];
    		}
    	} catch (ApiException $e) {
    		$statusCode = $apiResponse->getStatusCode();
    		Log::error('Create Card Error for '.$user->email,['errors'=>$e->getMessage(),'error_code'=>$statusCode]);
    		$user_info->square_payment_user_card_error = serialize(['errors'=>$e->getMessage(),'error_code'=>$statusCode]);
		    $user_info->save();
		    return ['status'=>false,'msg'=>$e->getMessage(),'error_code'=>$statusCode];
    		
    	} catch (\Exception $e) {
    		$statusCode = $apiResponse->getStatusCode();
    		Log::error('Create Card Error for '.$user->email,['error'=>$e->getMessage(),'error_code'=>$statusCode]);
    		$user_info->square_payment_user_card_error = serialize(['errors'=>$e->getMessage(),'error_code'=>$statusCode]);
		    $user_info->save();
		    return ['status'=>false,'msg'=>$e->getMessage(),'error_code'=>$statusCode];
    	}

    }

    public function createSquareCustomerSubscription($user,$customer_id,$plan_id,$card_id){
    	$client = new SquareClient([
    	    'accessToken' => config('constant.squareAccessToken'),
    	    'environment' => config('constant.squareEnvironment'),
    	]);
    	$subscriptionsApi = $client->getSubscriptionsApi();
    	$body_locationId = config('constant.squareLocationId');
    	$body_planId = $plan_id;
    	$body_customerId = $customer_id;
    	$body = new CreateSubscriptionRequest(
    	    $body_locationId,
    	    $body_planId,
    	    $body_customerId
    	);
    	$body->setIdempotencyKey((string) Str::uuid());
    	$body->setStartDate(date('Y-m-d'));
    	//$body->setCanceledDate('canceled_date0');
    	/*$body->setTaxPercentage('5');
    	$body->setPriceOverrideMoney(new Money);
    	$body->getPriceOverrideMoney()->setAmount(100);
    	$body->getPriceOverrideMoney()->setCurrency(Currency::USD);*/
    	$body->setCardId($card_id);
    	/*$body->setTimezone('America/Los_Angeles');
    	$body->setSource(new SubscriptionSource);
    	$body->getSource()->setName('My App');*/
    	$user_info = UserInfo::where('user_id',$user->id)->first();
    	if(!$user_info){
    		$user_info = new UserInfo;
    		$user_info->user_id = $user->id;
    	}

    	try {
    		$apiResponse = $subscriptionsApi->createSubscription($body);
    		if ($apiResponse->isSuccess()) {
    			$statusCode = $apiResponse->getStatusCode();
    		    $createSubscriptionResponse = $apiResponse->getResult()->getSubscription();
    			$subscription_id = $createSubscriptionResponse->getId();
    		    Log::error('Subscription Id for '.$user->email,['subscription_id'=>$subscription_id,'error_code'=>$statusCode]);

    			$user_info->api_response = json_encode(['status'=>true,'subscription_id'=>$subscription_id,'subscription_status'=>$createSubscriptionResponse->getStatus(),'data'=>$createSubscriptionResponse]);
				$user_info->save();
    		    
    		    return ['status'=>true,'subscription_id'=>$subscription_id,'subscription_status'=>$createSubscriptionResponse->getStatus(),'data'=>$createSubscriptionResponse];
    		} else {
    		    $errors = $apiResponse->getErrors();
    		    $statusCode = $apiResponse->getStatusCode();
    		    Log::error('Create Subscription Error for '.$user->email,['errors'=>$errors,'error_code'=>$statusCode]);

    		    $user_info->square_payment_user_subscription_error = serialize(['errors'=>$errors,'error_code'=>$statusCode]);
    		    $user_info->save();
    		    return ['status'=>false,'msg'=>$errors[0]->getDetail(),'error_code'=>$statusCode];
    		}
    	} catch (ApiException $e) {
    		$statusCode = $apiResponse->getStatusCode();
    		Log::error('Create Subscription Error for '.$user->email,['errors'=>$e->getMessage(),'error_code'=>$statusCode]);

    		$user_info->square_payment_user_subscription_error = serialize(['errors'=>$e->getMessage(),'error_code'=>$statusCode]);
		    $user_info->save();
		    return ['status'=>false,'msg'=>$e->getMessage(),'error_code'=>$statusCode];
    		
    	} catch (\Exception $e) {
    		$statusCode = $apiResponse->getStatusCode();
    		Log::error('Create Subscription Error for '.$user->email,['errors'=>$e->getMessage(),'error_code'=>$statusCode]);

    		$user_info->square_payment_user_subscription_error = serialize(['errors'=>$e->getMessage(),'error_code'=>$statusCode]);
		    $user_info->save();
		    return ['status'=>false,'msg'=>$e->getMessage(),'error_code'=>$statusCode];
    	}
    }

    public function searchSquarePaymentCustomerByEmail($user){
    	$client = new SquareClient([
    	    'accessToken' => config('constant.squareAccessToken'),
    	    'environment' => config('constant.squareEnvironment'),
    	]);
    	$searchCustomerApi = $client->getCustomersApi();
    	$body = new \Square\Models\SearchCustomersRequest;
    	//$body->setCursor('cursor0');
    	$body->setLimit(1);
    	$body->setQuery(new \Square\Models\CustomerQuery);
    	$body->getQuery()->setFilter(new \Square\Models\CustomerFilter);
    	$body->getQuery()->getFilter()->setEmailAddress(new \Square\Models\CustomerTextFilter);
    	$body->getQuery()->getFilter()->getEmailAddress()->setExact($user->email);
    	//$body->getQuery()->getFilter()->getEmailAddress()->setFuzzy($email);
    	$apiResponse = $searchCustomerApi->searchCustomers($body);

    	if ($apiResponse->isSuccess()) {
    	    $searchCustomersResponse = $apiResponse->getResult()->getCustomers();
    	    if(empty($searchCustomersResponse)){
    	    	return ['status'=>false];
    	    }
    	    $user_info = UserInfo::where('user_id',$user->id)->first();
    	    
    	    if(!$user_info){
    	    	$user_info = new UserInfo;
    	    	$user_info->user_id = $user->id;
    	    }
    	    $user_info->square_payment_user_id = $searchCustomersResponse[0]->getId();
    	    $user_info->save();
    	    return ['status'=>true,'square_payment_user_id'=>$searchCustomersResponse[0]->getId()];
    	} else {
    		
    	    //$errors = $apiResponse->getErrors();
    	    return ['status'=>false];
    	}
    }

    public function cancelSubscription($subscriptionId){
    	$client = new SquareClient([
    	    'accessToken' => config('constant.squareAccessToken'),
    	    'environment' => config('constant.squareEnvironment'),
    	]);

    	$subscriptionsApi = $client->getSubscriptionsApi();

    	try {
    		$apiResponse = $subscriptionsApi->cancelSubscription($subscriptionId);

    		if ($apiResponse->isSuccess()) {
    		    $cancelSubscriptionResponse = $apiResponse->getResult();
    		    return ['status'=>true,'data'=>$cancelSubscriptionResponse->getSubscription()->getStatus()];
    		} else {
    			$errors = $apiResponse->getErrors();
    		    return ['status'=>false,'msg'=>$errors[0]->getDetail()];
    		}
    	} catch (ApiException $e) {
    		return ['status'=>false,'msg'=>$e->getMessage()];
    		
    	}catch (\Exception $e) {
    		return ['status'=>false,'msg'=>$e->getMessage()];
    	}
    }

    public function listPlans(){
    	$client = new SquareClient([
    	    'accessToken' => config('constant.squareAccessToken'),
    	    'environment' => config('constant.squareEnvironment'),
    	]);

    	$subscriptionsApi = $client->getCatalogApi();

    	try {
    		$apiResponse = $subscriptionsApi->listCatalog();

    		if ($apiResponse->isSuccess()) {
    		    $list = $apiResponse->getResult();
    		    dd($list);
    		} else {
    			$errors = $apiResponse->getErrors();
    		    return ['status'=>false,'msg'=>$errors[0]->getDetail()];
    		}
    	} catch (ApiException $e) {
    		return ['status'=>false,'msg'=>$e->getMessage()];
    		
    	}catch (\Exception $e) {
    		return ['status'=>false,'msg'=>$e->getMessage()];
    	}
    }

    public function searchSubscription($subscriptionId){
    	$client = new SquareClient([
    	    'accessToken' => config('constant.squareAccessToken'),
    	    'environment' => config('constant.squareEnvironment'),
    	]);

    	$subscriptionsApi = $client->getSubscriptionsApi();

    	try {
    		$apiResponse = $subscriptionsApi->retrieveSubscription($subscriptionId);

    		if ($apiResponse->isSuccess()) {
    		    $subscriptionResponse = $apiResponse->getResult();
    		    //dd($subscriptionResponse);
    		    return ['status'=>true,'subscriptionStatus'=>$subscriptionResponse->getSubscription()->getStatus(),'canceledDate'=>$subscriptionResponse->getSubscription()->getCanceledDate(),'chargedThroughDate'=>$subscriptionResponse->getSubscription()->getChargedThroughDate()];		    
    		} else {
    			$errors = $apiResponse->getErrors();
    		    return ['status'=>false,'msg'=>$errors[0]->getDetail()];
    		}
    	} catch (ApiException $e) {
    		return ['status'=>false,'msg'=>$e->getMessage()];
    		
    	}catch (\Exception $e) {
    		return ['status'=>false,'msg'=>$e->getMessage()];
    	}
    }
}