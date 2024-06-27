<?php
use Square\Environment;
return[
	'siteTitle'=>'Brand Nue',
	'defaultCurrency'=>'$',
	'defaultEmail'=>'info@brandnue.com',
	'defaultPassword'=>'user@brandnue',
	'adminPerPage'=>9,
	'achievement'=>[
		'level'=>5,
		'data'=>[
			1=>["id"=>1,"percentage"=> 20,"title"=>"Doing Great!"],
			2=>["id"=>2,"percentage"=> 40,"title"=>"Nice Work!"],
			3=>["id"=>3,"percentage"=> 60,"title"=>"Awesome job!"],
			4=>["id"=>4,"percentage"=> 80,"title"=>"Almost There!"],
			5=>["id"=>5,"percentage"=> 100,"title"=>"Congratulation!"],
		]
	],
	'status'=>[
		'active'=>'Y',
		'in_active'=>'N',
	],
	'role'=>[
		'admin'=>'ADMIN',
		'customer'=>'CUSTOMER',
	],
	'meals'=>[
		//['name'=>'Breakfast'],
		['name'=>'Lunch'],
		['name'=>'Snack'],
		['name'=>'Dinner'],
	],
	'fcm'=>[
		'url'=>'https://fcm.googleapis.com/fcm/send',
		'secretKey'=>'key=AAAAv-aZhac:APA91bFrCSQD2EQpEp1N82MZ3J8H5xh_K7mtbvNEFOVxNL8xSgoJXXue9oNI_8qTXUYX39QQobPUl6umio-C_tThbWqNcbKX4i676FhgHJP7YLhRYJX-n0XqMe6UoNguszrDFWko1v8s',
		'FCM_API_KEY'=>'AIzaSyADZnINeiDcYx8Ora6LpXeVsogrB-t3pCo',
		'FCM_AUTH_DOMAIN'=>"brandnue-bcfc6.firebaseapp.com",
		'FCM_PROJECT_ID'=>"brandnue-bcfc6",
		'FCM_STORAGE_BUCKET'=>"brandnue-bcfc6.appspot.com",
		'FCM_MESSAGIN_SENDER_ID'=>"824207574439",
		'FCM_APP_ID'=>"1:824207574439:web:8248d972c280cea0886282",
		'FCM_JSON'=>'fcm.json',
		'FCM_API_SERVER_KEY'=>'key=AAAAv-aZhac:APA91bFrCSQD2EQpEp1N82MZ3J8H5xh_K7mtbvNEFOVxNL8xSgoJXXue9oNI_8qTXUYX39QQobPUl6umio-C_tThbWqNcbKX4i676FhgHJP7YLhRYJX-n0XqMe6UoNguszrDFWko1v8s'
	],
	'elite_member_request_status'=>[
		'requested'=>'Requested',
		'meeting_not_scheduled'=>'Contacted but meeting not scheduled',
		'meeting_scheduled'=>'Contacted and meeting scheduled',
		'membership_not_accepted'=>'Elite membership not accepted',
		'elite_member'=>'Became Elite member',
	],
	'standard_member_id'=>1,
	'elite_member_id'=>3,
	'premium_member_id'=>2,
	'yearly_premium_member_id'=>5,
	'yearly_elite_member_id'=>4,
	'genders'=>[
		'Male','Female'
	],
	'squareAccessToken'=>'EAAAEJzOSWVmpV8favcrR6177KYb7d2EkFYcsdfwvD2JT7YmEb5gyQx2PUSdWANi',
	'squareEnvironment'=> Environment::PRODUCTION,
	'squareLocationId'=>'2YCBSPT7GXXDC',
	'square_application_id'=>'sq0idp-KAiJ0HnnsqISbGqC0Y3S2A'
];


//SANDBOX