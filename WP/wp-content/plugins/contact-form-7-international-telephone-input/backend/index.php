<?php
if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}
add_action( 'wpcf7_init', 'wpcf7_add_form_tag_tel', 10, 0 );

function wpcf7_add_form_tag_tel() {
	wpcf7_add_form_tag( array( 'tel', 'tel*'),
		'wpcf7_tel_form_tag_handler', array( 'name-attr' => true ) );
}

function wpcf7_tel_form_tag_handler( $tag ) {
	if ( empty( $tag->name ) ) {
		return '';
	}

	$validation_error = wpcf7_get_validation_error( $tag->name );

	$class = wpcf7_form_controls_class( $tag->type );

	$class .= ' wpcf7-validates-as-tel';

	if ( $validation_error ) {
		$class .= ' wpcf7-not-valid';
	}

	$atts = array();

	$atts['class'] = $tag->get_class_option( $class );
	$atts['id'] = $tag->get_id_option();
	$atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );

	if ( $tag->has_option( 'readonly' ) ) {
		$atts['readonly'] = 'readonly';
	}

	if ( $tag->is_required() ) {
		$atts['aria-required'] = 'true';
	}
	//get otion
	$pre = (array) $tag->get_option( 'pre' );
	$onlyct = (array) $tag->get_option( 'onlyct' );
	$defcountry = (array) $tag->get_option( 'defcountry' );
	if ( $tag->has_option( 'auto' )) {
		$auto = true;
	}else{
		$auto = false;
	}
	if ( $tag->has_option( 'validation' )) {
		$validation = true;
	}else{
		$validation = false;
	}
	$content_attr = ' data-pre="'.implode("|",$pre).'" data-onlyct="'.implode("|",$onlyct).'" data-defcountry="'. implode("|",$defcountry) .'" data-auto="'. $auto.'" data-validation="'.$validation.'"';

	$atts['aria-invalid'] = $validation_error ? 'true' : 'false';

	$value = (string) reset( $tag->values );

	if ( $tag->has_option( 'placeholder' )
	or $tag->has_option( 'watermark' ) ) {
		$atts['placeholder'] = $value;
		$value = '';
	}

	$value = $tag->get_default_option( $value );

	$value = wpcf7_get_hangover( $tag->name, $value );

	$atts['value'] = $value;

	if ( wpcf7_support_html5() ) {
		$atts['type'] = 'tel';
	} else {
		$atts['type'] = 'text';
	}

	$atts['name'] = $tag->name;

	$atts = wpcf7_format_atts( $atts );
	$html = sprintf(
		'<span class="wpcf7-form-control-wrap %1$s"><input %2$s %3$s />%4$s</span>',
		sanitize_html_class( $tag->name ), $atts, $content_attr, $validation_error );

	return $html;
}


/* Validation filter */

add_filter( 'wpcf7_validate_tel', 'wpcf7_tel_validation_filter', 10, 2 );
add_filter( 'wpcf7_validate_tel*', 'wpcf7_tel_validation_filter', 10, 2 );


function wpcf7_tel_validation_filter( $result, $tag ) {
	$name = $tag->name;
	$value = isset( $_POST[$name] )
		? trim( strtr( (string) $_POST[$name], "\n", " " ) )
		: '';

	if ( $tag->is_required() and '' == $value ) {
		$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
	} 

	return $result;
}


/* Messages */

add_filter( 'wpcf7_messages', 'wpcf7_tel_messages', 10, 1 );

function wpcf7_tel_messages( $messages ) {
	return array_merge( $messages, array(
		'invalid_number' => array(
			'description' => __( "Number format that the sender entered is invalid", 'contact-form-7' ),
			'default' => __( "The number format is invalid.", 'contact-form-7' )
		),
	) );
}


/* Tag generator */

add_action( 'wpcf7_admin_init', 'wpcf7_add_tag_generator_tel', 18, 0 );

function wpcf7_add_tag_generator_tel() {
	$tag_generator = WPCF7_TagGenerator::get_instance();
	$tag_generator->add( 'tel', __( 'Telephone', 'contact-form-7' ),
		'wpcf7_tag_generator_tel' );
}

function wpcf7_tag_generator_tel( $contact_form, $args = '' ) {
	$args = wp_parse_args( $args, array() );
	$type = 'tel';

	$description = __( "Any options that take country codes should be, see %s.", 'contact-form-7' );

	$desc_link = wpcf7_link( __( 'https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2', 'contact-form-7' ), __( 'Number Fields', 'contact-form-7' ) );

?>
<div class="control-box">
<fieldset>
<legend><?php echo sprintf( esc_html( $description ), $desc_link ); ?></legend>

<table class="form-table">
<tbody>
	<tr>
	<th scope="row"><?php echo esc_html( __( 'Field type', 'contact-form-7' ) ); ?></th>
	<td>
		<fieldset>
		<legend class="screen-reader-text"><?php echo esc_html( __( 'Field type', 'contact-form-7' ) ); ?></legend>
		<label><input type="checkbox" name="required" /> <?php echo esc_html( __( 'Required field', 'contact-form-7' ) ); ?></label>
		</fieldset>
	</td>
	</tr>

	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'contact-form-7' ) ); ?></label></th>
	<td><input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /></td>
	</tr>

	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-pre' ); ?>"><?php echo esc_html( __( 'Preferred Countries', 'contact-form-7' ) ); ?></label></th>
	<td><input type="text" name="pre" class="option" id="<?php echo esc_attr( $args['content'] . '-pre' ); ?>" /><br />
		Specify the countries to appear at the top of the list. Default:<code>us|gb</code>
	</td>
	</tr>

	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-onlyct' ); ?>"><?php echo esc_html( __( 'Only Countries', 'contact-form-7' ) ); ?></label></th>
	<td><input type="text" name="onlyct" class="option" id="<?php echo esc_attr( $args['content'] . '-onlyct' ); ?>" /><br />
		In the dropdown, display only the countries you specify - see example.:<code>us|gb|bg</code>
	</td>
	</tr>

	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-auto' ); ?>"><?php echo esc_html( __( 'Automatically select Countries', 'contact-form-7' ) ); ?></label></th>
	<td><input type="checkbox" name="auto" checked="checked" class="option" />
		Automatically select the user's current country using an IP
	</td>
	</tr>
	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-validation' ); ?>"><?php echo esc_html( __( 'Validation', 'contact-form-7' ) ); ?></label></th>
	<td><input type="checkbox" name="validation" checked="checked" class="option" />
		Full validation, including specific error types
	</td>
	</tr>
	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-values' ); ?>"><?php echo esc_html( __( 'Default Country', 'contact-form-7' ) ); ?> </label></th>
	<td><input type="text" name="defcountry" class="option" id="<?php echo esc_attr( $args['content'] . '-defcountry' ); ?>" /><code>example: us --- default auto</code>
	</td>
	</tr>

	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php echo esc_html( __( 'Id attribute', 'contact-form-7' ) ); ?></label></th>
	<td><input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-id' ); ?>" /></td>
	</tr>

	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php echo esc_html( __( 'Class attribute', 'contact-form-7' ) ); ?></label></th>
	<td><input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-class' ); ?>" /></td>
	</tr>
</tbody>
</table>
</fieldset>
</div>

<div class="insert-box">
	<input type="text" name="<?php echo $type; ?>" class="tag code" readonly="readonly" onfocus="this.select()" />

	<div class="submitbox">
	<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7' ) ); ?>" />
	</div>

	<br class="clear" />

	<p class="description mail-tag"><label for="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>"><?php echo sprintf( esc_html( __( "To use the value input through this field in a mail field, you need to insert the corresponding mail-tag (%s) into the field on the Mail tab.", 'contact-form-7' ) ), '<strong><span class="mail-tag"></span></strong>' ); ?><input type="text" class="mail-tag code hidden" readonly="readonly" id="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>" /></label></p>
</div>
<?php
}
