
<?php while ( have_posts() ) : the_post(); ?>

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
  	 	<div class="flex-col flex-center text-center">		
  			<?php get_template_part( 'template-parts/posts/partials/entry', 'title');  ?>
  	 	</div>
  	</div><!-- flex-row -->
  </div><!-- .page-title -->

  
<?php endwhile; ?>

