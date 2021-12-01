<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\VerifyCsrfToken;

Route::get('/', 'HomeController@index')->name('index');
Route::post('console', 'HomeController@console')->name('index.console');

Route::prefix('gate')->group(function() {
	Route::get('/', 'UserController@gate')->name('user.gate');
	Route::get('redirect', 'UserController@gateRedirect')->name('user.gate.redirect');
	Route::get('callback', 'UserController@gateCallback')->name('user.gate.callback');
	Route::get('exit', 'UserController@gateExit')->name('user.gate.exit');
});

Route::prefix('user')->group(function() {
	Route::get('account', 'UserController@account')->name('user.account');
	Route::post('api/secret-generator', 'UserController@apiSecretGenerator')->name('user.api.secret_generator');

	Route::post('hide-info', 'UserController@hideInfo')->name('user.hide_info');
	Route::post('email-alerts', 'UserController@emailAlerts')->name('user.email_alerts');
	Route::post('logs/list', 'LogController@list')->name('user.logs.list');
});

Route::prefix('subscription')->group(function() {
	Route::get('/', 'SubscriptionController@view')->name('subscription.index');
	Route::post('details', 'SubscriptionController@details')->name('subscription.details');
	Route::post('cancel', 'SubscriptionController@cancel')->name('subscription.cancel');
	Route::post('start', 'SubscriptionController@start')->name('subscription.start');
	Route::post('order', 'SubscriptionController@order')->name('subscription.order')->withoutMiddleware([ VerifyCsrfToken::class ]);

	Route::get('payment/{status?}', 'SubscriptionController@payment')->name('subscription.payment')->where('status', '(success|cancel)');
	Route::get('payment-history', 'SubscriptionController@paymentHistory')->name('subscription.payment.history');
	Route::post('payment-history', 'SubscriptionController@paymentHistoryData');
});

Route::get('dashboard', 'HomeController@dashboard')->name('dashboard');
Route::get('track-list', 'TrackController@dashboard')->name('track.dashboard');
Route::get('search', 'SearchController@dashboard')->name('search.dashboard');

Route::get('faq', 'FaqController@view')->name('faq.index');
Route::post('faq', 'FaqController@list')->name('faq.list');

Route::get('{base}/{name}', 'HomeController@page')->name('page');
