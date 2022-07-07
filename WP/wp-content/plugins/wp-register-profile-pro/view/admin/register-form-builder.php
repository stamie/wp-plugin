<table width="100%" border="0" class="ap-table-noborder">
   <tr>
    <td align="center">
          <h3><?php _e('Create Custom Fields','wp-register-profile-with-shortcode');?></h3>
          <?php echo $rfc->field_list();?> 
      </td>
  </tr>
  <tr>
		<td>&nbsp;</td>
	</tr>
  <tr>
    <td>
    <div id="newFields">
	<div id="new_field_form"></div>
    <?php $this->saved_extra_fields($extra_fields, $post->ID);?>
    </div>
    </td>
  </tr>
</table>