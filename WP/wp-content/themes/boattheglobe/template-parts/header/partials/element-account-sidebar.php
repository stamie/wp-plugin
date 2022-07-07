
<li class="account-item has-icon menu-item">
<?php if ( is_user_logged_in() ) { ?>

<a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" class="account-link account-login" title="<?php _e('My account', 'boat-shortcodes'); ?>">
  <?php if(flatsome_option('account_icon') == 'icon'){
    echo get_flatsome_icon('icon-user');
    } else if(flatsome_option('account_icon') == 'avatar'){
    echo '<i class="image-icon circle">'.get_avatar(get_current_user_id()).'</i>';
    }
  ?>
  <span class="header-account-title">
    <?php _e('My account', 'boat-shortcodes'); ?>
  </span>
</a>

<?php } else { ?>
<a href="/login?request=<?php echo $_SERVER["REQUEST_URI"]; ?>"
    class="nav-top-link nav-top-not-logged-in">
  <?php
    if(flatsome_option('account_icon') == 'icon' || flatsome_option('account_icon') == 'avatar'){ echo get_flatsome_icon('icon-user');
    }
  ?>
  <span class="header-account-title">
    <?php _e('Login', 'boat-shortcodes'); ?>
  </span>
</a>
<?php } ?>

<?php
// Show Dropdown for logged in users
if ( is_user_logged_in() ) { ?>
<ul class="children">
  <li class="">
      <a href="/your-options"><?php echo __( 'My Options', 'boat-shortcodes' ); ?></a>
  </li>
  <li class="woocommerce-MyAccount-navigation-link woocommerce-MyAccount-navigation-link--customer-logout">
    <a class="" role="button" href="<?php echo wp_logout_url('/'); ?>"><?php _e('Logout','boat-shortcodes'); ?></a>
  </li>
</ul>
<?php } ?>
</li>
<?php

?>
