<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'HomeController@index')->name('index');

Route::get('gate', 'UserController@gate')->name('user.gate');
Route::get('gate/redirect', 'UserController@gateRedirect')->name('user.gate.redirect');
Route::get('gate/callback', 'UserController@gateCallback')->name('user.gate.callback');
Route::get('gate/exit', 'UserController@gateExit')->name('user.gate.exit');

Route::get('user/account', 'UserController@account')->name('user.account');
Route::post('user/api/secret-generator', 'UserController@apiSecretGenerator')->name('user.api.secret_generator');

Route::get('dashboard', 'HomeController@dashboard')->name('dashboard');
Route::post('logs', 'LogController@logs')->name('logs');
Route::get('track-list', 'TrackController@dashboard')->name('track.dashboard');
