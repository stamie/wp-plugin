<form name="f" action="" method="post">
<input type="hidden" name="log_id" value="<?php echo $id;?>" />
<input type="hidden" name="action" value="sub_log_edit" />
<table width="100%" border="0" cellspacing="10" class="ap-table">
<tr>
	<td colspan="2"><h3><?php _e('Edit Subscription Data','wp-register-profile-with-shortcode');?></h3></td>
</tr> 
<tr>
	<td width="200"><strong><?php _e('User','wp-register-profile-with-shortcode');?></strong></td>
	<td><select name="user_id" required>
		<option value=""> - </option>
		<?php echo $this->get_users_selected($data['user_id']);?>
	</select></td>
</tr>
<tr>
	<td><strong><?php _e('Subscription Package','wp-register-profile-with-shortcode');?></strong></td>
	<td><?php echo $sl->subscription_lists_options_selected($data['sub_type']);?></td>
</tr>
<tr>
	<td><strong><?php _e('Added On','wp-register-profile-with-shortcode');?></strong></td>
	<td><input type="text" name="sub_added" id="sub_added" value="<?php echo $data['sub_added'];?>" /></td>
</tr>
<tr>
	<td><strong><?php _e('Subscription End Date','wp-register-profile-with-shortcode');?></strong></td>
	<td><input type="text" name="sub_end_date" id="sub_end_date" value="<?php echo $data['sub_end_date'];?>" /></td>
</tr>
<tr>
	<td width="200" valign="top"><strong><?php _e('Payment Status','wp-register-profile-with-shortcode');?></strong></td>
	<td>
		<select name="payment_status">
			<?php echo $this->get_payment_status_selected($data['payment_status']);?>
		</select>
		
		<?php if($data['woo_order_id']){?>
			<p><a href="post.php?post=<?php echo $data['woo_order_id'];?>&action=edit"><?php _e('Click here for WooCommerce order details','wp-register-profile-with-shortcode');?></a></p>
		<?php } ?>

    </td>
</tr>
<tr>
	<td><strong><?php _e('Subscription Status','wp-register-profile-with-shortcode');?></strong></td>
	<td><strong style="color:<?php echo ($gc->subscription_status($data['user_id']) == 'Active'?'green':'red');?>;"><?php echo $gc->subscription_status($data['user_id'])?></strong></td>
</tr>
<tr>
	<td><strong><?php _e('Make This User Inactive','wp-register-profile-with-shortcode');?></strong></td>
	<td><input type="checkbox" name="inactive_this_user" value="yes" <?php echo $sub_user_global_inactive == 'yes'?'checked="checked"':'';?>><i><?php _e('Check this to make this user Inactive globally, Uncheck this to make user Active','wp-register-profile-with-shortcode');?></i></td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td><input type="submit" name="submit" value="<?php _e('Submit','wp-register-profile-with-shortcode');?>" class="button" /></td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
</table>
</form>
<script>
jQuery(function() {
	jQuery( "#sub_added, #sub_end_date" ).datepicker( { 'dateFormat' : 'yy-mm-dd'});
});
</script>