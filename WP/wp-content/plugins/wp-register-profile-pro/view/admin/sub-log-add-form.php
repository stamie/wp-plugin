<form name="f" action="" method="post">
<input type="hidden" name="action" value="sub_log_add" />
<table width="100%" border="0" cellspacing="10" class="ap-table">
<tr>
	<td colspan="2"><h3><?php _e('New Subscription Data','wp-register-profile-with-shortcode');?></h3></td>
</tr>
<tr>
	<td width="200"><strong><?php _e('User','wp-register-profile-with-shortcode');?></strong></td>
	<td><select name="user_id" required>
		<option value=""> - </option>
		<?php echo $this->get_users_selected();?>
	</select></td>
</tr>
<tr>
	<td><strong><?php _e('Package','wp-register-profile-with-shortcode');?></strong></td>
	<td><?php echo $sl->subscription_lists_options_selected();?></td>
</tr>
<tr>
	<td><strong><?php _e('Added On','wp-register-profile-with-shortcode');?></strong></td>
	<td><input type="text" name="sub_added" id="sub_added" value="" /></td>
</tr>
<tr>
	<td><strong><?php _e('Subscription End Date','wp-register-profile-with-shortcode');?></strong></td>
	<td><input type="text" name="sub_end_date" id="sub_end_date" value="" /></td>
</tr>
<tr>
	<td width="200"><strong><?php _e('Payment Status','wp-register-profile-with-shortcode');?></strong></td>
	<td>
		<select name="payment_status"><?php echo $this->get_payment_status_selected();?></select>
	</td>
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