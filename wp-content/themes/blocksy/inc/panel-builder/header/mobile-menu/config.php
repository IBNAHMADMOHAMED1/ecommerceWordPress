<?php

$config = [
	'name' => __('Mobile Menu', 'blocksy'),
	'typography_keys' => ['mobileMenuFont'],
	'devices' => ['mobile'],
	'allowed_in' => ['offcanvas'],
	'selective_refresh' => [
		'menu',
		'mobile_menu_type',
		'mobile_menu_interactive',
		'mobile_menu_toggle_shape'
	]
];

if (
	function_exists('blc_fs')
	&&
	blc_fs()->can_use_premium_code__premium_only()
) {
	$config['name'] = __('Mobile Menu 1', 'blocksy');
	unset($config['allowed_in']);
}

