<?php

add_action('elementor/widget/before_render_content', function($widget) {
	if (! class_exists('ElementorPro\Modules\Woocommerce\Widgets\Cart')) {
		return;
	}

	if ($widget instanceof ElementorPro\Modules\Woocommerce\Widgets\Cart) {
		global $ct_skip_cart;
		$ct_skip_cart = true;
	}
}, 10 , 1);

add_filter('wc_get_template', function ($template, $template_name, $args, $template_path, $default_path) {
	if ($template_name !== 'cart/cart.php') {
		return $template;
	}

	global $ct_skip_cart;

	if ($ct_skip_cart) {
		$default_path = WC()->plugin_path() . '/templates/';
		return $default_path . $template_name;
	}

	return $template;
}, 10, 5);

add_filter(
	'woocommerce_format_sale_price',
	function ($price, $regular_price, $sale_price) {
		return '<span class="sale-price">' . $price . '</span>';
	},
	10,
	3
);

add_action(
	'woocommerce_before_quantity_input_field',
	function () {
		if (get_theme_mod('has_custom_quantity', 'yes') !== 'yes') {
			return;
		}

		echo '<span class="ct-increase"></span>';
		echo '<span class="ct-decrease"></span>';
	}
);

add_action(
	'woocommerce_before_main_content',
	function () {
		$prefix = blocksy_manager()->screen->get_prefix();

		if ($prefix === 'woo_categories' || $prefix === 'search') {
			/**
			 * Note to code reviewers: This line doesn't need to be escaped.
			 * Function blocksy_output_hero_section() used here escapes the value properly.
			 */
			echo blocksy_output_hero_section([
				'type' => 'type-2'
			]);
		}

		$attr = [
			'class' => 'ct-container'
		];

		if (blocksy_get_page_structure() === 'narrow') {
			$attr['class'] = 'ct-container-narrow';
		}

		if ($prefix === 'product') {
			if (blocksy_sidebar_position() === 'none') {
				$attr['class'] = 'ct-container-full';

				$attr['data-content'] = 'normal';

				if (blocksy_get_page_structure() === 'narrow') {
					$attr['data-content'] = 'narrow';
				}
			}

			echo blocksy_output_hero_section([
				'type' => 'type-2'
			]);
		}

		echo '<div ' . blocksy_attr_to_html($attr) . ' ' . wp_kses(blocksy_sidebar_position_attr(), []) . ' ' . blocksy_get_v_spacing() . '>';

		if (is_product()) {
			echo '<article class="post-' . get_the_ID() . '">';
		} else {
			echo '<section>';
		}

		if (
			$prefix === 'woo_categories'
			||
			$prefix === 'search'
			||
			$prefix === 'product'
		) {
			/**
			 * Note to code reviewers: This line doesn't need to be escaped.
			 * Function blocksy_output_hero_section() used here escapes the value properly.
			 */
			echo blocksy_output_hero_section([
				'type' => 'type-1'
			]);
		}
	}
);

add_action(
	'woocommerce_after_main_content',
	function () {
		if (is_product()) {
			echo '</article>';
		} else {
			echo '</section>';
		}

		get_sidebar();
		echo '</div>';
	}
);

add_action(
	'woocommerce_before_template_part',
	function ($template_name, $template_path, $located, $args) {
		global $blocksy_is_offcanvas_cart;

		if ($template_name === 'global/quantity-input.php') {
			ob_start();
		}

		if ($template_name === 'single-product/up-sells.php') {
			ob_start();
		}

		if ($template_name === 'single-product/related.php') {
			ob_start();
		}
	},
	10,
	4
);

add_action(
	'woocommerce_after_template_part',
	function ($template_name, $template_path, $located, $args) {
		global $blocksy_is_offcanvas_cart;

		if ($template_name === 'global/quantity-input.php') {
			$quantity = ob_get_clean();

			if (get_theme_mod('has_custom_quantity', 'yes') === 'yes') {
				echo str_replace(
					'class="quantity"',
					'class="quantity" data-type="' . get_theme_mod('quantity_type', 'type-2') . '"',
					$quantity
				);
			} else {
				echo $quantity;
			}
		}

		if ($template_name === 'single-product/up-sells.php') {
			$upsells = ob_get_clean();

			echo str_replace(
				'class="up-sells upsells products"',
				'class="up-sells upsells products ' . trim(
					blocksy_visibility_classes(
						get_theme_mod(
							'upsell_products_visibility',
							[
								'desktop' => true,
								'tablet' => false,
								'mobile' => false,
							]
						)
					)
				) . '"',
				$upsells
			);
		}

		if ($template_name === 'single-product/related.php') {
			$related = ob_get_clean();

			echo str_replace(
				'class="related products"',
				'class="related products ' . trim(
					blocksy_visibility_classes(
						get_theme_mod(
							'related_products_visibility',
							[
								'desktop' => true,
								'tablet' => false,
								'mobile' => false,
							]
						)
					)
				) . '"',
				$related
			);
		}
	},
	4,
	4
);

function blocksy_add_minicart_quantity_fields($html, $cart_item, $cart_item_key) {
	$_product = apply_filters(
		'woocommerce_cart_item_product',
		$cart_item['data'],
		$cart_item,
		$cart_item_key
	);
	$product_price = apply_filters(
		'woocommerce_cart_item_price',
		WC()->cart->get_product_price($cart_item['data']),
		$cart_item,
		$cart_item_key
	);

	if ($_product->is_sold_individually()) {
		$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
	} else {
		$product_quantity = trim(woocommerce_quantity_input(
			array(
				'input_name'   => "cart[{$cart_item_key}][qty]",
				'input_value'  => $cart_item['quantity'],
				'max_value'    => $_product->get_max_purchase_quantity(),
				'min_value'    => '0',
				'product_name' => $_product->get_name(),
			),
			$_product,
			false
		));
	}

	return '<div class="ct-product-actions">' . $product_quantity . '<span class="multiply-symbol">×</span>' . $product_price . '</div>';
}

if (! function_exists('blocksy_product_get_gallery_images')) {
	function blocksy_product_get_gallery_images($product) {
		$root_product = $product;

		if ($product->post_type === 'product_variation') {
			$root_product = wc_get_product( $product->get_parent_id() );
		}

		$thumb_id = apply_filters(
			'woocommerce_product_get_image_id',
			get_post_thumbnail_id($root_product->get_id()),
			$root_product
		);

		$thumb_id = get_post_thumbnail_id($root_product->get_id());

		$gallery_images = $root_product->get_gallery_image_ids();

		if ($thumb_id) {
			array_unshift($gallery_images, intval($thumb_id));
		} else {
			$gallery_images = [null];
		}

		if ($product->post_type === 'product_variation') {
			$variation_main_image = $product->get_image_id();

			$variation_values = get_post_meta(
				$product->get_id(),
				'blocksy_post_meta_options'
			);

			if (empty($variation_values)) {
				$variation_values = [[]];
			}

			$variation_values = $variation_values[0];

			$variation_gallery_images = blocksy_akg('images', $variation_values, []);
			$gallery_source = blocksy_akg('gallery_source', $variation_values, 'default');

			if ($gallery_source === 'default') {
				if (! in_array($variation_main_image, $gallery_images)) {
					$gallery_images[0] = $variation_main_image;
				}
			} else {
				$gallery_images = [$variation_main_image];

				foreach ($variation_gallery_images as $variation_gallery_image) {
					$gallery_images[] = $variation_gallery_image['attachment_id'];
				}
			}
		}

		return $gallery_images;
	}
}
