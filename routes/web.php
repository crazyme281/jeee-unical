<?php

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
Route::get('/', 'HomeController@index')->name('home');
Route::get('/home', 'HomeController@index');
Route::get('/about', 'HomeController@about')->name('about');
Route::get('/contact', 'HomeController@contact')->name('contact');

Route::get('/_local/runtime', function () {
    abort_unless(app()->environment('local') && in_array(request()->ip(), ['127.0.0.1', '::1']), 404);
    return response()->json([
        'php' => PHP_VERSION,
        'build' => PHP_BUILD_DATE,
        'ini' => php_ini_loaded_file(),
        'upload_progress' => ini_get('session.upload_progress.enabled'),
        'project' => base_path(),
    ]);
});
Route::get('/submission-guideline', 'HomeController@guideline')->name('guideline');

Route::get('subscribe', 'HomeController@subscribe')->name('sub');
Route::get('/subscription', 'HomeController@subscription')->name('subscription');


Route::post('subscribe', 'SubscriptionController@subscribe')->name('sub');
Route::post('subscribe/cancel', 'SubscriptionController@cancel')->name('sub.cancel');

Route::get('/downloads/article/{id}', 'DownloadController@article')->name('downloads.article');
Route::get('/download/issues/{id}', 'DownloadController@issue')->name('downloads.issue');

//Search
Route::get('/search', 'SearchController@index')->name('search');
Route::get('/search?keyword={keyword}', 'SearchController@keyword')->name('search.keyword');

Route::resource('/issues', 'IssueController')->only(['index', 'show']);
Route::resource('/articles', 'ArticleController')->only(['index', 'show']);

Route::post('api/subscribe', 'EmailSubscription@subscribe');

Auth::routes();

Route::get('/profile', 'ProfileController@index')->name('profile');
Route::put('/profile', 'ProfileController@update')->name('profile.update');
Route::get('/account', 'ProfileController@account')->name('account');
Route::put('/account', 'ProfileController@updateAccount')->name('account.update');
Route::get('/account/subscriptions', 'ProfileController@subscriptions')->name('subscriptions');

Route::resource('/manuscripts', 'ManuscriptController')->only(['index', 'create', 'store', 'edit', 'update']);

//OAuth Routes
Route::get('auth/{provider}', 'Auth\LoginController@redirectToProvider');
Route::get('auth/{provider}/verify', 'Auth\LoginController@handleProviderCallback');
