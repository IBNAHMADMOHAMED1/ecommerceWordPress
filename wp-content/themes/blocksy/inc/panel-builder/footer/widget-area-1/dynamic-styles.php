<?php

if (! isset($selector)) {
	$selector = '[data-column="widget-area-1"]';
}

blocksy_output_responsive([
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
		'selector' => $root_selector,
		'operation' => 'replace-last',
		'to_add' => $selector
	])),
	'variableName' => 'horizontal-alignment',
	'value' => blocksy_akg('horizontal_alignment', $atts, 'CT_CSS_SKIP_RULE'),
	'unit' => '',
]);

blocksy_output_responsive([
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
		'selector' => $root_selector,
		'operation' => 'replace-last',
		'to_add' => $selector
	])),
	'variableName' => 'vertical-alignment',
	'value' => blocksy_akg('vertical_alignment', $atts, 'CT_CSS_SKIP_RULE'),
	'unit' => '',
]);

// Font color
blocksy_output_colors([
	'value' => blocksy_akg('widget_area_colors', $atts),
	'default' => [
		'default' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword() ],
		'link_initial' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword() ],
		'link_hover' => [ 'color' => Blocksy_Css_Injector::get_skip_rule_keyword() ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => blocksy_assemble_selector(
				blocksy_mutate_selector([
					'selector' => blocksy_mutate_selector([
						'selector' => $root_selector,
						'operation' => 'replace-last',
						'to_add' => $selector
					]),
					'operation' => 'suffix',
					'to_add' => '.ct-widget'
				])
			),
			'variable' => 'color'
		],

		'link_initial' => [
			'selector' => blocksy_assemble_selector(
				blocksy_mutate_selector([
					'selector' => blocksy_mutate_selector([
						'selector' => $root_selector,
						'operation' => 'replace-last',
						'to_add' => $selector
					]),
					'operation' => 'suffix',
					'to_add' => '.ct-widget'
				])
			),
			'variable' => 'linkInitialColor'
		],

		'link_hover' => [
			'selector' => blocksy_assemble_selector(
				blocksy_mutate_selector([
					'selector' => blocksy_mutate_selector([
						'selector' => $root_selector,
						'operation' => 'replace-last',
						'to_add' => $selector
					]),
					'operation' => 'suffix',
					'to_add' => '.ct-widget'
				])
			),
			'variable' => 'linkHoverColor'
		],
	],
]);

blocksy_output_spacing([
	'css' => $css,
	'tablet_css' => $tablet_css,
	'mobile_css' => $mobile_css,
	'selector' => blocksy_assemble_selector(blocksy_mutate_selector([
		'selector' => $root_selector,
		'operation' => 'replace-last',
		'to_add' => $selector
	])),
	'important' => true,
	'value' => blocksy_default_akg( 'widget_area_margin', $atts,
		blocksy_spacing_value([
			'linked' => true,
		])
	)
]);