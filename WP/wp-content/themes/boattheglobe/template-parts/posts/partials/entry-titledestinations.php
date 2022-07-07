
<?php echo do_shortcode('[block id="destinations-content"]'); ?>



<?php
$single_post = is_singular( 'post' );
if ( $single_post && get_theme_mod( 'blog_single_header_meta', 1 ) ) : ?>
	<div class="entry-meta uppercase is-xsmall">
		<?php flatsome_posted_on(); ?>
	</div><!-- .entry-meta -->
<?php elseif ( ! $single_post && 'post' == get_post_type() ) : ?>
	<div class="entry-meta uppercase is-xsmall">
		<?php flatsome_posted_on(); ?>
	</div><!-- .entry-meta -->
<?php endif; ?>
