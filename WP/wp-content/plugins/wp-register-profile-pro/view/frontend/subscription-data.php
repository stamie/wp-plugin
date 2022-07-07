<?php
if(empty($data)){ ?>
<div class="subscription-details">
     <?php if($title){ echo '<h3>'.$title.'</h3>'; } ?>
      <p><strong><?php _e('Status');?></strong> <?php _e('Inactive');?></p>
</div>
<?php } else { 
$subscription_type = $data->sub_type;
$gc = new Subscription_General_Class;
?>
<div class="subscription-details">
    <?php
    if($gc->get_sub_end_warning_message( get_current_user_id() )){
        echo '<div class="subscription_error">'.$gc->get_sub_end_warning_message(get_current_user_id()).'</div>';
    }
    ?>
     <?php if($title){ echo '<h3>'.$title.'</h3>'; } ?>
      <h4><?php echo get_the_title($subscription_type);?></h4>
      <p><strong><?php _e('End Date');?></strong> <?php echo $gc->subscription_end_date($user_id);?></p>
      <p><strong><?php _e('Status');?></strong> <span class="<?php echo $gc->subscription_status_class($user_id);?>"><?php echo $gc->subscription_status($user_id);?></span></p>
      
      <form name="subscription-renew"  method="post" action="" <?php do_action('rpwsp_register_sub_renew_form_tag');?>>
      <input type="hidden" name="option" value="wprp_renew_subscription" />

      <?php do_action('wp_register_profile_subscription' ); ?>

      <div class="reg-form-group"><input name="sub_renew" type="submit" value="<?php _e('Click here to renew subscription','wp-register-profile-with-shortcode');?>" <?php do_action('rpwsp_register_form_submit_tag');?>/></div>

      </form>
      
</div>
<?php
} // end of else 