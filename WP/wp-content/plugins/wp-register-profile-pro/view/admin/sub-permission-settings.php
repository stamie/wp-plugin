<form name="f" action="" method="post">
<input type="hidden" name="action" value="sub_permission_edit" />
<table width="100%" border="0" cellspacing="10" class="ap-table">
    <tr>
        <td colspan="2"><h3><?php _e('Subscription Permissions','wp-register-profile-with-shortcode');?></h3></td>
    </tr>
    <tr>
        <td width="300"><strong><?php _e('Select Subscription','wp-register-profile-with-shortcode');?></strong></td>
        <td><select name="subscription_id" required onChange="reloadSubPerList( this.value )">
            <option value=""> - </option>
            <?php echo $this->get_subscription_selected($subscription_id);?>
        </select></td>
    </tr>
    <?php if( $subscription_id ){ 
    $raa = get_option( 'subscription_raa_'.$subscription_id, true );
    $rcp = get_option( 'subscription_rcp_'.$subscription_id, true );
    ?>
    <tr>
        <td width="300"><strong><?php _e('Restrict admin panel access','wp-register-profile-with-shortcode');?></strong></td>
        <td><label><input type="checkbox" name="subscription_raa_<?php echo $subscription_id;?>" value="Yes" <?php echo $raa === 'Yes'?'checked':'';?>><i><?php _e('If checked users will not be able to access admin panel. Please note this rule is not applicable to','wp-register-profile-with-shortcode');?> <strong><?php _e('Administrator Roles','wp-register-profile-with-shortcode');?></strong></i></label></td>
    </tr>
    <tr>
        <td colspan="2"><hr></td>
    </tr>
    <tr>
        <td width="300"><strong><?php _e('Pages, Posts & Custom Post Types','wp-register-profile-with-shortcode');?></strong></td>
        <td></td>
    </tr>
    <?php
    $args = array(
        'public'   => true,
    );
    $post_types = get_post_types( $args, 'names' ); 
    $post_types = array_diff($post_types, array('attachment'));
    foreach ( $post_types as $post_type ) {
        ?>
        <tr>
            <td width="300" valign="top"><strong><?php echo ucfirst($post_type); ?></strong></td>
            <td>
            <?php
            $posts_data_args = array( 'post_type' => $post_type, 'posts_per_page' => -1 );
            $posts_data = new WP_Query( $posts_data_args );
            if ( $posts_data->have_posts() ) {
                while ( $posts_data->have_posts() ) {
                    $posts_data->the_post();
                    if( is_array($rcp) and in_array($posts_data->post->ID,$rcp) ){
                        echo '<label><input type="checkbox" name="subscription_rcp_'.$subscription_id.'[]" value="'.$posts_data->post->ID.'" checked>' . get_the_title() . '</label> ';
                    } else {
                        echo '<label><input type="checkbox" name="subscription_rcp_'.$subscription_id.'[]" value="'.$posts_data->post->ID.'">' . get_the_title() . '</label> ';
                    }
                }
                wp_reset_postdata();
            }
            ?>
            </td>
        </tr>
        <tr>
            <td colspan="2"><hr></td>
        </tr>
        <?php
    }
    ?>
    <?php } ?>
    <tr>
        <td>&nbsp;</td>
        <td><i><?php _e('Selected Pages, Posts and Custom Posts will be availabe to the subscribed users only. You can restrict the post content individually from the post edit section.','wp-register-profile-with-shortcode');?></i></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td><input type="submit" name="submit" value="<?php _e('Submit','wp-register-profile-with-shortcode');?>" class="button button-primary button-large button-ap-large" /></td>
    </tr>
</table>
</form>