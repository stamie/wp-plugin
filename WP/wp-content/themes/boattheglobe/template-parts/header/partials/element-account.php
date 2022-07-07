<?php $icon_style = get_theme_mod('account_icon_style'); ?>

<li class="account-item has-icon
  <?php echo ' active'; ?>
  <?php if ( is_user_logged_in() ) { ?> has-dropdown<?php } ?>"
>
<?php  echo '<div class="header-button">'; ?>

<?php if ( is_user_logged_in() ) { ?>
<a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" class="account-link account-login
  <?php if($icon_style && $icon_style !== 'image') echo get_flatsome_icon_class($icon_style, 'small'); ?>"
  title="<?php _e('My account', 'boat-shortcodes'); ?>">

	<?php  if ( get_theme_mod( 'header_account_title', 1 ) ) { ?>
		<span class="header-account-title">
		<?php
/*		if ( get_theme_mod( 'header_account_username' ) ) {
			$current_user = wp_get_current_user();
			echo apply_filters( 'flatsome_header_account_username', esc_html( $current_user->display_name ) );
		} els1e { */
			esc_html_e( 'My account', 'boat-shortcodes' );
		//}
		?>
		</span>
	<?php } ?>

  <?php 
    echo get_flatsome_icon('icon-user');
    ?>

</a>

<?php } else { ?>
  <a id="" href="/login?request=<?php echo $_SERVER["REQUEST_URI"]; ?>"
    class="nav-top-link nav-top-not-logged-in"
     >
<?=__("Login", "boat-shortcodes"); ?> / <?=__("Registration", "boat-shortcodes"); ?> 
</a>
<?php } ?>

<?php  echo '</div>'; ?>

<?php
// Show Dropdown for logged in users
if ( is_user_logged_in() ) { ?>
<ul class="nav-dropdown logged-user <?php flatsome_dropdown_classes(); ?>">
    <?php if (file_exists(__DIR__.'/myaccount/account-links.php')){
        require_once(__DIR__.'/myaccount/account-links.php');
      }
     ?>
</ul>
<?php } ?>

</li>

