<?php

$config = [
	'name' => __('Cart', 'blocksy'),
	'enabled' => class_exists('WooCommerce'),
	'typography_keys' => ['cart_total_font'],
	'selective_refresh' => [
		'has_cart_dropdown',
		'mini_cart_type'
	]
];
