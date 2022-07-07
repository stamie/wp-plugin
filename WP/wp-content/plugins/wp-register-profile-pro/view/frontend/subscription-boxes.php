<div class="subscription-box">
  <h3><?php echo get_the_title($id);?></h3>
  <p><?php echo get_subscription_content($id);?></p>
  
  <?php if($show_form){ ?>
    <form name="subscription-renew"  method="post" action="" <?php do_action('rpwsp_register_sub_renew_form_tag');?>>
    <input type="hidden" name="option" value="wprp_renew_subscription" />
    <input type="hidden" name="sub_type" value="<?php echo $id;?>" />
    <div class="reg-form-group"><input name="sub_renew" type="submit" value="<?php _e('Subscribe','wp-register-profile-with-shortcode');?>" <?php do_action('rpwsp_register_form_submit_tag');?>/></div>
    </form>
  <?php } else { ?>
    <a class="sub-btn" href="<?php echo $register_page_link;?>"><?php _e('Subscribe','wp-register-profile-with-shortcode');?></a>
  <?php } ?>
   
</div>