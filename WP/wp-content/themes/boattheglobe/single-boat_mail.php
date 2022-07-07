<?php
/**
 * Template Name: Boat email
 * Template Post Type: boat_email
 */
?>
<!DOCTYPE html>
<!--[if lte IE 9 ]><html <?php language_attributes(); ?> class="ie lt-ie9 <?php flatsome_html_classes(); ?>"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html <?php language_attributes(); ?> class="<?php flatsome_html_classes(); ?>"> <!--<![endif]-->
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php do_action( 'flatsome_after_body_open' ); ?>
<?php wp_body_open(); ?>

<?php do_action('flatsome_before_page' ); ?>
<?php do_action('flatsome_after_header'); ?>
<div id="wrapper">

	<div id="main" class="<?php flatsome_main_classes();  ?>">
		<div id="content" class="content-area page-wrapper" role="main">
			<div class="row row-main">
				<div class="large-12 col">
					<div class="col-inner">

						<?php while ( have_posts() ) : the_post(); ?>
							<?php the_content(); ?>
						<?php endwhile; // end of the loop. ?>

					</div><!-- .col-inner -->
				</div><!-- .large-12 -->
			</div><!-- .row -->
		</div>
	</div>

</div>
<?php do_action( 'flatsome_after_page' ); ?>

<?php wp_footer(); ?>
</body>
</html>