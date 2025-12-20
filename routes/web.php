<?php

use App\Models\Userpackges;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
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
//auth()->loginUsingId(1);

Route::get('/', function () {
    return redirect(route('login'));
});

Route::get('/pass', function () {
    echo Hash::make('12345678');
    die;
});
Route::get('/php', function () {
    echo phpinfo();
});

Auth::routes();
Route::get('logout', 'Auth\LoginController@logout')->name('logout');

Route::get('register', 'Auth\RegisterController@register');
Route::post('register', 'Auth\RegisterController@store')->name('register');

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/', 'HomeController@index')->name('home');

//Language Translation
Route::get('index/{locale}', 'HomeController@lang');

/* All Role Page */
Route::middleware(['auth'])->group(function () {
    Route::get('/change-password', 'PasswordController@index')->name('change-password');
    Route::post('/passwords/update', 'PasswordController@update')->name('passwords.update');

});



Route::middleware(['auth', 'adminAccess'])->group(function () {

    Route::group(['prefix' => 'user'], function () {
        Route::get('/index', 'UserController@index')->name('user');
        Route::get('/', 'UserController@index')->name('user');
        Route::get('/get', 'UserController@get')->name('user.list');
        Route::get('/detail', 'UserController@detail')->name('user.detail');
        Route::post('/addupdate', 'UserController@addupdate')->name('user.addupdate');
        Route::post('/delete', 'UserController@delete')->name('user.delete');
        Route::post('/verify', 'UserController@verify')->name('user.verify');
    });

    Route::group(['prefix' => 'instructors', 'as' => 'instructors.'], function () {
        Route::get('/', 'InstructorController@index')->name('index');
        Route::get('/get', 'InstructorController@get')->name('list');
        Route::get('/detail', 'InstructorController@detail')->name('detail');
        Route::post('/addupdate', 'InstructorController@addupdate')->name('addupdate');
        Route::post('/delete', 'InstructorController@delete')->name('delete');
    });

    Route::group(['prefix' => 'category'], function () {
        Route::get('/index', 'CategoryController@index')->name('category');
        Route::get('/', 'CategoryController@index')->name('category');
        Route::get('/get', 'CategoryController@get')->name('category.list');
        Route::get('/detail', 'CategoryController@detail')->name('category.detail');
        Route::post('/addupdate', 'CategoryController@addupdate')->name('category.addupdate');
        Route::post('/delete', 'CategoryController@delete')->name('category.delete');
        Route::post('/change-is-top', 'CategoryController@changeIsTop')->name('category.changeIsTop');
    });

    Route::group(['prefix' => 'subcategory'], function () {
        Route::get('/index', 'SubcategoryController@index')->name('subcategory');
        Route::get('/', 'SubcategoryController@index')->name('subcategory');
        Route::get('/get', 'SubcategoryController@get')->name('subcategory.list');
        Route::get('/detail', 'SubcategoryController@detail')->name('subcategory.detail');
        Route::post('/addupdate', 'SubcategoryController@addupdate')->name('subcategory.addupdate');
        Route::post('/delete', 'SubcategoryController@delete')->name('subcategory.delete');
    });

    /* Routes For Sub-Subcategory */
    Route::group(['prefix' => 'sub-subcategory'], function () {
        Route::get('/index', 'SubsubcategoryController@index')->name('sub-subcategory');
        Route::get('/', 'SubsubcategoryController@index')->name('sub-subcategory');
        Route::get('/get', 'SubsubcategoryController@get')->name('sub-subcategory.list');
        Route::get('/detail', 'SubsubcategoryController@detail')->name('sub-subcategory.detail');
        Route::post('/addupdate', 'SubsubcategoryController@addupdate')->name('sub-subcategory.addupdate');
        Route::post('/delete', 'SubsubcategoryController@delete')->name('sub-subcategory.delete');
    });

    /* Routes For topics */
    Route::group(['prefix' => 'topics', 'as' => 'topics.'], function () {
        Route::get('/index', 'TopicController@index')->name('index');
        Route::get('/data', 'TopicController@get')->name('data');
        Route::get('/detail', 'TopicController@detail')->name('detail');
        Route::post('/addupdate', 'TopicController@addupdate')->name('addupdate');
        Route::post('/delete', 'TopicController@delete')->name('delete');
    });
    /* Routes For exam section */
    Route::group(['prefix' => 'exam-section', 'as' => 'exam_section.'], function () {
        Route::get('/index', 'ExamSectionController@index')->name('index');
        Route::get('/data', 'ExamSectionController@get')->name('data');
        Route::get('/detail', 'ExamSectionController@detail')->name('detail');
        Route::post('/addupdate', 'ExamSectionController@addupdate')->name('addupdate');
        Route::post('/delete', 'ExamSectionController@delete')->name('delete');
    });

    Route::group(['prefix' => 'package'], function () {
        Route::get('/index', 'PackgesController@index')->name('package');
        Route::get('/', 'PackgesController@index')->name('package');
        Route::get('/get', 'PackgesController@get')->name('package.list');
        Route::get('/detail', 'PackgesController@detail')->name('package.detail');
        Route::post('/addupdate', 'PackgesController@addupdate')->name('package.addupdate');
        Route::post('/delete', 'PackgesController@delete')->name('package.delete');
    });

    Route::group(['prefix' => 'userpackage'], function () {
        Route::get('/index', 'UserpackgesController@index')->name('userpackage');
        Route::get('/', 'UserpackgesController@index')->name('userpackage');
        Route::get('/get', 'UserpackgesController@get')->name('userpackage.list');
        Route::get('/detail', 'UserpackgesController@detail')->name('userpackage.detail');
        Route::post('/addupdate', 'UserpackgesController@addupdate')->name('userpackage.addupdate');
        Route::post('/delete', 'UserpackgesController@delete')->name('userpackage.delete');
    });

    Route::group(['prefix' => 'exam'], function () {
        Route::get('/index', 'ExamController@index')->name('exam');
        Route::get('/', 'ExamController@index')->name('exam');
        Route::get('/get', 'ExamController@get')->name('exam.list');
        Route::get('/detail', 'ExamController@detail')->name('exam.detail');
        Route::post('/addupdate', 'ExamController@addupdate')->name('exam.addupdate');
        Route::post('/delete', 'ExamController@delete')->name('exam.delete');
        Route::get('/get-subcategories', 'ExamController@getSubcategories')->name('exam.subcat');
        Route::get('/get-sub-subcategories', 'ExamController@getSubSubcategories')->name('exam.sub-subcat');
        Route::post('/copy-exam', 'ExamController@copyExam')->name('exam.copy');
    });

    Route::group(['prefix' => 'question'], function () {
        Route::get('/index', 'QuestionController@index')->name('question');
        Route::get('/', 'QuestionController@index')->name('question');
        Route::get('/get', 'QuestionController@get')->name('question.list');
        Route::get('/detail', 'QuestionController@detail')->name('question.detail');
        Route::post('/addupdate', 'QuestionController@addupdate')->name('question.addupdate');
        Route::post('/delete', 'QuestionController@delete')->name('question.delete');

        Route::get('/create', 'QuestionController@create')->name('question.create');
        Route::get('/edit/{id}', 'QuestionController@edit')->name('question.edit');

        Route::get('/import', 'QuestionController@import')->name('question.import');
        Route::post('/importFile', 'QuestionController@importFile')->name('question.importFile');
        Route::get('/sorting', 'QuestionController@sorting')->name('question.sorting');
        Route::get('/export', 'QuestionController@export')->name('question.export');
        Route::post('/savesort', 'QuestionController@savesort')->name('question.savesort');
    });

// Route::group(['prefix' => 'questionanswer'], function () {
//         Route::get('/index', 'QuestionanswerController@index')->name('questionanswer');
//         Route::get('/', 'QuestionanswerController@index')->name('questionanswer');
//         Route::get('/get', 'QuestionanswerController@get')->name('questionanswer.list');
//         Route::get('/detail', 'QuestionanswerController@detail')->name('questionanswer.detail');
//         Route::post('/addupdate', 'QuestionanswerController@addupdate')->name('questionanswer.addupdate');
//         Route::post('/delete', 'QuestionanswerController@delete')->name('questionanswer.delete');
//     });

    Route::group(['prefix' => 'examquestion'], function () {
        Route::get('/index', 'ExamquestionController@index')->name('examquestion');
        Route::get('/', 'ExamquestionController@index')->name('examquestion');
        Route::get('/get', 'ExamquestionController@get')->name('examquestion.list');
        Route::get('/detail', 'ExamquestionController@detail')->name('examquestion.detail');
        Route::post('/addupdate', 'ExamquestionController@addupdate')->name('examquestion.addupdate');
        Route::post('/delete', 'ExamquestionController@delete')->name('examquestion.delete');
    });

    Route::group(['prefix' => 'userexam'], function () {
        Route::get('/index', 'UserexamController@index')->name('userexam');
        Route::get('/', 'UserexamController@index')->name('userexam');
        Route::get('/get', 'UserexamController@get')->name('userexam.list');
        Route::get('/detail', 'UserexamController@detail')->name('userexam.detail');
        Route::post('/addupdate', 'UserexamController@addupdate')->name('userexam.addupdate');
        Route::post('/delete', 'UserexamController@delete')->name('userexam.delete');
        Route::get('/download-exam-pdf', 'UserexamController@downloadPdf')->name('userexam.download.pdf');
    });

    Route::group(['prefix' => 'appinfo'], function () {
        Route::get('/index', 'AppinfoController@index')->name('appinfo');
        Route::get('/', 'AppinfoController@index')->name('appinfo');
        Route::get('/get', 'AppinfoController@get')->name('appinfo.list');
        Route::get('/detail', 'AppinfoController@detail')->name('appinfo.detail');
        Route::post('/addupdate', 'AppinfoController@addupdate')->name('appinfo.addupdate');
        Route::post('/delete', 'AppinfoController@delete')->name('appinfo.delete');
    });

    Route::group(['prefix' => 'notification'], function () {
        Route::get('/index', 'NotificationController@index')->name('notification');
        Route::get('/', 'NotificationController@index')->name('notification');
        Route::get('/get', 'NotificationController@get')->name('notification.list');
        Route::get('/detail', 'NotificationController@detail')->name('notification.detail');
        Route::post('/addupdate', 'NotificationController@addupdate')->name('notification.addupdate');
        Route::post('/delete', 'NotificationController@delete')->name('notification.delete');
    });

    /* Route For Discounts Code */
    Route::group(['prefix' => 'discountscode'], function () {
        Route::get('/', 'DiscountsCodeController@index')->name('discountscode');
        Route::get('/get', 'DiscountsCodeController@get')->name('discountscode.list');
        Route::post('/addupdate', 'DiscountsCodeController@addupdate')->name('discountscode.addupdate');
        Route::get('/detail', 'DiscountsCodeController@detail')->name('discountscode.detail');
        Route::post('/delete', 'DiscountsCodeController@delete')->name('discountscode.delete');
    });
Route::group(['prefix' => 'billing'], function () {
    Route::get('/invoices', 'BillingController@index')->name('billing.invoices');
    Route::get('/get', 'BillingController@get')->name('billing.list');
    Route::post('/send', 'BillingController@send')->name('billing.send');
    Route::post('/send-to-system', 'BillingController@sendToSystem')->name('billing.sendToSystem');
    Route::get('/{id}', 'BillingController@detail')->name('billing.detail');
    Route::delete('/{id}', 'BillingController@destroy')->name('billing.delete');
    

    
});

    /* Routes For Payments Type */
    Route::group(['prefix' => 'payment-types'], function () {
        Route::get('/', 'PaymentTypeController@index')->name('payment-types');
        Route::get('/get', 'PaymentTypeController@get')->name('payment-types.list');
        Route::post('/addupdate', 'PaymentTypeController@addupdate')->name('payment-types.addupdate');
        Route::get('/detail', 'PaymentTypeController@detail')->name('payment-types.detail');
        Route::post('/delete', 'PaymentTypeController@delete')->name('payment-types.delete');
    });


    Route::group(['prefix' => 'blogs', 'as' => 'blogs.'], function () {
        Route::get('/', 'BlogController@index')->name('index');
        Route::get('/get', 'BlogController@get')->name('list');

        Route::get('/create', 'BlogController@create')->name('create');
        Route::post('/create', 'BlogController@store')->name('store');

        Route::get('/edit/{id}', 'BlogController@edit')->name('edit');
        Route::post('/edit/{id}', 'BlogController@update')->name('update');

        Route::post('/delete', 'BlogController@delete')->name('delete');

    });

    Route::group(['prefix' => 'lab-value', 'as' => 'lab_value.'], function () {
        Route::get('/', 'LabValueController@create')->name('create');
        Route::post('/', 'LabValueController@store')->name('store');
    });

    Route::group(['prefix' => 'categories-export', 'as' => 'categories_export.'], function () {
        Route::get('/', 'CategoryController@export')->name('export');
    });


});
Route::get('/test-pdf', 'TestController@index');





Route::get('set-new-password/{token}', 'HomeController@set_new_password')->name('set_new_password');
Route::post('confirm-password', 'HomeController@confirm_new_password')->name('confirm_new_password');

// Clear all cache
Route::get('/clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('clear-compiled');
    Artisan::call('config:cache');
    dd("Cache is cleared");
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('payment/{id}', 'CheckoutController@index');
Route::get('payment-callback', 'CheckoutController@payment_callback');
Route::post('payment-callback', 'CheckoutController@payment_callback');
Route::get('/payment-status/success', 'CheckoutController@successPayment');
Route::get('/payment-status/error', 'CheckoutController@errorPayment');
Route::get('/payment-status/pending', 'CheckoutController@pendingPayment');


Route::post('check-efawatercom', [\App\Http\Controllers\HomeController::class, 'chech_efawatercom']);


Route::get('test12', function () {
    //    $sms = (new \App\Http\Controllers\Api\AuthController())->send_otp('97466999875', 1234);
//    dd($sms);
//    dd(('hi samer' , '972597466905'));
//    dd(sendTwilioWhatsapp('hi samer' , '972597466905'));
});
