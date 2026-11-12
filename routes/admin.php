<?php

Route::get('dashboard', 'DashboardController@dashboard');
Route::get('/', 'DashboardController@redirect');

Route::crud('user', 'UserCrudController');
Route::crud('manuscript', 'ManuscriptCrudController');
Route::crud('subscriber', 'SubscriberCrudController');
Route::crud('issue', 'IssueCrudController');
Route::crud('article', 'ArticleCrudController');
