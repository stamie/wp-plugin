
<li class="account-item has-icon">
<?php if ( is_user_logged_in() ) { ?>

	<a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>"
	class="account-link-mobile" title="<?php _e('My account', 'boat-shortcodes'); ?>">
	  <?php echo get_flatsome_icon('icon-user'); ?>
	</a>

<?php } else {?>
	
	<a href="/login?request=<?php echo $_SERVER["REQUEST_URI"]; ?>"
	class="account-link-mobile" title="<?php _e('My account', 'boat-shortcodes'); ?>">
	  <?php echo get_flatsome_icon('icon-user'); ?>
	</a>

<?php } ?>
	
</li>
