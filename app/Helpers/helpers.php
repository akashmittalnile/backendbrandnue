<?php
use App\Library\GetFunction;
use App\Library\PostFunction;

if(!function_exists('status_array')){
	function status_array(){
		return[
			config('constant.status.active')=>'Active',
			config('constant.status.in_active')=>'Inactive'
		];
	}
}

// helper for calling unique slug
if (! function_exists('generateSlug')) {
    function generateSlug($string, $model,$field='slug'){
        if($model instanceof \Illuminate\Database\Eloquent\Model){
            $slug = Str::slug( $string );
            $i = 0;
            $params = array ();
            $params[$field] = $slug;
            while ($model->where($params)->count()){
                if (!preg_match ('/-{1}[0-9]+$/', $slug )){
                    $slug .= '-' . ++$i;
                }else{
                    $slug = preg_replace ('/[0-9]+$/', ++$i, $slug );
                }

                $params [$field] = $slug;
            }
            return $slug;
        }else{
            return false;
        }
    }
}

// helper for calling unique sku
if (! function_exists('generateSku')) {
    function generateSku($model,$field='sku',$string=''){
        if($model instanceof \Illuminate\Database\Eloquent\Model){
            if(empty($string)){
                $string = rand(100,999).'-'.rand(100,999).'-'.rand(100,999);
            }
            $sku = $string;
            $i = 0;
            $params = array ();
            $params[$field] = $sku;
            while ($model->where($params)->count()){
                if (!preg_match ('/-{1}[0-9]+$/', $sku )){
                    $sku .= '-' . ++$i;
                }else{
                    $sku = preg_replace ('/[0-9]+$/', ++$i, $sku );
                }

                $params [$field] = $sku;
            }
            return $sku;
        }else{
            return false;
        }
    }
}

if(!function_exists('dataResponse')){
    function dataResponse($data){
        return response()->json(['status'=>true,'msg'=>'Success','data'=>$data]);
    }
}

if(!function_exists('noDataResponse')){
    function noDataResponse(){
        return response()->json(['status'=>false,'msg'=>'No data found','data'=>[]]);
    }
}

if(!function_exists('successMsgResponse')){
    function successMsgResponse($msg){
        return response()->json(['status'=>true,'msg'=>$msg,'data'=>[]]);
    }
}

if(!function_exists('errorMsgResponse')){
    function errorMsgResponse($msg){
        return response()->json(['status'=>false,'msg'=>$msg,'data'=>[]]);
    }
}

if(!function_exists('dateFormat')){
    function dateFormat($date){
        return date('m/d/Y',strtotime($date));
    }
}

if(!function_exists('dbDateFormat')){
    function dbDateFormat($date){
        $date = \DateTime::createFromFormat("m/d/Y" , $date);
        return $date->format('Y-m-d');
    }
}

if(!function_exists('uploadImage')){
    function uploadImage($request,$file_name,$path='uploads'){
        return GetFunction::uploadImage($request,$file_name,$path);
    }
}

if(!function_exists('uniqueCode')){
    function uniqueCode(){
        $code = mt_rand(1000,9999);
        do{
            $code = mt_rand(1000,9999);
        }while(DB::table('users')->where('remember_token',$code)->first());
        
        return $code;
    }
}

if(! function_exists('encryptDecrypt')){
    
   function encryptDecrypt($action, $string) {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = 'This is my secret key';
        $secret_iv = 'This is my secret iv';
        // hash
        $key = hash('sha256', $secret_key);
        
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if ( $action == 'encrypt' ) {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if( $action == 'decrypt' ) {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        return $output;
    }

}

if(!function_exists('convertToTime')){
    function convertToTime($time,$init_time_format = 'H:i:s'){
        
        $time_format = "00:00:00";
        if(empty($time)){
            return $time_format;
        }
        try {
            return date($init_time_format,strtotime(strtolower($time)));
        } catch (\Exception $e) {
            return $time_format;
        }
        
    }
}

if(!function_exists('differenceInHours')){
    function differenceInHours($startdate,$enddate){

        if(!empty($startdate) && !empty($enddate)){
            try {
                $starttimestamp = strtotime($startdate);
                $endtimestamp = strtotime($enddate);
                $difference = abs($endtimestamp - $starttimestamp)/3600;
                
                return $difference;
            } catch (\Exception $e) {
                return 0 ;
            }
        }
        return 0;
    }
}

if(! function_exists('deleteImage')){    
    function deleteImage($image_path){
        if(!empty($image_path)){
            $image_path = str_replace('public/', '', $image_path);
        }

        if (File::exists(public_path($image_path))) {
            @unlink(public_path($image_path));
        }
    }
}

if(! function_exists('getAge')){    
    function getAge($dob){
        if(!empty($dob)){
            $from = new \DateTime($dob);
            $to   = new \DateTime('today');
            return $from->diff($to)->y;
        }
        return NULL;
    }
}

if(! function_exists('pushNotification')){    
    function pushNotification($data){
        return PostFunction::pushNotification($data);
    }
}

if(! function_exists('uploadMultipleImage')){    
    function uploadMultipleImage($file,$path){
        return PostFunction::uploadMultipleImage($file,$path);
    }
}

if(! function_exists('chatTotal')){    
    function chatTotal($admin_id){
        return GetFunction::chatTotal($admin_id);
    }
}

if(! function_exists('getRelativeFatMass')){    
    function getRelativeFatMass($tracking,$user){
        $gender = strtolower($user->gender);
        $inch = (!empty($user->height_inch) && $user->height_inch != 'null') ? $user->height_inch : 5;
        $feet = (!empty($user->height_feet)) ? $user->height_feet : 5;
        // dd([$feet, $inch]);
        $height = ($feet*12)+$inch; //inches
        $waist_measurement = ($tracking && !empty($tracking->current_day_waist_measurement) && $tracking->current_day_waist_measurement > 0)? $tracking->current_day_waist_measurement : $user->waist_measurement;
        if($gender=='male'){
            //Male has a waist circumference of 60 inches
            $divide = ($height/$waist_measurement??60); //$height/$waist_measurement
            return (float)sprintf('%0.2f',(64 - (20*($divide)))); // 64 – 20 x (Height / Waist circumference) 
        }else if($gender=='female'){
            //Female has a waist circumference of 28 inches
            $divide = ($height/$waist_measurement??28); //$height/$waist_measurement
            /*echo $height;
            echo $waist_measurement;
            dd($divide);*/
            return (float)sprintf('%0.2f',(76 - (20*($divide))));
        }
        return 0;
    }
}

if(! function_exists('eliteMemberRequestTotal')){    
    function eliteMemberRequestTotal(){
        return GetFunction::eliteMemberRequestTotal();
    }
}

if(! function_exists('digitalLibraryTotal')){    
    function digitalLibraryTotal(){
        return GetFunction::digitalLibraryTotal();
    }
}

if(! function_exists('getOunce')){    
    function getOunce($current_weight){
        // 1 lb means 1 pound
        // 16 ounces = 1 pound
        return ceil(intval($current_weight)*16);
    }
}

if(! function_exists('getTotalAmountBySubscription')){    
    function getTotalAmountBySubscription($id,$price){
        return GetFunction::getTotalAmountBySubscription($id,$price);
    }
}

if(! function_exists('priceFormat')){    
    function priceFormat($price){
        return config('constant.defaultCurrency').number_format($price,2);
    }
}

if(! function_exists('getLoginDetails')){    
    function getLoginDetails($user){
        return GetFunction::getLoginDetails($user);
    }
}

if(!function_exists('isPremium')){
    function isPremium($user){
        return GetFunction::isPremium($user);
    }
}

if(!function_exists('isElite')){
    function isElite($user){
        return GetFunction::isElite($user);
    }
}


if(!function_exists('getStaticSubscription')){
    function getStaticSubscription(){
        return array(
            ''=>'All',
            'S'=>'Standard',
            'P'=>'Premium',
            'E'=>'Elite',
        );
    }
}

if(!function_exists('premiumIds')){
    function premiumIds(){
        return [config('constant.premium_member_id'),config('constant.yearly_premium_member_id')];
    }
}

if(!function_exists('eliteIds')){
    function eliteIds(){
        return [config('constant.elite_member_id'),config('constant.yearly_elite_member_id')];
    }
}

if(!function_exists('standardIds')){
    function standardIds(){
        return [config('constant.standard_member_id')];
    }
}

function googleaccounttoken(){
    $credentialsFilePath = "fcm.json";
    $client = new \Google_Client();
    $client->setAuthConfig($credentialsFilePath);
    $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
    $apiurl = 'https://fcm.googleapis.com/v1/projects/'.config('constant.fcm.FCM_PROJECT_ID').'/messages:send';
    $client->refreshTokenWithAssertion();
    $token = $client->getAccessToken();
    $access_token = $token['access_token'];
    return $access_token ;
}


function sendnotificationusinghttpv1($data){
    // dd($data);
    try{
        $data['apiurl'] = 'https://fcm.googleapis.com/v1/projects/'.config('constant.fcm.FCM_PROJECT_ID').'/messages:send';
        $headers = [
            'Authorization: Bearer ' . googleaccounttoken(),
            'Content-Type:application/json'
        ];
        $data['headers'] = $headers;
        
        $fields = [
            'message' => [
                'token' => $data['token'],
                'notification' => [
                    'title' => $data['data']['title'],
                    'body' => $data['data']['description']
                ]
            ]
        ];
        $fields = json_encode($fields);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $data['apiurl']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $data['headers']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        $result = curl_exec($ch);
        curl_close($ch);
        return response(["status"=>true,"data"=>$result]);
    }catch(Exception $e){
        return response(["status"=>false,"message"=>$e->getMessage()]);
    }
    
}