<?php

/**
 * The file used to show the settings on the screen
 *
 * @link:      https://wppostnav.com
 * @since      0.0.1
 *
 * @package    wp_post_nav
 * @subpackage wp_post_nav/admin/partials
 */
?>

<?php
// If this file is called directly, abort. //
if ( ! defined( 'ABSPATH' ) ) {
  exit;
} 
?>

<div id="wp-post-nav-sidebar">
  <div class="wp-post-nav-centered">
    <img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . '/images/logo.png';?>" alt="WP Post Nav" width="100" height="auto">
  </div>
  <div id="wp-post-nav-about">

    <?php
      $plugin_path = $my_plugin = WP_PLUGIN_DIR . '/' . $this->name;
      $plugin_info = get_plugin_data($plugin_path . '/wp-post-nav.php');
    ?>

    <h3>Plugin Information</h3>
    <ul>
      <li>Plugin Version: <?php echo $plugin_info['Version'];?></li>
      <li>Plugin Author: <?php echo ucfirst($plugin_info['Author']);?></li>
      <li>Plugin Url : <a href="<?php echo $plugin_info['PluginURI'];?>"><?php echo $plugin_info['PluginURI'];?></a></li>
    </ul>
    <hr>

    <p>Thank you for installing WP Post Nav.</p>

    <p>WP Post Nav is a simple to use post navigation plugin which allows easy navigation between all types of posts and post types.</p>

    <p>Upon activation, navigate to the settings page and choose the post types you wish the next / previous links to display on, you custom CSS styles and save to make your custom modifications.</p>

    <p>When visiting the front end of your website, on each post type activated, handy navigation arrows will appear on the screen to navigate to the next / previous post.</p>
    
    <div id="wp-post-nav-review">
      <h4>Leave A Review</h4>
      <p>Please support WP Post Nav by leaving a review on the WordPress Plugin page.</p>
      <p>A review helps other users find the best plugins for their site, and in turn, shows your support for the plugin developer.</p>
      <button id="review-button" class="button-primary"><a href="https://wordpress.org/support/plugin/wp-post-nav/reviews/">Leave A Review</a></button>
    </div>

    <div id="wp-post-nav-support">
      <h4>Get Support</h4>
      <p>Got a support question or issue with WP Post Nav?</p>
      <p>Ask a question in the WordPress Support forum</p>
      <button id="support-button" class="button-secondary"><a href="https://wordpress.org/support/plugin/wp-post-nav/" >Get Support</a></button>
    </div>
  </div>
</div>