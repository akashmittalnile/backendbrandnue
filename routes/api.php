<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['namespace'=>'Api','prefix'=>'v1','middleware'=>'api'],function(){
    /*Customer Register route start*/
    Route::post('customer/register','RegisterController@customerStore');
    Route::post('customer/login','LoginController@customerLogin');
    
    /*Customer Register route end*/

    /*Customer Forgot Password route start*/
    Route::post('customer/reset-password','ForgotPasswordController@passwordResetRequest');
    Route::post('customer/reset-password/update','ForgotPasswordController@passwordUpdate');
    /*Customer Forgot Password route end*/

    /*Common route start*/
        
        Route::get('terms-conditions',function(){
            return response()->json(['status'=>true,'url'=>URL::to('/')]);
        });

        Route::post('push-no','CommonController@pushNo');
        Route::get('subscription-plans','CommonController@subscriptionPlans');
        Route::get('square-payment-url/{plan_id}','CommonController@getPaymentUrl');
    /*Common route end*/

    Route::group(['middleware'=>['auth.jwt']],function(){
        Route::get('logout','LoginController@logout');
        /*Common route after login start*/
        Route::get('supplements/list','CommonController@getSupplementList');
        Route::get('exercises/list','CommonController@getExerciseList');

        Route::get('search-meal','CommonController@getSearchMealList');
        /*Common route after login end*/

        /*Customer route after login start*/
        Route::post('change-password','CustomerController@passwordUpdate');
        Route::get('profile','CustomerController@getProfile');
        Route::get('get-current-user-detail','CustomerController@getCurrentProfile');
        Route::post('profile/update','CustomerController@profileUpdate');
        Route::get('daily-traking/list','CustomerController@getDailyTrakingList');
        Route::get('daily-traking-by-date','CustomerController@getDailyTrakingByDate');
        Route::post('daily-traking/store','CustomerController@dailyTrakingStore');
        Route::put('daily-traking/update/{tracking}','CustomerController@dailyTrakingUpdate');
        Route::get('home-page','CustomerController@getHome');
        Route::get('recipe/list/favourite','CustomerController@getFavouriteRecipeList');
        Route::post('recipe/add/favourite/{id}','CustomerController@recipeAddToFavourite');
        Route::get('add/meal','CustomerController@getMeal');
        Route::post('add/meal','CustomerController@storeAddMeal');
        Route::get('added/meal/list','CustomerController@getAddedMeal');
        Route::post('send-msg','CustomerController@chatMsgToAdmin');
        Route::post('send-msg/read','CustomerController@chatMsgRead');
        Route::get('notification-list','CustomerController@notificationList');
        Route::get('notification-list/{data}','CustomerController@notificationDetail');
        Route::get('chat-and-notification-count','CustomerController@chatAndNotificationCount');
        Route::get('tracking-7-days','CustomerController@downloadPdfFile');
        Route::post('cancel-subscription','CustomerController@cancelSubscription');
        Route::post('cancel-apple-subscription','CustomerController@cancelAppleSubscription');
        Route::get('filter-graph','CustomerController@filterGraph');

        Route::get('daily-traking/delete/{id}','CustomerController@deleteDailyTraking');
        /*Customer route after login end*/ 
        

        /*Recipe route after login Start*/
        Route::get('recipe/list','RecipeController@getRecipe');
        Route::get('recipe/detail/{id}','RecipeController@getRecipeDetails');
        Route::get('categories','RecipeController@categoryList');
        
        /*Recipe route after login end*/

        /*Admin Route Start*/
        Route::get('customer-list/chat','AdminController@customerChatList');
        Route::get('dashboard','AdminController@dashboard');
        Route::post('profile-image/update','AdminController@uploadProfileImage');
        /*Admin Route end*/

        /*DigitalLibrary Route Start*/
        Route::get('instructional/videos','DigitalLibrary@getInstructoinalVideos');
        Route::get('instructional/video/{video}','DigitalLibrary@getInstructoinalVideoDetail');
        Route::get('instructional/templates','DigitalLibrary@getInstructoinalTemplates');
        Route::get('instructional/template/{template}','DigitalLibrary@getInstructoinalTemplateDetail');
        Route::get('instructional/guides','DigitalLibrary@getInstructoinalGuides');
        Route::get('instructional/guide/{guide}','DigitalLibrary@getInstructoinalGuideDetail');
        /*DigitalLibrary Route end*/

        /*Member Route Start*/
        Route::get('elite-membership-request','MembershipController@getElitMembershipRequest');
        Route::put('elite-membership-request','MembershipController@activeElitMembershipRequest');
        Route::post('elite-membership-request','MembershipController@elitMembershipRequest');
        Route::post('subscribe-plan/{plan}','MembershipController@subscribeMembership');
        Route::post('apple-plan/{plan}','MembershipController@activateApplePurchasedPlan');
        /*Member Route end*/


        /* reset fast time*/
        Route::post('reset/time','CustomerController@resetFastTime');
    });

    
});
