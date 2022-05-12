<?php

/**
 * Page options.
 *
 * @copyright 2019-present Creative Themes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @package   Blocksy
 */

$maybe_taxonomy = blocksy_maybe_get_matching_taxonomy('page', false);

$has_matching_template = (
	function_exists('blc_get_content_block_that_matches')
	&&
	blc_get_content_block_that_matches([
		'template_type' => 'single',
		'match_conditions_strategy' => 'single_page'
	])
);

$options = [
	'page_section_options' => [
		'type' => 'ct-options',
		'setting' => [ 'transport' => 'postMessage' ],
		'inner-options' => [
			[
				$has_matching_template ? [] : blocksy_get_options('general/page-title', [
					'prefix' => 'single_page',
					'is_single' => true,
					'is_page' => true
				]),

				blocksy_rand_md5() => [
					'label' => __( 'Page Structure', 'blocksy' ),
					'type' => 'ct-title',
				],

				blocksy_rand_md5() => [
					'title' => __( 'General', 'blocksy' ),
					'type' => 'tab',
					'options' => [
						blocksy_get_options('single-elements/structure', [
							'default_structure' => 'type-4',
							'prefix' => 'single_page',
						]),
					],
				],

				blocksy_rand_md5() => [
					'title' => __( 'Design', 'blocksy' ),
					'type' => 'tab',
					'options' => [
						blocksy_get_options('single-elements/structure-design', [
							'prefix' => 'single_page',
						])
					],
				]
			],

			$has_matching_template ? [] : [
				blocksy_rand_md5() => [
					'type' => 'ct-title',
					'label' => __( 'Page Elements', 'blocksy' ),
				],

				blocksy_get_options('single-elements/featured-image', [
					'prefix' => 'single_page',
				]),

				blocksy_get_options('single-elements/post-share-box', [
					'prefix' => 'single_page'
				]),
			],

			$maybe_taxonomy && ! $has_matching_template ? [
				'single_page_has_post_tags' => [
					'label' => sprintf(
						__('Page %s', 'blocksy'),
						get_taxonomy($maybe_taxonomy)->label
					),
					'type' => 'ct-switch',
					'value' => 'no',
					'sync' => blocksy_sync_single_post_container([
						'prefix' => 'single_page'
					]),
				],
			] : [],

			$has_matching_template ? [] : [
				blocksy_get_options('general/comments-single', [
					'prefix' => 'single_page',
				]),

				apply_filters(
					'blocksy_single_posts_end_customizer_options',
					[],
					'single_page'
				),
			]
		],
	],
];
