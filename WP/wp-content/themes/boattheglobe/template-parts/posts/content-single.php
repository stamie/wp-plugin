

<div class="entry-content single-page">

	
	<?php the_content(); ?>
	

	<?php
	wp_link_pages();
	?>

	<div class="iboat-search">
		<h4 style="text-align: center; color: #ccab25; font-size:25px; margin-bottom:0px!important;">HELP ME TO CHOOSE THE PERFECT BOAT</h4>
		<h5 style="text-align: center; color: #000; font-size:15px; margin-bottom:20px!important;">Need to find the right boat with the help of an expert? Send us your needs and<br /> we will find the most suitable yacht for you!</h5>
		<?php  echo do_shortcode('[contact-form-7 id="1143700"]'); ?>
	</div>


	<?php if ( get_theme_mod( 'blog_share', 1 ) ) {
		// SHARE ICONS
		echo '<div class="blog-share text-center">';
		echo '<div class="is-divider medium"></div>';
		echo do_shortcode( '[share]' );
		echo '</div>';
	} ?>
</div><!-- .entry-content2 -->

<?php if ( get_theme_mod( 'blog_single_footer_meta', 1 ) ) : ?>
	<footer class="entry-meta text-<?php echo get_theme_mod( 'blog_posts_title_align', 'center' ); ?>">
		<?php
		/* translators: used between list items, there is a space after the comma */
		$category_list = get_the_category_list( __( ', ', 'flatsome' ) );

		/* translators: used between list items, there is a space after the comma */
		$tag_list = get_the_tag_list( '', __( ', ', 'flatsome' ) );


		// But this blog has loads of categories so we should probably display them here.
		if ( '' != $tag_list ) {
			$meta_text = __( 'This entry was posted in %1$s and tagged %2$s.', 'flatsome' );
		} else {
			$meta_text = __( 'This entry was posted in %1$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'flatsome' );
		}

		printf( $meta_text, $category_list, $tag_list, get_permalink(), the_title_attribute( 'echo=0' ) );
		?>
	</footer><!-- .entry-meta -->
<?php endif; ?>

<?php if ( get_theme_mod( 'blog_author_box', 1 ) ) : ?>
	<div class="entry-author author-box">
		<div class="flex-row align-top">
			<div class="flex-col mr circle">
				<div class="blog-author-image">
					<?php
					$user = get_the_author_meta( 'ID' );
					echo get_avatar( $user, 90 );
					?>					
				</div>
			</div><!-- .flex-col -->
			<div class="flex-col flex-grow">
			
				<span itemprop="datePublished" class="entry-date" style="display:none"><time class="published" datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date('Y-m-d'); ?></time></span>
				<span itemprop="dateModified" class="modate"><time class="updated" datetime="<?php echo get_the_date('Y-m-d'); ?>"><?php if( get_the_modified_date() != get_the_date() ) echo the_modified_date();?></time></span>
				
				<h5 class="author-name uppercase pt-half">
					<span class="fn">
						<?php echo esc_html( get_the_author_meta( 'display_name' ) ); ?>
					</span>
				</h5>
				
				<p class="author-desc small"><?php echo esc_html( get_the_author_meta( 'user_description' ) ); ?></p>
			</div><!-- .flex-col -->
		</div>
	</div>
<?php endif; ?>

<?php if ( get_theme_mod( 'blog_single_next_prev_nav', 1 ) ) :
	flatsome_content_nav( 'nav-below' );
endif; ?>
</section>