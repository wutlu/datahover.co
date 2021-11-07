<?php

use Illuminate\Support\Facades\Route;

Route::prefix('users')->group(function() {
	Route::get('/', 'UserController@view')->name('root.users');
	Route::post('read', 'UserController@read')->name('root.users.read');
	Route::post('update', 'UserController@update')->name('root.users.update');
	Route::post('delete', 'UserController@delete')->name('root.users.delete');
	Route::post('list', 'UserController@list')->name('root.users.list');
});

Route::prefix('all-tracks')->group(function() {
	Route::get('/', 'TrackController@view')->name('root.tracks');
	Route::post('read', 'TrackController@read')->name('root.tracks.read');
	Route::post('update', 'TrackController@update')->name('root.tracks.update');
	Route::post('delete', 'TrackController@delete')->name('root.tracks.delete');
	Route::post('list', 'TrackController@list')->name('root.tracks.list');
});

Route::prefix('proxies')->group(function() {
	Route::get('/', 'ProxyController@view')->name('root.proxies');
	Route::post('read', 'ProxyController@read')->name('root.proxies.read');
	Route::post('update', 'ProxyController@update')->name('root.proxies.update');
	Route::post('list', 'ProxyController@list')->name('root.proxies.list');
	Route::post('settings', 'ProxyController@settings')->name('root.proxies.settings');
});

Route::prefix('elasticsearch')->group(function() {
	Route::get('/', 'ElasticsearchController@view')->name('root.elasticsearch');
	Route::post('status/{status}', 'ElasticsearchController@status')->name('root.elasticsearch.status')->where('status', '(health|nodes|indices)');
});

Route::prefix('crawlers')->namespace('Crawlers')->group(function() {
	Route::prefix('twitter')->group(function() {
		Route::get('/', 'TwitterController@view')->name('crawlers.twitter');
		Route::post('tokens', 'TwitterController@tokens')->name('crawlers.twitter.tokens');
		Route::post('tokens/create', 'TwitterController@createToken')->name('crawlers.twitter.tokens.create');
		Route::post('tokens/delete', 'TwitterController@deleteToken')->name('crawlers.twitter.tokens.delete');
	});

	Route::prefix('facebook')->group(function() {
		Route::get('/', 'FacebookController@view')->name('crawlers.facebook');
	});

	Route::prefix('news')->group(function() {
		Route::get('/', 'NewsController@view')->name('crawlers.news');
	});

	Route::prefix('youtube')->group(function() {
		Route::get('/', 'YouTubeController@view')->name('crawlers.youtube');
	});
});

Route::post('option', 'OptionController@update')->name('option.update');
