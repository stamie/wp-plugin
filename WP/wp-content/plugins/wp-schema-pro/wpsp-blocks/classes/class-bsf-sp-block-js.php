<?php
/**
 * Schema Pro Block Helper.
 *
 * @package Schema Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'BSF_SP_Block_JS' ) ) {

	/**
	 * Class BSF_SP_Block_JS.
	 */
	class BSF_SP_Block_JS {

		/**
		 * Adds Google fonts for FAQ block.
		 *
		 * @since 1.15.0
		 * @param array $attr the blocks attr.
		 */
		public static function blocks_faq_gfont( $attr ) {

			$question_load_google_font = isset( $attr['questionloadGoogleFonts'] ) ? $attr['questionloadGoogleFonts'] : '';
			$question_font_family      = isset( $attr['questionFontFamily'] ) ? $attr['questionFontFamily'] : '';
			$question_font_weight      = isset( $attr['questionFontWeight'] ) ? $attr['questionFontWeight'] : '';
			$question_font_subset      = isset( $attr['questionFontSubset'] ) ? $attr['questionFontSubset'] : '';

			$answer_load_google_font = isset( $attr['answerloadGoogleFonts'] ) ? $attr['answerloadGoogleFonts'] : '';
			$answer_font_family      = isset( $attr['answerFontFamily'] ) ? $attr['answerFontFamily'] : '';
			$answer_font_weight      = isset( $attr['answerFontWeight'] ) ? $attr['answerFontWeight'] : '';
			$answer_font_subset      = isset( $attr['answerFontSubset'] ) ? $attr['answerFontSubset'] : '';

			BSF_SP_Helper::blocks_google_font( $question_load_google_font, $question_font_family, $question_font_weight, $question_font_subset );
			BSF_SP_Helper::blocks_google_font( $answer_load_google_font, $answer_font_family, $answer_font_weight, $answer_font_subset );

		}
	}
}
