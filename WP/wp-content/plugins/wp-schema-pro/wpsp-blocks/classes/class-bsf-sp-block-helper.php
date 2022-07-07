<?php
/**
 * Schema Pro Blocks Helper.
 *
 * @package Schema Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'BSF_SP_Block_Helper' ) ) {

	/**
	 * Class BSF_SP_Block_Helper.
	 */
	final class BSF_SP_Block_Helper {


		/**
		 * Get FAQ CSS.
		 *
		 * @since 2.2.0
		 * @param array  $attr The block attributes.
		 * @param string $id The selector ID.
		 */
		public static function get_faq_css( $attr, $id ) {

			$defaults = BSF_SP_Helper::$block_list['wpsp/faq']['attributes'];

			$attr = array_merge( $defaults, $attr );

			$icon_color        = $attr['iconColor'];
			$icon_active_color = $attr['iconActiveColor'];

			$attr['questionBottomPaddingDesktop'] = ( 10 === $attr['questionBottomPaddingDesktop'] && 10 !== $attr['vquestionPaddingDesktop'] ) ? $attr['vquestionPaddingDesktop'] : $attr['questionBottomPaddingDesktop'];

			$attr['questionLeftPaddingDesktop'] = ( 10 === $attr['questionLeftPaddingDesktop'] && 10 !== $attr['hquestionPaddingDesktop'] ) ? $attr['hquestionPaddingDesktop'] : $attr['questionLeftPaddingDesktop'];

			$attr['questionBottomPaddingTablet'] = ( 10 === $attr['questionBottomPaddingTablet'] && 10 !== $attr['vquestionPaddingTablet'] ) ? $attr['vquestionPaddingTablet'] : $attr['questionBottomPaddingTablet'];

			$attr['questionLeftPaddingTablet'] = ( 10 === $attr['questionLeftPaddingTablet'] && 10 !== $attr['hquestionPaddingTablet'] ) ? $attr['hquestionPaddingTablet'] : $attr['questionLeftPaddingTablet'];

			$attr['questionBottomPaddingMobile'] = ( 10 === $attr['questionBottomPaddingMobile'] && 10 !== $attr['vquestionPaddingMobile'] ) ? $attr['vquestionPaddingMobile'] : $attr['questionBottomPaddingMobile'];

			$attr['questionLeftPaddingMobile'] = ( 10 === $attr['questionLeftPaddingMobile'] && 10 !== $attr['hquestionPaddingMobile'] ) ? $attr['hquestionPaddingMobile'] : $attr['questionLeftPaddingMobile'];

			if ( ! isset( $attr['iconColor'] ) || '' === $attr['iconColor'] ) {

				$icon_color = $attr['questionTextColor'];
			}
			if ( ! isset( $attr['iconActiveColor'] ) || '' === $attr['iconActiveColor'] ) {

				$icon_active_color = $attr['questionTextActiveColor'];
			}

			$icon_size   = BSF_SP_Helper::get_css_value( $attr['iconSize'], $attr['iconSizeType'] );
			$t_icon_size = BSF_SP_Helper::get_css_value( $attr['iconSizeTablet'], $attr['iconSizeType'] );
			$m_icon_size = BSF_SP_Helper::get_css_value( $attr['iconSizeMobile'], $attr['iconSizeType'] );

			$selectors = array(
				' .wpsp-icon svg'                      => array(
					'width'     => $icon_size,
					'height'    => $icon_size,
					'font-size' => $icon_size,
					'fill'      => $icon_color,
				),
				' .wpsp-icon-active svg'               => array(
					'width'     => $icon_size,
					'height'    => $icon_size,
					'font-size' => $icon_size,
					'fill'      => $icon_active_color,
				),
				' .wpsp-faq-child__outer-wrap'         => array(
					'margin-bottom' => BSF_SP_Helper::get_css_value( $attr['rowsGap'], 'px' ),
				),
				' .wpsp-faq-item'                      => array(
					'background-color' => $attr['boxBgColor'],
					'border-style'     => $attr['borderStyle'],
					'border-width'     => BSF_SP_Helper::get_css_value( $attr['borderWidth'], 'px' ),
					'border-radius'    => BSF_SP_Helper::get_css_value( $attr['borderRadius'], 'px' ),
					'border-color'     => $attr['borderColor'],
				),
				' .wpsp-faq-item .wpsp-question'       => array(
					'color' => $attr['questionTextColor'],
				),
				' .wpsp-faq-item.wpsp-faq-item-active .wpsp-question' => array(
					'color' => $attr['questionTextActiveColor'],
				),
				' .wpsp-faq-item:hover .wpsp-question' => array(
					'color' => $attr['questionTextActiveColor'],
				),
				' .wpsp-faq-questions-button'          => array(
					'padding-top'    => BSF_SP_Helper::get_css_value( $attr['vquestionPaddingDesktop'], $attr['questionPaddingTypeDesktop'] ),
					'padding-bottom' => BSF_SP_Helper::get_css_value( $attr['questionBottomPaddingDesktop'], $attr['questionPaddingTypeDesktop'] ),
					'padding-right'  => BSF_SP_Helper::get_css_value( $attr['hquestionPaddingDesktop'], $attr['questionPaddingTypeDesktop'] ),
					'padding-left'   => BSF_SP_Helper::get_css_value( $attr['questionLeftPaddingDesktop'], $attr['questionPaddingTypeDesktop'] ),
				),
				' .wpsp-faq-content span'              => array(
					'margin-top'    => BSF_SP_Helper::get_css_value( $attr['vanswerPaddingDesktop'], $attr['answerPaddingTypeDesktop'] ),
					'margin-bottom' => BSF_SP_Helper::get_css_value( $attr['vanswerPaddingDesktop'], $attr['answerPaddingTypeDesktop'] ),
					'margin-right'  => BSF_SP_Helper::get_css_value( $attr['hanswerPaddingDesktop'], $attr['answerPaddingTypeDesktop'] ),
					'margin-left'   => BSF_SP_Helper::get_css_value( $attr['hanswerPaddingDesktop'], $attr['answerPaddingTypeDesktop'] ),
				),
				'.wpsp-faq-icon-row .wpsp-faq-item .wpsp-faq-icon-wrap' => array(
					'margin-right' => BSF_SP_Helper::get_css_value( $attr['gapBtwIconQUestion'], 'px' ),
				),
				'.wpsp-faq-icon-row-reverse .wpsp-faq-item .wpsp-faq-icon-wrap' => array(
					'margin-left' => BSF_SP_Helper::get_css_value( $attr['gapBtwIconQUestion'], 'px' ),
				),
				' .wpsp-faq-item:hover .wpsp-icon svg' => array(
					'fill' => $icon_active_color,
				),
				' .wpsp-faq-item .wpsp-faq-questions-button.wpsp-faq-questions' => array(
					'flex-direction' => $attr['iconAlign'],
				),
				' .wpsp-faq-item .wpsp-faq-content p'  => array(
					'color' => $attr['answerTextColor'],
				),
			);

			$t_selectors = array(
				' .wpsp-faq-questions-button' => array(
					'padding-top'    => BSF_SP_Helper::get_css_value( $attr['vquestionPaddingTablet'], $attr['questionPaddingTypeDesktop'] ),
					'padding-bottom' => BSF_SP_Helper::get_css_value( $attr['questionBottomPaddingTablet'], $attr['questionPaddingTypeDesktop'] ),
					'padding-right'  => BSF_SP_Helper::get_css_value( $attr['hquestionPaddingTablet'], $attr['questionPaddingTypeDesktop'] ),
					'padding-left'   => BSF_SP_Helper::get_css_value( $attr['questionLeftPaddingTablet'], $attr['questionPaddingTypeDesktop'] ),
				),
				' .wpsp-faq-content span'     => array(
					'margin-top'    => BSF_SP_Helper::get_css_value( $attr['vanswerPaddingTablet'], $attr['answerPaddingTypeDesktop'] ),
					'margin-bottom' => BSF_SP_Helper::get_css_value( $attr['vanswerPaddingTablet'], $attr['answerPaddingTypeDesktop'] ),
					'margin-right'  => BSF_SP_Helper::get_css_value( $attr['hanswerPaddingTablet'], $attr['answerPaddingTypeDesktop'] ),
					'margin-left'   => BSF_SP_Helper::get_css_value( $attr['hanswerPaddingTablet'], $attr['answerPaddingTypeDesktop'] ),
				),
				' .wpsp-icon svg'             => array(
					'width'     => $t_icon_size,
					'height'    => $t_icon_size,
					'font-size' => $t_icon_size,
				),
				' .wpsp-icon-active svg'      => array(
					'width'     => $t_icon_size,
					'height'    => $t_icon_size,
					'font-size' => $t_icon_size,
				),
			);
			$m_selectors = array(
				' .wpsp-faq-questions-button' => array(
					'padding-top'    => BSF_SP_Helper::get_css_value( $attr['vquestionPaddingMobile'], $attr['questionPaddingTypeDesktop'] ),
					'padding-bottom' => BSF_SP_Helper::get_css_value( $attr['questionBottomPaddingMobile'], $attr['questionPaddingTypeDesktop'] ),
					'padding-right'  => BSF_SP_Helper::get_css_value( $attr['hquestionPaddingMobile'], $attr['questionPaddingTypeDesktop'] ),
					'padding-left'   => BSF_SP_Helper::get_css_value( $attr['questionLeftPaddingMobile'], $attr['questionPaddingTypeDesktop'] ),
				),
				' .wpsp-faq-content span'     => array(
					'margin-top'    => BSF_SP_Helper::get_css_value( $attr['vanswerPaddingMobile'], $attr['answerPaddingTypeDesktop'] ),
					'margin-bottom' => BSF_SP_Helper::get_css_value( $attr['vanswerPaddingMobile'], $attr['answerPaddingTypeDesktop'] ),
					'margin-right'  => BSF_SP_Helper::get_css_value( $attr['hanswerPaddingMobile'], $attr['answerPaddingTypeDesktop'] ),
					'margin-left'   => BSF_SP_Helper::get_css_value( $attr['hanswerPaddingMobile'], $attr['answerPaddingTypeDesktop'] ),
				),
				' .wpsp-icon svg'             => array(
					'width'     => $m_icon_size,
					'height'    => $m_icon_size,
					'font-size' => $m_icon_size,
				),
				' .wpsp-icon-active svg'      => array(
					'width'     => $m_icon_size,
					'height'    => $m_icon_size,
					'font-size' => $m_icon_size,
				),
			);

			if ( 'accordion' === $attr['layout'] && true === $attr['inactiveOtherItems'] ) {

				$selectors[' .wp-block-wpsp-faq-child.wpsp-faq-child__outer-wrap .wpsp-faq-content '] = array(
					'display' => 'none',
				);
			}
			if ( 'accordion' === $attr['layout'] && true === $attr['expandFirstItem'] ) {

				$selectors[' .wpsp-faq__wrap.wpsp-buttons-layout-wrap > .wpsp-faq-child__outer-wrap:first-child > .wpsp-faq-child__wrapper .wpsp-faq-item.wpsp-faq-item-active .wpsp-faq-content '] = array(
					'display' => 'block',
				);
			}
			if ( true === $attr['enableSeparator'] ) {

				$selectors[' .wpsp-faq-child__outer-wrap .wpsp-faq-content '] = array(
					'border-style'        => 'solid',
					'border-top-color'    => $attr['borderColor'],
					'border-top-width'    => BSF_SP_Helper::get_css_value( $attr['borderWidth'], 'px' ),
					'border-right-width'  => '0px',
					'border-bottom-width' => '0px',
					'border-left-width'   => '0px',
				);
			}
			if ( 'grid' === $attr['layout'] ) {

				$selectors['.wpsp-faq-layout-grid .wpsp-faq__wrap .wpsp-faq-child__outer-wrap '] = array(
					'text-align' => $attr['align'],
				);
				$selectors['.wpsp-faq-layout-grid .wpsp-faq__wrap.wpsp-buttons-layout-wrap ']    = array(
					'grid-template-columns' => 'repeat(' . $attr['columns'] . ', 1fr)',
					'grid-column-gap'       => BSF_SP_Helper::get_css_value( $attr['columnsGap'], 'px' ),
					'grid-row-gap'          => BSF_SP_Helper::get_css_value( $attr['rowsGap'], 'px' ),
					'display'               => 'grid',
				);
				$t_selectors['.wpsp-faq-layout-grid .wpsp-faq__wrap.wpsp-buttons-layout-wrap ']  = array(
					'grid-template-columns' => 'repeat(' . $attr['tcolumns'] . ', 1fr)',
				);
				$m_selectors['.wpsp-faq-layout-grid .wpsp-faq__wrap.wpsp-buttons-layout-wrap ']  = array(
					'grid-template-columns' => 'repeat(' . $attr['mcolumns'] . ', 1fr)',
				);
			}

			$combined_selectors = array(
				'desktop' => $selectors,
				'tablet'  => $t_selectors,
				'mobile'  => $m_selectors,
			);

			$combined_selectors = BSF_SP_Helper::get_typography_css( $attr, 'question', ' .wpsp-faq-questions-button .wpsp-question', $combined_selectors );
			$combined_selectors = BSF_SP_Helper::get_typography_css( $attr, 'answer', ' .wpsp-faq-item .wpsp-faq-content p', $combined_selectors );

			return BSF_SP_Helper::generate_all_css( $combined_selectors, '.wpsp-block-' . $id );
		}
		/**
		 *  Prepare if class 'Schema_Pro_Loader' exist.
		 *  Kicking this off by calling 'get_instance()' method
		 */

	}
}

