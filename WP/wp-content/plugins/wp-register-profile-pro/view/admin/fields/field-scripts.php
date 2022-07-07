<script type="text/javascript">
  function genFieldName(t){
    var temp = t.value.toLowerCase().replace(/ +/g,'_').replace(/[^a-z0-9_]/g,'').trim();
    if(jQuery(t).parent().next().find('input[name="field_name"]').val() == ''){
      jQuery(t).parent().next().find('input[name="field_name"]').val(temp);
    }
  }
  function genFieldNameEdit(t){
    var temp = t.value.toLowerCase().replace(/ +/g,'_').replace(/[^a-z0-9_]/g,'').trim();
    if(jQuery(t).parent().next().find('input[name="field_names[]"]').val() == ''){
      jQuery(t).parent().next().find('input[name="field_names[]"]').val(temp);
    }
  }
  function selectField(t){
    if( t.value == '' ){
      alert( 'Please select field type!' );
      return false;
    }
    addNewField(t.value);
  }

  function delNewField(t){
    jQuery(t).parent().parent().remove();
  }

  function delField(t){
    jQuery(t).parent().parent().parent().remove();
  }

  function editField(t){
    jQuery(t).parent().parent().siblings('div.custom-field-box-form').show();
  }

  function closeField(t){
    jQuery(t).closest('div.custom-field-box-form').hide();
  }

  function addNewField(field){
    jQuery.ajax({
      type: 'POST',
      async : false,
      data: { option : "addNewField", field : field},
      success: function(data) {
        jQuery('#new_field_form').html(data);
      }
    });
  }

  function saveField(field_type){

    jQuery.ajax({
      type: 'POST',
      async : false,
      data: {option : "saveField", field_type : field_type, field_label : jQuery('#field_label').val(), field_name : jQuery('#field_name').val(), field_desc : jQuery('#field_desc').val(), field_desc_position : jQuery('#field_desc_position').val(), field_placeholder : jQuery('#field_placeholder').val(), field_required : jQuery('#field_required').val(), field_title : jQuery('#field_title').val(), field_pattern: jQuery('#field_pattern').val(), field_show_register : jQuery('#field_show_register').val(), field_show_profile : jQuery('#field_show_profile').val(),field_values : jQuery('#field_values').val()},
      success: function(data) {
        jQuery('#newFields').prepend(data);
        jQuery('#field_list').val('');
        jQuery('#field_label').val('');
        jQuery('#field_name').val('');
        jQuery('#field_desc').val('');
        jQuery('#field_desc_position').val('');
        jQuery('#field_placeholder').val('');
        jQuery('#field_required').val('');
        jQuery('#field_title').val('');
        jQuery('#field_pattern').val('');
        jQuery('#field_show_register').val('');
        jQuery('#field_show_profile').val('');
        jQuery('#field_values').val('');
        jQuery('#new_field_form').html('');

      }
    });
  }
</script>