<p><strong><?php _e('WooCommerce Product','wp-register-profile-with-shortcode');?></strong>
<select name="woo_product_id" class="widefat">
  <?php echo $this->get_woo_product_selected($woo_product_id);?>
</select>
</p>
<p><?php _e('Enter Product ID from WooCommerce. Leave this empty to make the booking FREE','wp-register-profile-with-shortcode');?></p>

<p>
<strong><?php _e('Period','wp-register-profile-with-shortcode');?></strong> <input type="text" name="sub_period" value="<?php echo $sub_period;?>" class="widefat">
<i><?php _e('in days.','wp-register-profile-with-shortcode');?></i>
</p>