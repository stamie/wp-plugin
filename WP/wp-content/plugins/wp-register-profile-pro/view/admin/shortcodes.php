<table width="100%" border="0" class="ap-table">
      <tr>
        <td><h3>Shortcodes</h3></td>
      </tr>
      <tr>
        <td>
          
<p>1. Use this <strong>[rp_register_widget]</strong> shortcode to display registration form in post or page.</p>

 <p>2. Use This shortcode to retrieve user data <strong>[rp_user_data field="first_name" user_id="2"]</strong> user_id can be blank. if blank then the data is retrieve from currently loged in user. Or else you can use this function in your template file.<br />
 <strong>&lt;?php echo rp_user_data_func("first_name","2"); ?&gt;</strong><br /><br />
 For <strong>date</strong> type fields one additional parameter <strong>dateformat</strong> can be passed to the shortcode with the desired date format. For example, <strong>[rp_user_data user_id="2" field="dob" dateformat="jS F, Y"]</strong> will output something like this <strong>26th July, 2012</strong> and for use in the template files use function call like this.<br /><strong>&lt;?php echo rp_user_data_func("dob","2","jS F, Y"); ?&gt;</strong></p>

<p>3. Use this shortcode for user profile page <strong>[rp_profile_edit]</strong> Logged in users can edit profile data from this page.</p>

<p>4. Use this shortcode to display Update Password form <strong>[rp_update_password]</strong></p>

<p>5. Use this <strong>[subscription_view id="1" link="http://example.com/register/"]</strong> shortcode in your page to display individual subscription details. Here "id" is subscription id. Create subscriptions <a href="edit.php?post_type=subscription">here</a>. ( "id" is required, "link" is optional ). "link" parameter can be used as registration page URL. This way non logged in users can be redirected to registartion page.</p>
    
<p>6. Use this <strong>[subscription_user_data title="Subscription Details"]</strong> shortcode in your page to display user's subscription status. This shortcode will display subscription status of the currently logged in user Active/ Inactive.( "title" is optional ).</p>
    
<p>7. Use this <strong>[rp_subscription_log title="Subscription Log"]</strong> shortcode to display subscription payment log of currently logged in user.</p>

 </td>
  </tr>
</table>



