<?php
if(version_compare(PHP_VERSION, '7.2.0', '>=')) {
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
}

Route::get('dashboard', 'DashboardController@dashboard');
Route::get('/', 'DashboardController@redirect');

CRUD::resource('user', 'UserCrudController');
CRUD::resource('manuscript', 'ManuscriptCrudController');
CRUD::resource('author', 'AuthorCrudController');
CRUD::resource('subscriber', 'SubscriberCrudController');
CRUD::resource('issue', 'IssueCrudController');
CRUD::resource('article', 'ArticleCrudController');
