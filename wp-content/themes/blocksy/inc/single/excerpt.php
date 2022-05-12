<?php

defined('ABSPATH') || die("Don't run this file directly!");

add_filter(
	'excerpt_length',
	function ($length) {
		return 100;
	}
);

if (! function_exists('blocksy_trim_excerpt')) {
	function blocksy_trim_excerpt($excerpt, $length) {
		$text = $excerpt;

		if ($length !== 'original') {
			$match_result = [];

			preg_match(
				'/^[\p{Latin}\p{Common}\p{Greek}\p{Cyrillic}\p{Georgian}\p{Old_Turkic}]+$/u',
				$excerpt,
				$match_result
			);

			if (! empty($match_result)) {
				$text = wp_trim_words($excerpt, $length, '…');
			} else {
				if (function_exists('mb_strimwidth')) {
					$text = mb_strimwidth($excerpt, 0, $length, '…');
				} else {
					$text = wp_trim_words($excerpt, $length, '…');
				}
			}
		}

		foreach (wp_extract_urls($text) as $url) {
			$text = str_replace($url, '', $text);
		}

		$text = apply_filters('blocksy:excerpt:output', $text);

		echo apply_filters('the_excerpt', $text);
	}
}

add_filter(
	'excerpt_more',
	function () {
		return '…';
	}
);

