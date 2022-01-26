<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\VerifyCsrfToken;

Route::get('/', 'HomeController@index')->name('index');
Route::post('try-search', 'HomeController@search')->name('index.search');

Route::get('search', 'SearchController@view')->name('search');

Route::prefix('gate')->group(function() {
	Route::get('/', 'UserController@gate')->name('user.gate');
	Route::get('redirect', 'UserController@gateRedirect')->name('user.gate.redirect');
	Route::get('callback', 'UserController@gateCallback')->name('user.gate.callback');
	Route::get('exit', 'UserController@gateExit')->name('user.gate.exit');
});

Route::prefix('user')->group(function() {
	Route::get('account', 'UserController@account')->name('user.account');
	Route::post('api/secret-generator', 'UserController@apiSecretGenerator')->name('user.api.secret_generator');

	Route::post('info', 'UserController@info')->name('user.info');
	Route::post('email-alerts', 'UserController@emailAlerts')->name('user.email_alerts');
	Route::post('logs/list', 'LogController@list')->name('user.logs.list');
});

Route::prefix('feed')->group(function() {
	Route::get('/', 'FeedController@view')->name('feed.index');
	Route::post('create', 'FeedController@create')->name('feed.create');
	Route::post('read', 'FeedController@read')->name('feed.read');
	Route::post('delete', 'FeedController@delete')->name('feed.delete');
	Route::post('list', 'FeedController@list')->name('feed.list');
});

Route::prefix('subscription')->group(function() {
	Route::get('/', 'SubscriptionController@view')->name('subscription.index');
	Route::post('details', 'SubscriptionController@details')->name('subscription.details');
	Route::post('cancel', 'SubscriptionController@cancel')->name('subscription.cancel');
	Route::post('start', 'SubscriptionController@start')->name('subscription.start');
});

Route::prefix('payments')->group(function() {
	Route::get('/', 'PaymentController@payments')->name('payments');
	Route::post('/', 'PaymentController@paymentsData');
	Route::get('status/{status?}', 'PaymentController@payment')->name('payment')->where('status', '(success|cancel)');
	Route::post('order', 'PaymentController@order')->name('payment.order')->withoutMiddleware([ VerifyCsrfToken::class ]);
	Route::get('invoice/{key}.pdf', 'PaymentController@invoice')->name('invoice');
});

Route::get('dashboard', 'HomeController@dashboard')->name('dashboard');
Route::get('log-console', 'LogController@view')->name('logs');
Route::get('track-list', 'TrackController@dashboard')->name('track.dashboard');

Route::get('faq', 'FaqController@view')->name('faq.index');
Route::post('faq', 'FaqController@list')->name('faq.list');

Route::get('{base}/{name}', 'HomeController@page')->name('page');
