<?php

// Emails 

global 
$wprw_mail_to_admin_subject, 
$wprw_mail_to_admin_body, 
$new_user_register_mail_subject, 
$new_user_register_mail_body, 
$user_activation_register_mail_subject, 
$user_activation_register_mail_body, 
$user_activation_create_password_mail_subject, 
$user_activation_create_password_mail_body,
$user_activation_register_mail_subject_v2,
$user_activation_register_mail_body_v2,
$user_activation_create_password_mail_subject_v2,
$user_activation_create_password_mail_body_v2,
$subscription_email_subject, 
$subscription_email_body
;

$wprw_mail_to_admin_subject = "New User Registration";
$wprw_mail_to_admin_body = "A new user with Username #user_name# has registered on #site_name#

<h3>New User Data</h3>

#new_user_data#

Thank You";

$new_user_register_mail_subject = "Registration Successful";
$new_user_register_mail_body = "We are pleased to confirm your registration for #site_name#. Below is your login credential.

Username: #user_name#

Password: #user_password#

Url: #site_url#

Thank You";


$user_activation_register_mail_subject = "Registration Activation Link";
$user_activation_register_mail_body = "Hi #user_name#,

To set your password please visit the following address:
#activation_url#

Thank You";

$user_activation_create_password_mail_subject = "Your Account is Activated";
$user_activation_create_password_mail_body = "Hi #user_name#,

Your account is successfully activated. Please visit #site_url# 

Thank You";

$user_activation_register_mail_subject_v2 = "Registration Activation Link";
$user_activation_register_mail_body_v2 = "Hi #user_name#,

To activate your account please visit the following address:
#activation_url#

Thank You";

$user_activation_create_password_mail_subject_v2 = "Your Account is Activated";
$user_activation_create_password_mail_body_v2 = "Hi #user_name#,

Your account is successfully activated. Please visit #site_url# 

Thank You";


$subscription_email_subject = "Thank you for your subscription";
$subscription_email_body = "Thank you for your subscription.

Site URL: #site_url#

Package: #sub_name#

Subscription End Date: #end_date#
";

// Emails