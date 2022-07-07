<?php

$repeater_type        = 'row';
$default_text_align   = 'left';
$repeater_col_spacing = 'normal';


$options = array(
	'pages_options'   => array(
		'type'    => 'group',
		'heading' => __( 'Options' ),
		'options' => array(
			'style'   => array(
				'type'    => 'select',
				'heading' => __( 'Style' ),
				'default' => 'normal',
				'options' => require( get_template_directory() . '/inc/builder/shortcodes/values/box-layouts.php' ),
			),

			'pord' => array(
				'type'    => 'select',
				'heading' => 'Page or Destination',
				'default' => '',
				'param_name'=>'pord',
				'options' => array('Page'=>'Page', 'Destination'=>'Destination'),
			),

			'parent1'  => array(
				'type'    => 'select',
				'heading' => 'Parent1',
				'conditions'=>'pord !== "Destination"',
				'default' => '',
				'options' => ux_builder_get_page_parents(),
			),
			'parent2'  => array(
				'type'    => 'select',
				'heading' => 'Parent2',
				'conditions'=>'pord === "Destination"',
				'default' => '',
				'options' => ux_builder_get_destination_parents(),
			), 
			

			'orderby' => array(
				'type'    => 'select',
				'heading' => __( 'Order By' ),
				'default' => 'menu_order',
				'options' => array(
					'post_title'    => 'Title',
					'post_date'     => 'Date',
					'menu_order'    => 'Menu Order',
					'post_modified' => 'Last Modified',
				),
			),
			'order'   => array(
				'type'    => 'select',
				'heading' => __( 'Order' ),
				'default' => 'asc',
				'options' => array(
					'asc'  => 'ASC',
					'desc' => 'DESC',
				),
			),
		),
	),
	'layout_options'        => require(get_template_directory() . '/inc/builder/shortcodes/commons/repeater-options.php' ),
	'layout_options_slider' => require( get_template_directory() . '/inc/builder/shortcodes/commons/repeater-slider.php' ),
);

$box_styles = require( get_template_directory() . '/inc/builder/shortcodes/commons/box-styles.php' );
$options    = array_merge( $options, $box_styles );

$advanced = array('advanced_options' => require( get_template_directory() . '/inc/builder/shortcodes/commons/advanced.php'));
$options = array_merge($options, $advanced);

add_ux_builder_shortcode( 'ux_pages',
	array(
		'name'      => __( 'Pages2', 'ux-builder' ),
		'category'  => __( 'Content' ),
		'thumbnail' => flatsome_ux_builder_thumbnail( 'pages' ),
		'scripts'   => array(
			'flatsome-masonry-js' => get_template_directory_uri() . '/assets/libs/packery.pkgd.min.js',
		),
		'presets'   => array(
			array(
				'name'    => __( 'Default' ),
				'content' => '[ux_pages]',
			),
		),
		'options'   => $options,
	)
);
