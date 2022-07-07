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

	<div id="content" class="row">

		<h5 class="author-name uppercase pt-half" style="display:inline-block; width: auto; padding-top: 3px;">
			<?php echo esc_html( get_the_author_meta( 'display_name' ) ); ?>
		</h5>


		<span itemprop="datePublished" class="entry-date" style="display:inline-block">
			<time class="published" datetime="2013-06-13 12:00:00">&nbsp;-&nbsp;<?php echo get_the_date(); ?></time>
		</span>
		<span itemprop="dateModified" class="modate">
			<time class="updated" datetime="<?php echo get_the_date('Y-m-d'); ?>"><?php if( get_the_modified_date() != get_the_date() ) echo the_modified_date();?></time>
		</span>
	</div>
		
</div>

<?php do_action( 'flatsome_after_page' ); ?>

<?php 
}
get_footer(); 

//} 

 ?>