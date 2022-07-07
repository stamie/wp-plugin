<?php
/**
 * The template for displaying all pages.
 *
 * @package flatsome
 */


if(flatsome_option('pages_template') != 'default') {
	
	// Get default template from theme options.
	get_template_part('page', flatsome_option('pages_template'));
	return;

} else {

get_header();
do_action( 'flatsome_before_page' ); ?>


<div class="entry-attachment makika">
       <?php $image_size = apply_filters( 'wporg_attachment_size', 'large' ); 
             echo wp_get_attachment_image( get_the_ID(), $image_size ); ?>
  
           <?php if ( has_excerpt() ) : ?>
        
           <div class="entry-caption">
                 <?php the_excerpt(); ?>
           </div><!-- .entry-caption -->
       <?php endif; ?>
</div><!-- .entry-attachment -->

<?php
do_action( 'flatsome_after_page' );
get_footer();

}

?>