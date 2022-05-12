<?php

add_filter(
	'do_shortcode_tag',
	function ($output, $tag, $attr, $m) {
		if ($tag === 'woocommerce_my_account') {
			$endpoint = WC()->query->get_current_endpoint();

			$unauthorized_class = 'ct-woo-unauthorized';

			if (
				$endpoint === 'lost-password'
				&&
				empty($_GET['show-reset-form'])
			) {
				$unauthorized_class .= ' ct-request-password-screen';
			}

			return str_replace(
				'class="woocommerce"',
				'class="woocommerce ' . (is_user_logged_in() ? 'ct-woo-account' : $unauthorized_class) . '"',
				$output
			);
		}

		return $output;
	},
	9999,
	4
);

add_action('woocommerce_before_account_navigation', function () {
	$username = '';

	if (get_theme_mod('has_account_page_name', 'no') === 'yes') {
		$username .= wp_get_current_user()->display_name;
	}

	if (get_theme_mod('has_account_page_quick_actions', 'no') === 'yes') {
		$account_details_url = wc_get_endpoint_url(
			'edit-account',
			'',
			get_permalink(get_option('woocommerce_myaccount_page_id'))
		);
		$username .= '<span><a href="' . $account_details_url . '">' . __('Account', 'blocksy') . '</a> <i>|</i> <a href="' . wc_logout_url() . '">' . __("Log out", 'blocksy') . '</a></span>';
	}

	if (! empty($username)) {
		$username = '<div class="ct-account-user-box">' . $username . '</div>';
	}

	if (get_theme_mod('has_account_page_avatar', 'no') === 'yes') {
		$avatar_size = intval(get_theme_mod(
			'account_page_avatar_size',
			'35'
		)) * 2;

		$username = blocksy_simple_image(
			get_avatar_url(
				get_current_user_id(),
				[
					'size' => $avatar_size
				]
			),
			[
				'tag_name' => 'span',

				'suffix' => 'static',
				'ratio_blocks' => false,

				'img_atts' => [
					'width' => $avatar_size / 2,
					'height' => $avatar_size / 2,
					'style' => 'height:' . (
						intval($avatar_size) / 2
					) . 'px',
				],
			]
		) . $username;
	}

	if (! empty($username)) {
		echo '<div class="ct-account-welcome">';
		echo $username;
		echo '</div>';
	}
});

