<?php
/**
 * Schema Pro Config.
 *
 * @package Schema Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'BSF_SP_Config' ) ) {

	/**
	 * Class BSF_SP_Config.
	 */
	class BSF_SP_Config {

		/**
		 * Block Attributes
		 *
		 * @var block_attributes
		 */
		public static $block_attributes = null;

		/**
		 * Block Assets
		 *
		 * @var block_attributes
		 */
		public static $block_assets = null;

		/**
		 * Get Widget List.
		 *
		 * @since 0.0.1
		 *
		 * @return array The Widget List.
		 */
		public static function get_block_attributes() {

			if ( null === self::$block_attributes ) {
				self::$block_attributes = array(
					'wpsp/faq'       => array(
						'slug'        => '',
						'title'       => __( 'FAQ - Schema Pro', 'wp-schema-pro' ),
						'description' => __( 'This block helps you add a FAQ section with inbuilt schema support.', 'wp-schema-pro' ),
						'default'     => true,
						'js_assets'   => array( 'wpsp-faq-js' ),
						'attributes'  => array(
							'block_id'                     => '',
							'layout'                       => 'accordion',
							'inactiveOtherItems'           => true,
							'expandFirstItem'              => false,
							'enableSchemaSupport'          => false,
							'align'                        => 'left',
							'enableSeparator'              => false,
							'rowsGap'                      => 10,
							'columnsGap'                   => 10,
							'boxBgColor'                   => '#FFFFFF',
							'boxPaddingTypeMobile'         => 'px',
							'boxPaddingTypeTablet'         => 'px',
							'boxPaddingTypeDesktop'        => 'px',
							'vBoxPaddingMobile'            => 10,
							'hBoxPaddingMobile'            => 10,
							'vBoxPaddingTablet'            => 10,
							'hBoxPaddingTablet'            => 10,
							'vBoxPaddingDesktop'           => 10,
							'hBoxPaddingDesktop'           => 10,
							'borderStyle'                  => 'solid',
							'borderWidth'                  => 1,
							'borderRadius'                 => 2,
							'borderColor'                  => '#D2D2D2',
							'questionTextColor'            => '#313131',
							'questionTextActiveColor'      => '#313131',
							'questionPaddingTypeDesktop'   => 'px',
							'vquestionPaddingMobile'       => 10,
							'vquestionPaddingTablet'       => 10,
							'vquestionPaddingDesktop'      => 10,
							'hquestionPaddingMobile'       => 10,
							'hquestionPaddingTablet'       => 10,
							'hquestionPaddingDesktop'      => 10,
							'answerTextColor'              => '#313131',
							'answerPaddingTypeDesktop'     => 'px',
							'vanswerPaddingMobile'         => 10,
							'vanswerPaddingTablet'         => 10,
							'vanswerPaddingDesktop'        => 10,
							'hanswerPaddingMobile'         => 10,
							'hanswerPaddingTablet'         => 10,
							'hanswerPaddingDesktop'        => 10,
							'iconColor'                    => '',
							'iconActiveColor'              => '',
							'gapBtwIconQUestion'           => 10,
							'questionloadGoogleFonts'      => false,
							'answerloadGoogleFonts'        => false,
							'questionFontFamily'           => 'Default',
							'questionFontWeight'           => '',
							'questionFontSubset'           => '',
							'questionFontSize'             => '',
							'questionFontSizeType'         => 'px',
							'questionFontSizeTablet'       => '',
							'questionFontSizeMobile'       => '',
							'questionLineHeight'           => '',
							'questionLineHeightType'       => 'em',
							'questionLineHeightTablet'     => '',
							'questionLineHeightMobile'     => '',
							'answerFontFamily'             => 'Default',
							'answerFontWeight'             => '',
							'answerFontSubset'             => '',
							'answerFontSize'               => '',
							'answerFontSizeType'           => 'px',
							'answerFontSizeTablet'         => '',
							'answerFontSizeMobile'         => '',
							'answerLineHeight'             => '',
							'answerLineHeightType'         => 'em',
							'answerLineHeightTablet'       => '',
							'answerLineHeightMobile'       => '',
							'icon'                         => 'fas fa-plus',
							'iconActive'                   => 'fas fa-minus',
							'iconAlign'                    => 'row',
							'iconSize'                     => 15,
							'iconSizeMobile'               => 15,
							'iconSizeTablet'               => 15,
							'iconSizeType'                 => 'px',
							'columns'                      => 2,
							'tcolumns'                     => 2,
							'mcolumns'                     => 1,
							'schema'                       => '',
							'enableToggle'                 => true,
							'questionLeftPaddingTablet'    => 10,
							'questionBottomPaddingTablet'  => 10,
							'questionLeftPaddingDesktop'   => 10,
							'questionBottomPaddingDesktop' => 10,
							'questionLeftPaddingMobile'    => 10,
							'questionBottomPaddingMobile'  => 10,
						),
					),
					'wpsp/faq-child' => array(
						'slug'        => '',
						'title'       => __( 'FAQ - Schema Pro Child', 'wp-schema-pro' ),
						'description' => __( 'This block helps you add single FAQ.', 'wp-schema-pro' ),
						'default'     => true,
						'attributes'  => array(
							'block_id'   => '',
							'question'   => '',
							'answer'     => '',
							'icon'       => 'fas fa-plus',
							'iconActive' => 'fas fa-minus',
							'layout'     => 'accordion',
						),
					),
				);
			}
			return self::$block_attributes;
		}

		/**
		 * Get Block Assets.
		 *
		 * @since 1.13.4
		 *
		 * @return array The Asset List.
		 */
		public static function get_block_assets() {

			if ( true === ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ) {
				$faq_js = BSF_AIOSRS_PRO_URI . 'wpsp-blocks/assets/js/faq.js';
			} else {
				$faq_js = BSF_AIOSRS_PRO_URI . 'wpsp-blocks/assets/min-js/faq.min.js';
			}

			if ( null === self::$block_assets ) {
				self::$block_assets = array(

					'wpsp-faq-js' => array(
						'src'        => $faq_js,
						'dep'        => array(),
						'skipEditor' => true,
					),
				);
			}
			return self::$block_assets;
		}
	}
}

