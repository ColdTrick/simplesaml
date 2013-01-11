<?php 

	elgg_register_event_handler("plugins_boot", "system", "simplesaml_plugins_boot");
	
	function simplesaml_plugins_boot(){
		
		if($path = elgg_get_plugin_setting("simplesamlphp_path", "simplesaml")){
			
			if(file_exists($path . "/lib/_autoload.php")){
				// register library
				elgg_register_library("simplesamlphp", $path . "/lib/_autoload.php");
				
				elgg_register_event_handler("init", "system", "simplesaml_init");
			}
		}
	}
	
	function simplesaml_init(){
		// load libraries
		elgg_load_library("simplesamlphp");
		
		require_once(dirname(__FILE__) . "/lib/events.php");
		require_once(dirname(__FILE__) . "/lib/functions.php");
		require_once(dirname(__FILE__) . "/lib/hooks.php");
		require_once(dirname(__FILE__) . "/lib/page_handlers.php");
		
		// extend CSS
		elgg_extend_view("css/elgg", "simplesaml/css/site");
		
		// allow login
		elgg_extend_view("forms/login", "simplesaml/login");
		
		// register page_handler for nice URL's
		elgg_register_page_handler("saml", "simplesaml_page_handler");
		
		// register widgets
		elgg_register_widget_type("simplesaml", elgg_echo("login"), elgg_echo("simplesaml:widget:description"), "index", true);
		
		// register events
		elgg_register_event_handler("login", "user", "simplesaml_login_event_handler");
		
		// register plugin hooks
		elgg_register_plugin_hook_handler("public_pages", "walled_garden", "simplesaml_walled_garden_hook");
		elgg_register_plugin_hook_handler("widget_url", "widget_manager", "simplesaml_widget_url_hook");
		
		// register actions
		elgg_register_action("simplesaml/register", dirname(__FILE__) . "/actions/register.php", "public");
		elgg_register_action("simplesaml/unlink", dirname(__FILE__) . "/actions/unlink.php");
	}