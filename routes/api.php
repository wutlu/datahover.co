<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function() {
	Route::post('tracks/list', 'TrackController@listApi')->name('api.track.list');
	Route::post('tracks/delete', 'TrackController@deleteApi')->name('api.track.delete');
	Route::post('tracks/create', 'TrackController@createApi')->name('api.track.create');

	Route::post('logs/list', 'LogController@listApi')->name('api.logs.list');

	Route::post('search', 'SearchController@searchApi')->name('api.search');
});
