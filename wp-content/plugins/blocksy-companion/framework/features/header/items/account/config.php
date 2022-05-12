<?php

$config = [
	'name' => __('Account', 'blocksy'),

    'typography_keys' => ['account_label_font'],

	'selective_refresh' => [
		'account_state',
		'account_link',
		'loggedin_media',
		'account_loggedin_icon',
		'account_loggedin_icon_position',
		'loggedin_text',
		'login_account_action',
		'loggedout_account_custom_page',
		'logged_out_style',
		'accountHeaderIcon',
		'account_user_visibility'
	],

	'translation_keys' => [
		['key' => 'login_label'],
		['key' => 'account_custom_page']
	]
];

