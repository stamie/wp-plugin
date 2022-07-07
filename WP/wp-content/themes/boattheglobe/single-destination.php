<?php
/**
 * The template for displaying destination pages.
 *
 * Template Post Type: destination
 */


if(flatsome_option('pages_template') != 'default') {
	
	// Get default template from theme options.
	get_template_part('page', flatsome_option('pages_template'));
	return;

} else {

get_header(); ?>

<!--?php do_action( 'flatsome_before_page' ); ?-->

<!-- destinations header -->

  <div class="page-title blog-featured-title featured-title no-overflow postheaderimage">

	<div class="page-title-bg fill">
		<?php global $dynamic_featured_image;
		$featured_images = $dynamic_featured_image->get_featured_images( get_the_ID() );
		foreach($featured_images as $featured_image) { ?>
		<div class="title-bg fill bg-fill bg-top" style="background-image: url('<?php echo $featured_image['full']; ?>');" data-parallax-fade="true" data-parallax="-2" data-parallax-background data-parallax-container=".page-title"></div>
		<?php } ?>
  		<div class="title-overlay fill" style="background-color: rgba(0,0,0,.2)"></div>
  	</div>


  	<div class="page-title-inner container  flex-row  dark is-large" style="min-height: 400px">
  	 	<div class="flex-col flex-center text-center destination-header2">		
  			<?php get_template_part( 'template-parts/posts/partials/entry', 'titledestinations');  ?>
  	 	</div>
  	</div><!-- flex-row -->
  </div><!-- .page-title -->

<!-- destinations header end -->

<div id="content" role="main" class="content-area destinations">

		<?php while ( have_posts() ) : the_post(); ?>

			<?php the_content(); ?>
		
		<?php endwhile; // end of the loop. ?>

	<div id="content" class="row">
		<div class="lastnavi" style="display:inline-block; width:50%!important; text-align: left;"><span class="bbutton"><i class="fas fa-angle-left" style="display: inline-block;"></i>
			<?php 
			   if(function_exists('get_hansel_and_gretel_breadcrumbs')): 
				  echo get_hansel_and_gretel_breadcrumbs();
			   endif;
			?>
		</span></div>

		<span style="display:inline-block; width:50%; text-align: right;">
			<h5 class="author-name uppercase pt-half" style="display:inline-block; width: auto; padding-top: 3px;">
				<?php echo esc_html( get_the_author_meta( 'display_name' ) ); ?>
			</h5>


			<span itemprop="datePublished" class="entry-date" style="display:inline-block">
				<time class="published" datetime="2013-06-13 12:00:00">&nbsp;-&nbsp;<?php echo get_the_date(); ?></time>
			</span>
			<span itemprop="dateModified" class="modate">
				 <time class="updated" datetime="<?php echo get_the_date('Y-m-d'); ?>"><?php if( get_the_modified_date() != get_the_date() ) echo the_modified_date();?></time>
			</span>		
		</span>		
</div>

<?php do_action( 'flatsome_after_page' ); ?>

<?php get_footer(); 

}

?>