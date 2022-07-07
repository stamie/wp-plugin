<div class="wrap">
  <h2><?php _e('Subscription Log','wp-register-profile-with-shortcode');?> <a href="admin.php?page=subscription_log_v2&action=add" class="page-title-action">Add New</a></h2>

  <div id="poststuff">
    <div id="post-body" class="metabox-holder">
      <div id="post-body-content">
        <div class="meta-box-sortables ui-sortable">
            <form method="post" enctype="multipart/form-data">
            <?php
            $this->subscription_obj->prepare_items();
            $this->subscription_obj->custom_display(); 
            ?>
            </form>
        </div>
      </div>
    </div>
    <br class="clear">
  </div>
</div>