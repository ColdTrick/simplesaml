<?php 

	$english = array(
		// general stuff
		'simplesaml:error:loggedin' => "This actions can't be performed when you're logged in",
		'simplesaml:error:no_source' => "No SAML connection defined",
		'simplesaml:error:source_not_enabled' => "The provided SAML connection isn't enabled on this site",
		'simplesaml:error:source_mismatch' => "The provided SAML connecten doesn't macht the server connection",
		'simplesaml:error:class' => "Error while getting the SAML configuration: %s",
		
		// pages
		// no linked account
		'simplesaml:no_linked_account:title' => "No account linked to the SAML connection: %s",
		'simplesaml:no_linked_account:description' => "We couldn't find an account which is linked to your SAML account of %s. You can link your site account with your SAML account when you login here.",
		
		'simplesaml:forms:register:description' => "If you don't have an account on this site yet, you can register an account by clicking on the register button. It may be neccesary to provide some additional information.",
		
		// settings
		'simplesaml:settings:simplesamlphp_path' => "Path to the SimpleSAMLPHP library",
		'simplesaml:settings:simplesamlphp_path:description' => "The full path to the SimpleSAMLPHP (http://simplesamlphp.org) library without a trailing slash (/)",
		'simplesaml:settings:simplesamlphp_directory' => "Virtual directory of the SimpleSAMLPHP library",
		'simplesaml:settings:simplesamlphp_directory:description' => "The directory in which the SimpleSAMLPHP library is located without a trailing slash(/). For example if the full path is %ssimplesamlphp/, please enter simplesamlphp",
		
		'simplesaml:settings:sources:name' => "SAML connection",
		'simplesaml:settings:sources:allow_registration' => "Allow registration",
		
		'simplesaml:settings:sources:configuration:title' => "Configuration settings for: %s",
		'simplesaml:settings:sources:configuration:icon' => "URL to an icon for this connection (optional)",
		'simplesaml:settings:sources:configuration:icon:description' => "You can provide an URL to an icon for this connection, it will be used on the login screen and the user settings page.",
		'simplesaml:settings:sources:configuration:external_id' => "Field with the unique user id from the SAML connection (optional)",
		'simplesaml:settings:sources:configuration:external_id:description' => "If you can't get the unique user id from the attributes, you can provide a field from the AuthData which contains the user id",
		
		'simplesaml:settings:warning:configuration:sources' => "No SAML connections have been configured yet",
		'simplesaml:settings:warning:configuration:simplesamlphp' => "Please provide the path to the SimpleSAMLPHP library for further configuration options",
		
		// user settings
		'simplesaml:usersettings:connected' => "Your account is connected with the SAML connection %s. You can login to this site with you SAML account if you wish.",
		'simplesaml:usersettings:unlink_url' => "Click here to remove the connection",
		'simplesaml:usersettings:unlink_confirm' => "Are you sure you wish to break the connection with %s",

		'simplesaml:usersettings:not_connected' => "Your account is not connected with the SAML connection %s. If you wish to login on this site with your SAML account, please link both account.",
		'simplesaml:usersettings:link_url' => "Click here to link both accounts",
		
		'simplesaml:usersettings:no_sources' => "No SAML sources are available, please ask your administrator to configure this.",
		
		// widgets
		'simplesaml:widget:description' => "Shows a login widget with only SAML sources",
		'simplesaml:widget:select_source' => "Please select the SAML source to show in the widget",
		'simplesaml:widget:logged_in' => "<b>%s</b> welcome on the <b>%s</b> community",
	
		// procedures
		// login
		'simplesaml:login:no_linked_account' => "No account is connected to the SAML connection %s",
		
		// authorize
		'simplesaml:authorize:error:attributes' => "No attributes could be found from the SAML connection %s, please try again or contact your site administrator",
		'simplesaml:authorize:error:external_id' => "No unique identifier could be found from the SAML connection %s, please try again or contact your site administrator",
		'simplesaml:authorize:error:link' => "An unknown error occured while connecting with the SAML connection %s",
		'simplesaml:authorize:success' => "You've successfully connected your account with the SAML connection %s",
		
		// actions
		// register
		'simplesaml:action:register:error:displayname' => "No display name was provided, please fill in your name",
		
		// unlink
		'simplesaml:action:unlink:error' => "An unknown error occured while removing the link with the SAML connection %s",
		'simplesaml:action:unlink:success' => "You've successfully removed the link with the SAML connection %s",
		
	);
	
	add_translation("en", $english);