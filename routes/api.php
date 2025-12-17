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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/get-question','Api\ExamController@getQuestion')->name('get-exams');

// User login register routes
Route::post('/login','Api\AuthController@login')->name('login');

Route::post('/signup','Api\AuthController@signup')->name('signup');
Route::post('/verify-otp','Api\AuthController@verifyotp')->name('verifyotp');
Route::post('/resend-otp','Api\AuthController@resendotp')->name('sendotp');
Route::post('/check-verification','Api\AuthController@checkVerification')->name('sendotp');

Route::get('/get-exams','Api\ExamController@get_exams')->name('get-exams');

Route::post('/forget','Api\AuthController@forget');
Route::post('/forget-verify-otp','Api\AuthController@verifyOtpForForget');
Route::post('/reset','Api\AuthController@reset');

Route::get('landing/index' , 'Api\HomeController@landing');
//Route::post('/resetpassword','Api\AuthController@resetpassword')->name('resetpassword');
//Route::post('/register-otp','Api\AuthController@registerOtp')->name('register-otp');
//Route::post('/register','Api\AuthController@register')->name('register');
//Route::post('/register-otp-using-whatsapp','Api\AuthController@registerOtpUsingWhatsapp')->name('register-otp-using-whatsapp');
Route::get('blogs/index' , 'Api\BlogController@index');
Route::get('blogs/{id}' , 'Api\BlogController@details');

Route::get('get-category/{id}','Api\CategoryController@getCategory')->name('get-subcategory');
Route::get('get-subcategories/{cat_id}','Api\CategoryController@getSubCategories')->name('get-subcategory');

Route::post('/contact','Api\HomeController@contact')->name('contactus');


Route::group(['namespace' => 'Api' , 'middleware' => ['auth.bearer','auth:api']],function () {
    Route::get('/home','HomeController@home');

    // get and update user data
    Route::get('/get-user-info','UserController@get_user_info');
    Route::post('/update-user-info','UserController@update_user_info');
    Route::post('/update-user-password','UserController@update_user_password');


    //not none
//    Route::get('/user-category-data','UserController@user_category_data')->name('user-category-data');
//    Route::get('/user-category-exams-data','UserController@user_category_exams_data')->name('user-category-exams-data');
//    Route::get('/user-exams-data','UserController@user_exams_data')->name('user-exams-data');
//    Route::get('/user-exams','UserController@user_exams')->name('user-exams');

    // category & subcategory routes
    Route::get('/get-category','CategoryController@get_category')->name('get-category');
    Route::get('/get-subcategory','CategoryController@get_subcategory')->name('get-subcategory');

    // exams routes

    Route::get('/get-exam-questions','ExamController@get_exam_questions')->name('get-exam-questions');
//    Route::post('/store-question-answer','ExamController@store_question_answer')->name('store-question-answer');
    Route::get('/user-exams-summary','ExamController@user_exams_summary')->name('user-exams-summary');
    Route::post('/change-user-exam-status','ExamController@change_user_exam_status')->name('change-user-exam-status');
    Route::get('/user-question-answer-history','ExamController@questionanswerhistory')->name('user-question-answer-history');

    Route::get('/get-exam-report','ExamController@getExamReport')->name('exam-report');
    Route::post('/user-exam-trials','ExamController@userExamTrials')->name('user-exam-trials');


    Route::post('/user-exam-pdf','ExamController@userExamPdf')->name('user-exam-pdf');

    Route::group(['prefix' => 'exams'] , function (){
        Route::get('/get','ExamTrialController@get');
        Route::get('{exam_id}/get','ExamTrialController@single');
        Route::get('/create','ExamTrialController@create');
        Route::get('/refresh-sections-and-topics','ExamTrialController@refreshSectionsAndTopics');
        Route::post('/store','ExamTrialController@store');
        Route::post('/reset','ExamTrialController@reset');

        Route::get('/get-questions','ExamTrialController@get_exam_questions');
        Route::post('{exam_id}/store-question-answer','ExamTrialController@store_question_answer');
        Route::post('{exam_id}/store-single-question-answer','ExamTrialController@store_single_question_answer');
        Route::get('{question_id}/get-question','ExamTrialController@getQuestion');

    });


    Route::group(['prefix' => 'packages' ] , function (){
        Route::get('/data','PackageController@data')->withoutMiddleware(['auth.bearer','auth:api']);
        Route::get('/index','PackageController@index')->withoutMiddleware(['auth.bearer','auth:api']);
        Route::get('/{id}/get','PackageController@getPackage')->withoutMiddleware(['auth.bearer','auth:api']);
        Route::get('/user-subscription','PackageController@userSubscription');
        Route::post('/subscribe','PackageController@subscribe'); // subscribe package route
        Route::post('/store-subscribe','PackageController@storeSubscribe')->name('api.store_subscriptions'); // subscribe package route

        Route::post('/checkout','PackageController@checkout')->name('checkout'); // payment package route
        Route::get('package-payment', 'PackageController@package_payment_status');
        Route::post('/check-coupon','CouponController@checkCoupon')->name('check-coupon');

    });

    // package routes


//    Route::get('/home-page-info','HomeController@home_page_info')->name('home-page-info');


    Route::post('/logout','UserController@logout')->name('logout');

    Route::post('/contactus','UserController@contactus')->name('contactus');

    /* Payment types */
    Route::get('/payment-types','PaymentTypeController@paymentTypes')->name('paymentTypes');
});
