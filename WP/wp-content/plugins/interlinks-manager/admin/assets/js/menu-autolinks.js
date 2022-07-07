jQuery(document).ready(function($) {

  $('#cancel').click(function(event) {

    //reload the Autolinks menu
    event.preventDefault();
    window.location.replace(window.daim_admin_url + 'admin.php?page=daim-autolinks');

  });

  //Submit the filter form when the select box changes
  $('#daext-filter-form select').on('change', function() {
    $('#daext-filter-form').submit();
  });

});