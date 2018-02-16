<?php
/**
 * This file is included when the plugin gets initialized
 */

use Elgg\Project\Paths;
use Elgg\Includer;

elgg_register_event_handler('plugins_boot', 'system', 'simplesaml_plugins_boot');

/**
 * Called on the 'plugins_boot' 'system' event
 *
 * @return void
 */
function simplesaml_plugins_boot() {
	
	$path = elgg_get_plugin_setting('simplesamlphp_path', 'simplesaml');
	if (empty($path)) {
		return;
	}
	
	$path = Paths::sanitize($path);
	if (!file_exists("{$path}lib/_autoload.php")) {
		return;
	}
	
	// register library
	Includer::includeFile("{$path}lib/_autoload.php");
	
	elgg_register_event_handler('init', 'system', 'simplesaml_init');
}

/**
 * Called on the 'init' 'system' event
 *
 * @return void
 */
function simplesaml_init() {
	
	// load libraries
	Includer::requireFile(dirname(__FILE__) . '/lib/functions.php');
	
	// check for force authentication
	elgg_extend_view('page/default', 'simplesaml/force_authentication', 200);
	elgg_extend_view('page/walled_garden', 'simplesaml/force_authentication', 200);
	
	// allow login
	elgg_extend_view('forms/login', 'simplesaml/login');
	
	// register events
	elgg_register_event_handler('login:after', 'user', '\ColdTrick\SimpleSAML\Login::loginEvent');
	
	// register plugin hooks
	elgg_register_plugin_hook_handler('public_pages', 'walled_garden', '\ColdTrick\SimpleSAML\WalledGarden::publicPages');
	elgg_register_plugin_hook_handler('entity:url', 'object', '\ColdTrick\SimpleSAML\WidgetManager::widgetURL');
	elgg_register_plugin_hook_handler('setting', 'plugin', '\ColdTrick\SimpleSAML\PluginSettings::saveSetting');
	elgg_register_plugin_hook_handler('action', 'logout', '\ColdTrick\SimpleSAML\Logout::action');
}
