<?php

return [
	'twitter' => [
		'name' => 'Twitter',
		'icon' => 'images/icons/twitter.png',
		'tracks' => [
			'keyword' => [ 'required', 'regex:/^[A-z0-9 ]{3,24}$/i' ],
		],
		'gains' => 'At the end of this job you store "Public Tweets"',
	],
	// 'facebook' => [
	// 	'name' => 'Facebook',
	// 	'icon' => 'images/icons/facebook.png',
	// 	'tracks' => [
	// 		'page' => [ 'required', 'regex:/^https?\:\/\/www\.facebook\.com\/(.*?)(\/|$)/i', 'active_url' ],
	// 	],
	// 	'gains' => 'At the end of this job you store "Public Facebook Posts"',
	// ],
	'youtube' => [
		'name' => 'YouTube',
		'icon' => 'images/icons/youtube.png',
		'tracks' => [
			'keyword' => [ 'required', 'regex:/^[a-zA-Z0-9 ]{3,24}$/i' ],
		],
		'gains' => 'At the end of this job you store "Public YouTube Video Text Contents and Public Video Comments"'
	],
	'news' => [
		'name' => 'News',
		'icon' => 'images/icons/news.png',
		'tracks' => [
			'page' => [ 'required', 'regex:/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9][a-z0-9-]{0,61}[a-z0-9]$/i' ],
		],
		'gains' => 'At the end of this job you store "Public News"'
	],
];
