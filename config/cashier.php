<?php

return [
	'currency' => env('CASHIER_CURRENCY', 'usd'),
	'currency_symbol' => env('CASHIER_CURRENCY_SYMBOL', '$'),
	'currency_locale' => env('CASHIER_CURRENCY_LOCALE', 'en'),
	'logger' => env('CASHIER_LOGGER', 'stack'),
];
