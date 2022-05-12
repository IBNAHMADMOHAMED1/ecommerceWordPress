<?php

if (defined('WC_VERSION') && version_compare(WC_VERSION, '2.3', '>=')) {
	add_filter('woocommerce_add_to_cart_fragments', 'blocksy_header_cart_item_fragment');
} else {
	add_filter('add_to_cart_fragments', 'blocksy_header_cart_item_fragment');
}

remove_action('woocommerce_cart_collaterals', 'woocommerce_cross_sell_display');

if (! function_exists('blocksy_header_cart_item_fragment')) {
	function blocksy_header_cart_item_fragment($fragments) {
		$maybe_header_id = null;

		if (
			isset($_GET['blocksy-header-id'])
			&&
			! empty($_GET['blocksy-header-id'])
		) {
			$maybe_header_id = $_GET['blocksy-header-id'];
		}

		$header = new Blocksy_Header_Builder_Render([
			'current_section_id' => $maybe_header_id
		]);

		$elements = new Blocksy_Header_Builder_Elements([
			'current_section_id' => $maybe_header_id
		]);

		$fragments['[data-device="desktop"] .ct-header-cart > .ct-cart-item'] = $header->render_single_item('cart', [
			'render_args' => [
				'only_item' => true
			]
		]);

		$fragments['[data-device="mobile"] .ct-header-cart > .ct-cart-item'] = $header->render_single_item('cart', [
			'render_args' => [
				'only_item' => true,
				'device' => 'mobile'
			]
		]);

		$fragments['.ct-header-cart > .ct-cart-content'] = $header->render_single_item('cart', [
			'render_args' => [
				'only_cart' => true
			]
		]);

		$fragments['#woo-cart-panel .ct-panel-content'] = $elements->render_cart_offcanvas([
			'has_container' => false
		]);

		return apply_filters('blocksy:woocommerce:cart-fragments', $fragments);
	}
}
