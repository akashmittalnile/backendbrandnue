<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Route::group(['namespace'=>'Auth'],function(){
    Route::get('login','LoginController@showLoginForm')->name('login');
    Route::get('logout','LoginController@logout')->name('logout');
    Route::post('login','LoginController@login')->name('post.login');

    Route::get('password/reset','ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/email','ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('password/reset/{token}','ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('/password/reset', 'ResetPasswordController@reset')->name('password.update');
});

Route::get('dashboard','DashboardController@dashboard')->name('admin.dashboard');
Route::get('dashboard/charts','DashboardController@getChartData')->name('admin.dashboard.charts');
Route::get('profile','DashboardController@profile')->name('admin.profile');
Route::post('profile','DashboardController@profileUpdate')->name('admin.profile.update');
Route::get('change-password','DashboardController@changePassword')->name('admin.change.password');
Route::post('change-password','DashboardController@changePasswordUpdate')->name('admin.change.password.update');

/*Digital library Routes Start*/
Route::get('digital-library','DigitalLibraryController@digitalLibrary')->name('admin.digital.library');
Route::get('digital-library/{id}/edit','DigitalLibraryController@digitalLibraryEdit')->name('admin.digital.library.edit');
Route::post('digital-library/{id}/edit','DigitalLibraryController@digitalLibraryUpdate')->name('admin.digital.library.update');
Route::delete('digital-library/{id}/delete','DigitalLibraryController@digitalLibraryDeleteImage')->name('admin.digital.library.deleteImage');

Route::get('digital-library/recipes','RecipeController@recipeList')->name('admin.recipe.list');
Route::get('digital-library/recipes/{status}','RecipeController@recipeListWithCondition')->name('admin.recipe.list,status');
Route::get('digital-library/add-recipe','RecipeController@createRecipe')->name('admin.recipe.create');
Route::post('digital-library/add-recipe','RecipeController@storeRecipe')->name('admin.recipe.store');
Route::post('digital-library/add-recipe/{recipe}','RecipeController@updateRecipe')->name('admin.recipe.update');
Route::get('digital-library/recipe/{recipe}/detail','RecipeController@recipeDetail')->name('admin.recipe.detail');
Route::get('digital-library/recipe/{recipe}/edit','RecipeController@recipeEdit')->name('admin.recipe.edit');
Route::post('ajax/{recipe}/upload','RecipeController@uploadFile')->name('admin.upload.file');
Route::delete('ajax/delete','RecipeController@deleteFile')->name('admin.delete.file');

Route::get('digital-library/recipe/{recipe}/delete','RecipeController@recipeDelete')->name('admin.recipe.delete');

Route::get('digital-library/instructional-templates','DigitalLibraryController@instructionalTemplates')->name('admin.instructional.template');
Route::get('digital-library/instructional-templates/create','DigitalLibraryController@createInstructionalTemplate')->name('admin.instructional.template.create');
Route::post('digital-library/instructional-templates/create','DigitalLibraryController@storeInstructionalTemplate');
Route::get('digital-library/instructional-templates/{template}/edit','DigitalLibraryController@editInstructionalTemplate')->name('admin.instructional.template.edit');
Route::post('digital-library/instructional-templates/{template}/edit','DigitalLibraryController@updateInstructionalTemplate');
Route::get('digital-library/instructional-templates/{template}/show','DigitalLibraryController@showInstructionalTemplate')->name('admin.instructional.template.show');
Route::delete('digital-library/instructional-templates/{template}/delete','DigitalLibraryController@deleteInstructionalTemplate')->name('admin.instructional.template.delete');

Route::get('digital-library/instructional-guides','DigitalLibraryController@instructionalGuides')->name('admin.instructional.guide');
Route::get('digital-library/instructional-guides/create','DigitalLibraryController@createInstructionalGuide')->name('admin.instructional.guide.create');
Route::post('digital-library/instructional-guides/create','DigitalLibraryController@storeInstructionalGuide');
Route::get('digital-library/instructional-guides/{guide}/edit','DigitalLibraryController@editInstructionalGuide')->name('admin.instructional.guide.edit');
Route::post('digital-library/instructional-guides/{guide}/edit','DigitalLibraryController@updateInstructionalGuide');
Route::get('digital-library/instructional-guides/{guide}/show','DigitalLibraryController@showInstructionalGuide')->name('admin.instructional.guide.show');
Route::delete('digital-library/instructional-guides/{guide}/delete','DigitalLibraryController@deleteInstructionalGuide')->name('admin.instructional.guide.delete');

Route::get('digital-library/instructional-videos','DigitalLibraryController@instructionalVideos')->name('admin.instructional.video');
Route::get('digital-library/instructional-videos/create','DigitalLibraryController@createInstructionalVideo')->name('admin.instructional.video.create');
Route::post('digital-library/instructional-videos/create','DigitalLibraryController@storeInstructionalVideo');
Route::get('digital-library/instructional-videos/{video}/edit','DigitalLibraryController@editInstructionalVideo')->name('admin.instructional.video.edit');
Route::post('digital-library/instructional-videos/{video}/edit','DigitalLibraryController@updateInstructionalVideo');
Route::get('digital-library/instructional-videos/{video}/show','DigitalLibraryController@showInstructionalVideo')->name('admin.instructional.video.show');
Route::delete('digital-library/instructional-videos/{video}/delete','DigitalLibraryController@deleteInstructionalVideo')->name('admin.instructional.video.delete');
Route::put('digital-library/instructional-videos/{video}/delete','DigitalLibraryController@deleteInstructionalVideoOnly')->name('admin.instructional.video.update');
Route::post('upload','CommonController@uploadFile')->name('upload');

/*Digital library Routes End*/

/*Create Admin Routes Start*/
Route::get('admins','UserController@adminList')->name('admin.admin.list');
Route::get('admins/create','UserController@adminCreate')->name('admin.admin.create');
Route::post('admins/create','UserController@adminStore')->name('admin.admin.store');
Route::get('admins/{user}/edit','UserController@adminEdit')->name('admin.admin.edit');
Route::post('admins/{user}/edit','UserController@adminUpdate')->name('admin.admin.update');
Route::get('admins/{user}/show','UserController@adminShow')->name('admin.admin.show');
Route::delete('admins/{user}/delete','UserController@adminDelete')->name('admin.admin.delete');

/*Customer Routes Start*/
Route::get('users','UserController@userList')->name('admin.user.list');
Route::get('users/{user}/edit','UserController@editUser')->name('admin.user.edit');
Route::post('users/{user}/edit','UserController@updateUser');
Route::delete('users/{user}','UserController@deleteUser')->name('admin.user.delete');
Route::patch('users/{user}/{status}','UserController@changeUserStatus')->name('admin.user.status');
Route::get('users/create','UserController@userCreate')->name('admin.user.create');
Route::post('users/create','UserController@userStore')->name('admin.user.store');
Route::get('users/{user}/show','UserController@userShow')->name('admin.user.show');
Route::put('users/{user}/password-reset','UserController@passwordReset')->name('admin.user.password-reset');
Route::get('users/{user}/daily-traking','UserController@userDailyTracking')->name('admin.user.daily-traking');
Route::get('users/{user}/daily-traking/notes','UserController@userDailyTrackingNote')->name('admin.user.daily-traking-note');
Route::post('users/{user}/daily-traking/notes','UserController@userDailyTrackingNoteStore');
Route::delete('users/{user}/daily-traking/notes/{note}','UserController@userDailyTrackingNoteDelete')->name('admin.user.daily-traking-note-delete');

Route::put('cancel-user-subscription/{user}/{subscription_id?}','UserController@cancelUserSubscription')->name('admin.user.cancel-active-subscription');

Route::get('customers/chat','UserController@getChatUserList')->name('admin.customer.chat');
Route::get('customers/chat/detail/{user}','UserController@getChatUserDetail')->name('admin.customer.chat.detail');
Route::get('customers/chat/{user}','UserController@ajaxChatList')->name('admin.customer.ajax.chat.list');
Route::post('customers/chat/{user}','UserController@postChat')->name('admin.customer.chat.post');
/*Customer Routes End*/


/*Notificatoins Routes start*/
Route::get('notifications','NotificationController@index')->name('admin.notification.list');
Route::get('notifications/create','NotificationController@create')->name('admin.notification.create');
Route::post('notifications/create','NotificationController@store')->name('admin.notification.store');
Route::get('notifications/{data}/edit','NotificationController@edit')->name('admin.notification.edit');
Route::post('notifications/{data}/edit','NotificationController@update')->name('admin.notification.update');
Route::get('notifications/{data}/show','NotificationController@show')->name('admin.notification.show');
Route::delete('notifications/{data}/delete','NotificationController@delete')->name('admin.notification.delete');
/*Notificatoins Routes end*/

/*Membership Routes start*/
Route::get('elite-membership-request','MembershipController@elitMembershipRequestList')->name('admin.membership.elite-membership-request-list');
Route::get('get-modal/{member}','MembershipController@getModal')->name('admin.membership.elite-member.modal');
Route::post('get-modal/{member}','MembershipController@changeElitMembershipRequest')->name('admin.membership.elite-member.post');

Route::get('memberships','MembershipController@getPlanList')->name('admin.membership.plans');
Route::get('memberships/{plan}','MembershipController@editPlan')->name('admin.membership.plans.edit');
Route::post('memberships/{plan}','MembershipController@updatePlan');
Route::get('payments','MembershipController@paymentList')->name('admin.membership.payments');
/*Membership Routes end*/


/*Admin Route start*/
Route::get('supplements','AdminController@supplementList')->name('admin.supplement.list');
Route::get('supplements/create','AdminController@supplementCreate')->name('admin.supplement.create');
Route::post('supplements/create','AdminController@supplementStore')->name('admin.supplement.store');
Route::get('supplements/{supplement}/edit','AdminController@supplementEdit')->name('admin.supplement.edit');
Route::patch('supplements/{supplement}/edit','AdminController@supplementUpdate')->name('admin.supplement.update');
Route::delete('supplements/{supplement}/delete','AdminController@supplementDelete')->name('admin.supplement.delete');

Route::get('exercises','AdminController@exerciseList')->name('admin.exercise.list');
Route::get('exercises/create','AdminController@exerciseCreate')->name('admin.exercise.create');
Route::post('exercises/create','AdminController@exerciseStore')->name('admin.exercise.store');
Route::get('exercises/{exercise}/edit','AdminController@exerciseEdit')->name('admin.exercise.edit');
Route::patch('exercises/{exercise}/edit','AdminController@exerciseUpdate')->name('admin.exercise.update');
Route::delete('exercises/{exercise}/delete','AdminController@exerciseDelete')->name('admin.exercise.delete');


Route::get('categories','AdminController@categoryList')->name('admin.category.list');
Route::get('categories/create','AdminController@categoryCreate')->name('admin.category.create');
Route::post('categories/create','AdminController@categoryStore')->name('admin.category.store');
Route::get('categories/{category}/edit','AdminController@categoryEdit')->name('admin.category.edit');
Route::patch('categories/{category}/edit','AdminController@categoryUpdate')->name('admin.category.update');
Route::delete('categories/{category}/delete','AdminController@categoryDelete')->name('admin.category.delete');
/*Admin Route end*/

Route::get('api-payment-web/{id}',function(\App\Models\SubscriptionPlan $id){
    return view('common.squre-payment',compact('id'));
})->name('api-web-view');

Route::get('api-page/{slug}','CommonController@pageContent')->name('api.page-content');
//Auth::routes();

Route::get('pay',function(){
    return view('admin.payments.payment-token');
});

/*Cron Job Routes*/

Route::get('check-subscription-expiration','CronController@checkSubscription');

Route::get('/clear', function() {
    $exitCode = Artisan::call('cache:clear');
    $exitCode = Artisan::call('config:clear');
    $exitCode = Artisan::call('config:cache');
    $exitCode = Artisan::call('view:clear');
    $exitCode = Artisan::call('optimize:clear');
    $exitCode = Artisan::call('route:clear');
    return '<h1>Cache facade value cleared</h1>';
});
Route::get('/migrate', function() {
    $exitCode = Artisan::call('migrate');
    return '<h1>migrate success</h1>';
});

Route::get('/seed', function() {
    //$exitCode = Artisan::call('db:seed');
    return '<h1>DB seed success</h1>';
});


#########################################
# 11-jul-22 
####################################################

Route::get('/make-migration', function() {
    $exitCode = Artisan::call('make:migration add_columan_to_subscribe_members_table');
    return '<h1>migration success</h1>';
});


Route::get('/call-migration', function() {
    $exitCode = Artisan::call('migrate --path=/database/migrations/2022_10_11_032943_add_columan_to_subscribe_members_table.php');
    return '<h1>call migration success</h1>';
});

Route::get('/route-list', function() {
    $exitCode = Artisan::call('route:list');
    dd($exitCode);
    return '<h1>call migration success</h1>';
});

# 05-dec-22
Route::get('transaction-list','AdminController@transactionList')->name('admin.transaction.list');
Route::get('logs','AdminController@showLogFile')->name('admin.log.file');
Route::get('clean-logs/{id}','AdminController@cleanLogFile')->name('admin.clean.logs');

Route::get('check/invoice','CronController@checkInvoice')->name('admin.check.invoice');
Route::get('check/membership','CronController@checkMembership')->name('admin.check.membership');