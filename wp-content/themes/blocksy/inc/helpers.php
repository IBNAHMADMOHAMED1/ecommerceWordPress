<?php
/**
 * General purpose helpers
 *
 * @copyright 2019-present Creative Themes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @package Blocksy
 */

add_action(
	'dynamic_sidebar_before',
	function () {
		ob_start();
	}
);

add_action(
	'dynamic_sidebar_after',
	function () {
		$text = str_replace(
			'textwidget',
			'textwidget entry-content',
			ob_get_clean()
		);

		echo $text;
	}
);

if (! function_exists('blocksy_assert_args')) {
	function blocksy_assert_args($args, $fields = []) {
		foreach ($fields as $single_field) {
			if (
				! isset($args[$single_field])
				||
				!$args[$single_field]
			) {
				throw new Error($single_field . ' missing in args!');
			}
		}
	}
}

add_filter('widget_nav_menu_args', function ($nav_menu_args, $nav_menu, $args, $instance) {
	$nav_menu_args['menu_class'] = 'widget-menu';
	return $nav_menu_args;
}, 10, 4);

class Blocksy_Walker_Page extends Walker_Page {
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		if (
			isset( $args['item_spacing'] )
			&&
			'preserve' === $args['item_spacing']
		) {
			$t = "\t";
			$n = "\n";
		} else {
			$t = '';
			$n = '';
		}

		$indent  = str_repeat( $t, $depth );
		$output .= "{$n}{$indent}<ul class='sub-menu'>{$n}";
	}

	public function start_el( &$output, $page, $depth = 0, $args = array(), $current_page = 0 ) {
		parent::start_el(
			$output,
			$page,
			$depth,
			$args,
			$current_page
		);

		$output = str_replace(
			"</a><ul class='sub-menu'>",
			"~</a>^<ul class='sub-menu'>",
			$output
		);

		$output = str_replace(
			"menu-item-has-children\"><a",
			"menu-item-has-children\">^^<a",
			$output
		);

		$output = str_replace(
			"current-menu-item\"><a",
			"current-menu-item\">^^<a",
			$output
		);

		$output = preg_replace('/~~+/', '~', $output);
	}
}

function blocksy_sync_whole_page($args = []) {
	return array_merge(
		[
			'selector' => 'main#main',
			'container_inclusive' => true,
			'render' => function () {
				echo blocksy_replace_current_template();
			}
		],
		$args
	);
}

if (! function_exists('blocksy_get_with_percentage')) {
	function blocksy_get_with_percentage( $id, $value ) {
		$val = get_theme_mod( $id, $value );

		if ( strpos( $value, '%' ) !== false && is_numeric($val)) {
			$val .= '%';
		}

		return str_replace( '%%', '%', $val );
	}
}

/**
 * Link to menus editor for every empty menu.
 *
 * @param array  $args Menu args.
 */
if (! function_exists('blocksy_link_to_menu_editor')) {
	function blocksy_link_to_menu_editor($args) {
		if (! current_user_can('manage_options')) {
			return;
		}

		// see wp-includes/nav-menu-template.php for available arguments
		// phpcs:ignore WordPress.PHP.DontExtract.extract_extract
		extract($args);

		$output = '<a class="ct-create-menu" href="' . admin_url('nav-menus.php') . '" target="_blank">' . $before . __('You don\'t have a menu yet, please create one here &rarr;', 'blocksy') . $after . '</a>';

		if (! empty($container)) {
			$output = "<$container>$output</$container>";
		}

		if ($echo) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo wp_kses_post($output);
		}

		return $output;
	}
}

add_filter(
	'rest_post_query',
	function ($args, $request) {
		if (
			isset($request['post_type'])
			&&
			(strpos($request['post_type'], 'ct_forced') !== false)
		) {
			$post_type = explode(
				':',
				str_replace('ct_forced_', '', $request['post_type'])
			);

			if ($post_type[0] === 'any') {
				$post_type = array_diff(
					get_post_types(['public' => true]),
					['ct_content_block']
				);
			}

			$args = [
				'posts_per_page' => $args['posts_per_page'],
				'post_type' => $post_type,
				'paged' => 1,
				's' => $args['s'],
			];
		}

		if (
			isset($request['post_type'])
			&&
			(strpos($request['post_type'], 'ct_cpt') !== false)
		) {
			$next_args = [
				'posts_per_page' => $args['posts_per_page'],
				'post_type' => array_diff(
					get_post_types(['public' => true]),
					['post', 'page', 'attachment', 'ct_content_block']
				),
				'paged' => 1
			];

			if (isset($args['s'])) {
				$next_args['s'] = $args['s'];
			}

			$args = $next_args;
		}

		if (
			is_array($args['post_type'])
			&&
			in_array('product', $args['post_type'])
		) {
			if ('yes' === get_option('woocommerce_hide_out_of_stock_items')) {
				$meta_query = [];

				if (isset($args['meta_query'])) {
					$meta_query = $args['meta_query'];
				}

				$meta_query[] = array(
					'key'     => '_stock_status',
					'value'   => 'outofstock',
					'compare' => '!=',
				);

				$args['meta_query'] = $meta_query;
			}

			if (
				function_exists('wc_get_product_visibility_term_ids')
				&&
				count($args['post_type']) === 1
			) {
				$product_visibility_term_ids = wc_get_product_visibility_term_ids();

				$tax_query = [];

				if (isset($args['tax_query'])) {
					$tax_query = $args['tax_query'];
				}

				$tax_query['relation'] = 'AND';

				$tax_query[] = [
					[
						'taxonomy' => 'product_visibility',
						'field' => 'term_taxonomy_id',
						'terms' => $product_visibility_term_ids['exclude-from-search'],
						'operator' => 'NOT IN',
					]
				];

				$args['tax_query'] = $tax_query;
			}
		}

		return $args;
	},
	10,
	2
);

if (!is_admin()) {
	add_filter('pre_get_posts', function ($query) {
		if ($query->is_search && (
			is_search()
			||
			wp_doing_ajax()
		)) {
			if (!empty($_GET['ct_post_type'])) {
				$custom_post_types = blocksy_manager()->post_types->get_supported_post_types();

				if (function_exists('is_bbpress')) {
					$custom_post_types[] = 'forum';
					$custom_post_types[] = 'topic';
					$custom_post_types[] = 'reply';
				}

				$allowed_post_types = [];

				$post_types = explode(
					':',
					sanitize_text_field($_GET['ct_post_type'])
				);

				$known_cpts = ['post', 'page'];

				if (get_post_type_object('product')) {
					$known_cpts[] = 'product';
				}

				foreach ($post_types as $single_post_type) {
					if (
						in_array($single_post_type, $custom_post_types)
						||
						in_array($single_post_type, $known_cpts)
					) {
						$allowed_post_types[] = $single_post_type;
					}
				}

				$query->set('post_type', $allowed_post_types);

				if (in_array('product', $allowed_post_types)) {
					if ('yes' === get_option('woocommerce_hide_out_of_stock_items')) {
						$meta_query = [];

						if (! empty($query->get('meta_query'))) {
							$meta_query = $query->get('meta_query');
						}

						$meta_query[] = array(
							'key'     => '_stock_status',
							'value'   => 'outofstock',
							'compare' => '!=',
						);

						$query->set('meta_query', $meta_query);
					}
				}
			}
		}

		return $query;
	});
}

/**
 * Extract variable from a file.
 *
 * @param string $file_path path to file.
 * @param array  $_extract_variables variables to return.
 * @param array  $_set_variables variables to pass into the file.
 */
if (! function_exists('blocksy_get_variables_from_file')) {
	function blocksy_get_variables_from_file(
		$file_path,
		array $_extract_variables,
		array $_set_variables = array()
	) {
		// phpcs:ignore WordPress.PHP.DontExtract.extract_extract
		extract($_set_variables, EXTR_REFS);
		unset($_set_variables);

		if (is_file($file_path)) {
			require $file_path;
		}

		foreach ($_extract_variables as $variable_name => $default_value) {
			if (isset($$variable_name) ) {
				$_extract_variables[$variable_name] = $$variable_name;
			}
		}

		return $_extract_variables;
	}
}

/**
 * Transform ID to title.
 *
 * @param string $id initial ID.
 */
if (! function_exists('blocksy_id_to_title')) {
	function blocksy_id_to_title($id) {
		if (
			function_exists('mb_strtoupper')
			&&
			function_exists('mb_substr')
			&&
			function_exists('mb_strlen')
		) {
			$id = mb_strtoupper(mb_substr($id, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr(
				$id,
				1,
				mb_strlen($id, 'UTF-8'),
				'UTF-8'
			);
		} else {
			$id = strtoupper(substr($id, 0, 1)) . substr($id, 1, strlen($id));
		}

		return str_replace(['_', '-'], ' ', $id);
	}
}

/**
 * Extract a key from an array with defaults.
 *
 * @param string       $keys 'a/b/c' path.
 * @param array|object $array_or_object array to extract from.
 * @param null|mixed   $default_value defualt value.
 */
if (! function_exists('blocksy_default_akg')) {
	function blocksy_default_akg($keys, $array_or_object, $default_value) {
		return blocksy_akg($keys, $array_or_object, $default_value);
	}
}

/**
 * Recursively find a key's value in array
 *
 * @param string       $keys 'a/b/c' path.
 * @param array|object $array_or_object array to extract from.
 * @param null|mixed   $default_value defualt value.
 *
 * @return null|mixed
 */
if (! function_exists('blocksy_akg')) {
	function blocksy_akg($keys, $array_or_object, $default_value = null) {
		if (! is_array($keys)) {
			$keys = explode('/', (string) $keys);
		}

		$array_or_object = $array_or_object;
		$key_or_property = array_shift($keys);

		if (is_null($key_or_property)) {
			return $default_value;
		}

		$is_object = is_object($array_or_object);

		if ($is_object) {
			if (! property_exists($array_or_object, $key_or_property)) {
				return $default_value;
			}
		} else {
			if (! is_array($array_or_object) || ! array_key_exists($key_or_property, $array_or_object)) {
				return $default_value;
			}
		}

		if (isset($keys[0])) { // not used count() for performance reasons.
			if ($is_object) {
				return blocksy_akg($keys, $array_or_object->{$key_or_property}, $default_value);
			} else {
				return blocksy_akg($keys, $array_or_object[$key_or_property], $default_value);
			}
		} else {
			if ($is_object) {
				return $array_or_object->{$key_or_property};
			} else {
				return $array_or_object[ $key_or_property ];
			}
		}
	}
}

if (! function_exists('blocksy_akg_or_customizer')) {
	function blocksy_akg_or_customizer($key, $source, $default = null) {
		$source = wp_parse_args(
			$source,
			[
				'prefix' => '',

				// customizer | array
				'strategy' => 'customizer',
			]
		);

		if ($source['strategy'] !== 'customizer' && !is_array($source['strategy'])) {
			throw new Error(
				'strategy wrong value provided. Array or customizer is required.'
			);
		}

		if (! empty($source['prefix'])) {
			$source['prefix'] .= '_';
		}

		if ($source['strategy'] === 'customizer') {
			return get_theme_mod($source['prefix'] . $key, $default);
		}

		return blocksy_akg($source['prefix'] . $key, $source['strategy'], $default);
	}
}

if (! function_exists('blocksy_collect_and_return')) {
	function blocksy_collect_and_return($cb) {
		ob_start();

		if ($cb) {
			call_user_func($cb);
		}

		return ob_get_clean();
	}
}

/**
 * Generate a random ID.
 */
if (! function_exists('blocksy_rand_md5')) {
	function blocksy_rand_md5() {
		return md5(time() . '-' . uniqid(wp_rand(), true) . '-' . wp_rand());
	}
}

/**
 * Generate attributes string for html tag
 *
 * @param array $attr_array array('href' => '/', 'title' => 'Test').
 *
 * @return string 'href="/" title="Test"'
 */
if (! function_exists('blocksy_attr_to_html')) {
	function blocksy_attr_to_html(array $attr_array) {
		$html_attr = '';

		foreach ($attr_array as $attr_name => $attr_val) {
			if (false === $attr_val) {
				continue;
			}

			$html_attr .= $attr_name . '="' . $attr_val . '" ';
		}

		return $html_attr;
	}
}

/**
 * Generate html tag
 *
 * @param string      $tag Tag name.
 * @param array       $attr Tag attributes.
 * @param bool|string $end Append closing tag. Also accepts body content.
 *
 * @return string The tag's html
 */
if (! function_exists('blocksy_html_tag')) {
	function blocksy_html_tag($tag, $attr = [], $end = false) {
		$html = '<' . $tag . ' ' . blocksy_attr_to_html($attr);

		if (true === $end) {
			// <script></script>
			$html .= '></' . $tag . '>';
		} elseif (false === $end) {
			// <br/>
			$html .= '/>';
		} else {
			// <div>content</div>
			$html .= '>' . $end . '</' . $tag . '>';
		}

		return $html;
	}
}

/**
 * Safe render a view and return html
 * In view will be accessible only passed variables
 * Use this function to not include files directly and to not give access to current context variables (like $this)
 *
 * @param string $file_path File path.
 * @param array  $view_variables Variables to pass into the view.
 *
 * @return string HTML.
 */
if (! function_exists('blocksy_render_view')) {
	function blocksy_render_view(
		$file_path,
		$view_variables = [],
		$default_value = ''
	) {
		if (! is_file($file_path)) {
			return $default_value;
		}

		// phpcs:ignore WordPress.PHP.DontExtract.extract_extract
		extract($view_variables, EXTR_REFS);
		unset($view_variables);

		ob_start();
		require $file_path;

		return ob_get_clean();
	}
}

if (! function_exists('blocksy_get_wp_theme')) {
	function blocksy_get_wp_theme() {
		return apply_filters('blocksy_get_wp_theme', wp_get_theme());
	}
}

if (! function_exists('blocksy_get_wp_parent_theme')) {
	function blocksy_get_wp_parent_theme() {
		return apply_filters('blocksy_get_wp_theme', wp_get_theme(get_template()));
	}
}

function blocksy_current_url() {
	static $url = null;

	if ($url === null) {
		if (is_multisite() && !(defined('SUBDOMAIN_INSTALL') && SUBDOMAIN_INSTALL)) {
			switch_to_blog(1);
			$url = home_url();
			restore_current_blog();
		} else {
			$url = home_url();
		}

		//Remove the "//" before the domain name
		$url = ltrim(preg_replace('/^[^:]+:\/\//', '//', $url), '/');

		//Remove the ulr subdirectory in case it has one
		$split = explode('/', $url);

		//Remove end slash
		$url = rtrim($split[0], '/');

		$url .= '/' . ltrim(blocksy_akg('REQUEST_URI', $_SERVER, ''), '/');
		$url = set_url_scheme('//' . $url); // https fix
	}

	return $url;
}

/**
 * Treat non-posts home page as a simple page.
 */
if (! function_exists('blocksy_is_page')) {
	function blocksy_is_page($args = []) {
		$args = wp_parse_args(
			$args,
			[
				'shop_is_page' => true,
				'blog_is_page' => true
			]
		);

		static $static_result = null;

		if ($static_result !== null) {
		}

		$result = (
			(
				$args['blog_is_page']
				&&
				is_home()
				&&
				!is_front_page()
			) || is_page() || (
				$args['shop_is_page'] && function_exists('is_shop') && is_shop()
			) || is_attachment()
		);

		if ($result) {
			$post_id = get_the_ID();

			if (is_home() && !is_front_page()) {
				$post_id = get_option('page_for_posts');
			}

			if (function_exists('is_shop') && is_shop()) {
				$post_id = get_option('woocommerce_shop_page_id');
			}

			$static_result = $post_id;

			return $post_id;
		}

		$static_result = false;
		return false;
	}
}

if (! function_exists('blocksy_get_all_image_sizes')) {
	function blocksy_get_all_image_sizes() {
		$titles = [
			'thumbnail' => __('Thumbnail', 'blocksy'),
			'medium' => __('Medium', 'blocksy'),
			'medium_large' => __('Medium Large', 'blocksy'),
			'large' => __('Large', 'blocksy'),
			'full' => __('Full Size', 'blocksy'),
		];

		$all_sizes = get_intermediate_image_sizes();

		$result = [
			'full' => __('Full Size', 'blocksy')
		];

		foreach ($all_sizes as $single_size) {
			if (isset($titles[$single_size])) {
				$result[$single_size] = $titles[$single_size];
			} else {
				$result[$single_size] = $single_size;
			}
		}

		return $result;
	}
}

