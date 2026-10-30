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
if(version_compare(PHP_VERSION, '7.2.0', '>=')) {
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
}
Route::get('/', 'HomeController@index')->name('home');
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/about', 'HomeController@about')->name('about');
Route::get('/contact', 'HomeController@contact')->name('contact');
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

Route::resource('/issues', 'IssueController');
Route::resource('/articles', 'ArticleController');

Route::post('api/subscribe', 'EmailSubscription@subscribe');

Auth::routes();

Route::get('/profile', 'ProfileController@index')->name('profile');
Route::put('/profile', 'ProfileController@update')->name('profile.update');
Route::get('/account', 'ProfileController@account')->name('account');
Route::put('/account', 'ProfileController@updateAccount')->name('account.update');
Route::get('/account/subscriptions', 'ProfileController@subscriptions')->name('subscriptions');

Route::resource('/manuscripts', 'ManuscriptController');

//OAuth Routes
Route::get('auth/{provider}', 'Auth\LoginController@redirectToProvider');
Route::get('auth/{provider}/verify', 'Auth\LoginController@handleProviderCallback');
