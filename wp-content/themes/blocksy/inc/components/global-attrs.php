<?php

function blocksy_main_attr() {
	$attrs = [
		'id' => 'main',
		'class' => 'site-main'
	];

	if (blocksy_has_schema_org_markup()) {
		$attrs['class'] .= ' hfeed';
	}

	return blocksy_attr_to_html(array_merge(
		apply_filters('blocksy:main:attr', $attrs),
		blocksy_schema_org_definitions('creative_work', [
			'array' => true
		]))
	);
}

add_filter('body_class', function ($classes) {
	// if (get_theme_mod('has_passepartout', 'no') === 'yes') {
	// 	$classes[] = 'ct-passepartout';
	// };

	$classes[] = 'ct-loading';

	if (function_exists('is_product_category')) {
		if (is_product_category() || is_product_tag()) {
			$classes[] = 'woocommerce-archive';
		}
	}

	if (in_array('elementor-default', $classes)) {
		$current_template = blocksy_manager()->get_current_template();

		if (
			strpos($current_template, 'elementor') !== false
			&&
			is_singular()
			&&
			class_exists('\ElementorPro\Modules\ThemeBuilder\Module')
		) {
			$location_documents = \ElementorPro\Modules\ThemeBuilder\Module::instance()
				->get_conditions_manager()
				->get_documents_for_location(
					'single'
				);

			$first_key = key($location_documents);

			if (! empty($location_documents)) {
				$theme_document = $location_documents[$first_key];

				$document_page_template = $theme_document->get_settings('page_template');

				if (empty($document_page_template)) {
					$classes[] = 'ct-elementor-default-template';
				}
			}
		} else {
			if (
				! in_array('elementor-template-canvas', $classes)
				&&
				! in_array('elementor-template-full-width', $classes)
			) {
				$classes[] = 'ct-elementor-default-template';
			}
		}
	}

	return $classes;
}, 999999);

if (! function_exists('blocksy_body_attr')) {
	function blocksy_body_attr() {
		$attrs = [];

		if (get_theme_mod('has_passepartout', 'no') === 'yes') {
			$attrs['data-frame'] = 'default';
		};

		$attrs['data-prefix'] = blocksy_manager()->screen->get_prefix() . blocksy_manager()->screen->get_prefix_addition();

		$attrs['data-header'] = apply_filters(
			'blocksy:general:body-header-attr',
			substr(str_replace(
				'ct-custom-',
				'',
				blocksy_manager()->header_builder->get_current_section_id()
			), 0, 6)
		);

		$attrs['data-footer'] = substr(str_replace(
			'ct-custom-',
			'',
			blocksy_manager()->footer_builder->get_current_section_id()
		), 0, 6);

		$footer_render = new Blocksy_Footer_Builder_Render();
		$footer_atts = $footer_render->get_current_section()['settings'];

		$reveal_result = [];

		if (blocksy_default_akg(
			'has_reveal_effect/desktop',
			$footer_atts,
			false
		)) {
			$reveal_result[] = 'desktop';
		}

		if (blocksy_default_akg(
			'has_reveal_effect/tablet',
			$footer_atts,
			false
		)) {
			$reveal_result[] = 'tablet';
		}

		if (blocksy_default_akg(
			'has_reveal_effect/mobile',
			$footer_atts,
			false
		)) {
			$reveal_result[] = 'mobile';
		}

		if (count($reveal_result) > 0) {
			$attrs['data-footer'] .= ':reveal';
		}

		return blocksy_attr_to_html(array_merge([
			'data-link' => get_theme_mod('content_link_type', 'type-2'),
		], $attrs, blocksy_schema_org_definitions('single', ['array' => true])));
	}
}
