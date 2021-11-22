<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'HomeController@index')->name('index');
Route::post('console', 'HomeController@console')->name('index.console');

Route::get('gate', 'UserController@gate')->name('user.gate');
Route::get('gate/redirect', 'UserController@gateRedirect')->name('user.gate.redirect');
Route::get('gate/callback', 'UserController@gateCallback')->name('user.gate.callback');
Route::get('gate/exit', 'UserController@gateExit')->name('user.gate.exit');

Route::prefix('user')->group(function() {
	Route::get('account', 'UserController@account')->name('user.account');
	Route::post('api/secret-generator', 'UserController@apiSecretGenerator')->name('user.api.secret_generator');
	Route::get('subscription', 'UserController@subscription')->name('user.subscription');
	Route::post('get-subscription', 'UserController@getSubscription')->name('user.get_subscription');
	Route::post('hide-info', 'UserController@hideInfo')->name('user.info.hide');
});

Route::get('dashboard', 'HomeController@dashboard')->name('dashboard');
Route::get('track-list', 'TrackController@dashboard')->name('track.dashboard');
Route::get('search', 'SearchController@dashboard')->name('search.dashboard');

Route::get('page/{page}', 'HomeController@page')->name('page');

Route::get('faq', 'FaqController@view')->name('faq.index');
Route::post('faq', 'FaqController@list')->name('faq.list');
