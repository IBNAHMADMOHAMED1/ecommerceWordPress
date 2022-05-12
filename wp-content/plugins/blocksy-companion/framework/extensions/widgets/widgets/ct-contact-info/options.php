<?php
/**
 * Contact Info widget
 *
 * @copyright 2019-present Creative Themes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @package   Blocksy
 */


$options = [

	'title' => [
		'type' => 'text',
		'label' => __( 'Title', 'blocksy-companion' ),
		'field_attr' => [ 'id' => 'widget-title' ],
		'design' => 'inline',
		'value' => __( 'Contact Info', 'blocksy-companion' ),
		'disableRevertButton' => true,
	],

	// 'contact_text' => [
	// 	'label' => __( 'Text', 'blocksy-companion' ),
	// 	'type' => 'textarea',
	// 	'design' => 'inline',
	// 	'disableRevertButton' => true,
	// ],

	'contact_text' => [
		'label' => __( 'Text', 'blocksy-companion' ),
		'type' => 'wp-editor',
		'design' => 'inline',
		'desc' => __( 'You can add here some arbitrary HTML code.', 'blocksy-companion' ),
		'disableRevertButton' => true,
		'setting' => [ 'transport' => 'postMessage' ],

		'mediaButtons' => false,
		'tinymce' => [
			'toolbar1' => 'bold,italic,link,undo,redo',
		],
	],

	'contact_information' => [
		'label' => false,
		'type' => 'ct-layers',
		'manageable' => true,
		'value' => [
			[
				'id' => 'address',
				'enabled' => true,
				'title' => __('Address:', 'blocksy-companion'),
				'content' => 'Street Name, NY 38954',
			],

			[
				'id' => 'phone',
				'enabled' => true,
				'title' => __('Phone:', 'blocksy-companion'),
				'content' => '578-393-4937',
				'link' => 'tel:578-393-4937',
			],

			[
				'id' => 'mobile',
				'enabled' => true,
				'title' => __('Mobile:', 'blocksy-companion'),
				'content' => '578-393-4937',
				'link' => 'tel:578-393-4937',
			],

		],

		'settings' => [
			'address' => [
				'label' => __( 'Address', 'blocksy-companion' ),
				'options' => [
					'title' => [
						'type' => 'text',
						'label' => __('Title', 'blocksy-companion'),
						'value' => __('Address:', 'blocksy-companion'),
						'design' => 'inline',
					],

					'content' => [
						'type' => 'text',
						'label' => __('Content', 'blocksy-companion'),
						'value' => 'Street Name, NY 38954',
						'design' => 'inline',
					],

					'link' => [
						'type' => 'text',
						'label' => __('Link (optional)', 'blocksy-companion'),
						'design' => 'inline',
					]
				],

				'clone' => true
			],

			'phone' => [
				'label' => __( 'Phone', 'blocksy-companion' ),
				'clone' => true,
				'options' => [

					'title' => [
						'type' => 'text',
						'label' => __('Title', 'blocksy-companion'),
						'value' => __('Phone:', 'blocksy-companion'),
						'design' => 'inline',
					],

					'content' => [
						'type' => 'text',
						'label' => __('Content', 'blocksy-companion'),
						'value' => '578-393-4937',
						'design' => 'inline',
					],

					'link' => [
						'type' => 'text',
						'label' => __('Link (optional)', 'blocksy-companion'),
						'value' => 'tel:578-393-4937',
						'design' => 'inline',
					]

				]
			],

			'mobile' => [
				'label' => __( 'Mobile', 'blocksy-companion' ),
				'clone' => true,
				'options' => [
					'title' => [
						'type' => 'text',
						'label' => __('Title', 'blocksy-companion'),
						'value' => __('Mobile:', 'blocksy-companion'),
						'design' => 'inline',
					],

					'content' => [
						'type' => 'text',
						'label' => __('Content', 'blocksy-companion'),
						'value' => '578-393-4937',
						'design' => 'inline',
					],

					'link' => [
						'type' => 'text',
						'label' => __('Link (optional)', 'blocksy-companion'),
						'value' => 'tel:578-393-4937',
						'design' => 'inline',
					],

				]
			],

			'hours' => [
				'label' => __( 'Work Hours', 'blocksy-companion' ),
				'clone' => true,
				'options' => [
					'title' => [
						'type' => 'text',
						'label' => __('Title', 'blocksy-companion'),
						'value' => __('Opening hours', 'blocksy-companion'),
						'design' => 'inline',
					],

					'content' => [
						'type' => 'text',
						'label' => __('Content', 'blocksy-companion'),
						'value' => '9AM - 5PM',
						'design' => 'inline',
					],

					'link' => [
						'type' => 'text',
						'label' => __('Link (optional)', 'blocksy-companion'),
						'value' => '',
						'design' => 'inline',
					],

				]
			],

			'fax' => [
				'label' => __( 'Fax', 'blocksy-companion' ),
				'clone' => true,
				'options' => [
					'title' => [
						'type' => 'text',
						'label' => __('Title', 'blocksy-companion'),
						'value' => __('Fax:', 'blocksy-companion'),
						'design' => 'inline',
					],

					'content' => [
						'type' => 'text',
						'label' => __('Content', 'blocksy-companion'),
						'value' => '578-393-4937',
						'design' => 'inline',
					],

					'link' => [
						'type' => 'text',
						'label' => __('Link (optional)', 'blocksy-companion'),
						'value' => 'tel:578-393-4937',
						'design' => 'inline',
					],

				]
			],

			'email' => [
				'label' => __( 'Email', 'blocksy-companion' ),
				'clone' => true,
				'options' => [
					'title' => [
						'type' => 'text',
						'label' => __('Title', 'blocksy-companion'),
						'value' => __('Email:', 'blocksy-companion'),
						'design' => 'inline',
					],

					'content' => [
						'type' => 'text',
						'label' => __('Content', 'blocksy-companion'),
						'value' => 'contact@yourwebsite.com',
						'design' => 'inline',
					],

					'link' => [
						'type' => 'text',
						'label' => __('Link (optional)', 'blocksy-companion'),
						'value' => 'mailto:contact@yourwebsite.com',
						'design' => 'inline',
					],

				]
			],

			'website' => [
				'label' => __( 'Website', 'blocksy-companion' ),
				'clone' => true,
				'options' => [
					'title' => [
						'type' => 'text',
						'label' => __('Title', 'blocksy-companion'),
						'value' => __('Website:', 'blocksy-companion'),
						'design' => 'inline',
					],

					'content' => [
						'type' => 'text',
						'label' => __('Content', 'blocksy-companion'),
						'value' => 'creativethemes.com',
						'design' => 'inline',
					],

					'link' => [
						'type' => 'text',
						'label' => __('Link (optional)', 'blocksy-companion'),
						'value' => 'https://creativethemes.com',
						'design' => 'inline',
					],
				]
			],
		],
	],

	'contacts_icons_size' => [
		'label' => __( 'Icons Size', 'blocksy-companion' ),
		'type' => 'ct-radio',
		'value' => 'medium',
		'view' => 'text',
		'design' => 'block',
		'setting' => [ 'transport' => 'postMessage' ],
		'choices' => [
			'small' => __( 'Small', 'blocksy-companion' ),
			'medium' => __( 'Medium', 'blocksy-companion' ),
			'large' => __( 'Large', 'blocksy-companion' ),
		],
	],

	'contacts_icon_shape' => [
		'label' => __( 'Icons Shape Type', 'blocksy-companion' ),
		'type' => 'ct-radio',
		'value' => 'rounded',
		'view' => 'text',
		'design' => 'block',
		'setting' => [ 'transport' => 'postMessage' ],
		'choices' => [
			'simple' => __( 'None', 'blocksy-companion' ),
			'rounded' => __( 'Rounded', 'blocksy-companion' ),
			'square' => __( 'Square', 'blocksy-companion' ),
		],
	],

	blocksy_rand_md5() => [
		'type' => 'ct-condition',
		'condition' => [ 'contacts_icon_shape' => '!simple' ],
		'options' => [

			'contacts_icon_fill_type' => [
				'label' => __( 'Shape Fill Type', 'blocksy-companion' ),
				'type' => 'ct-radio',
				'value' => 'outline',
				'view' => 'text',
				'design' => 'block',
				'setting' => [ 'transport' => 'postMessage' ],
				'choices' => [
					'solid' => __( 'Solid', 'blocksy-companion' ),
					'outline' => __( 'Outline', 'blocksy-companion' ),
				],
			],

		],
	],

	'contact_link_target' => [
		'type'  => 'ct-switch',
		'label' => __( 'Open link in new tab', 'blocksy-companion' ),
		'value' => 'no',
		'design' => 'inline-full',
		'disableRevertButton' => true,
	],
];
