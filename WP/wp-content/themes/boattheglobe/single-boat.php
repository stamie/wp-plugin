<?php
/**
 * The template for displaying port pages.
 *
 * Template Post Type: port
 */



if(flatsome_option('pages_template') != 'default') {

	// Get default template from theme options.
	get_template_part('page', flatsome_option('pages_template'));

	return;

} else {

get_header(); ?>

<!--?php do_action( 'flatsome_before_page' ); ?-->

<div id="content" role="main" class="content-area port">


<?php  while ( have_posts() ) : the_post(); ?>

<?php  
	the_content();
	
 ?>


<?php  endwhile; // end of the loop. ?> 

		
</div>

<?php do_action( 'flatsome_after_page' ); ?>

<?php get_footer(); 

} 


 //get_footer(); 
 ?>

