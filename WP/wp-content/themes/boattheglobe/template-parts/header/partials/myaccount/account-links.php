<?php 
 if ( has_nav_menu( 'my_account' ) ) { 
    echo wp_nav_menu(array(
      'theme_location' => 'my_account',
      'container' => false,
      'items_wrap' => '%3$s',
      'depth' => 0,
      'walker' => new FlatsomeNavSidebar
    ));
  }
  ?>



    <li class="">
      <a href="/your-options"><?php echo __( 'My Options', 'boat-shortcodes' ); ?></a>
      
    </li>
  <?php // do_action('flatsome_account_links'); ?>
  <li class="woocommerce-MyAccount-navigation-link woocommerce-MyAccount-navigation-link--customer-logout">
    <a class="" role="button" href="<?php echo wp_logout_url('/'); ?>"><?php _e('Logout','boat-shortcodes'); ?></a>
  </li>

