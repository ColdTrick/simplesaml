<?php
/**
 * All plugin hook handlers can be found in this file
 */

/**
 * Extend the allowed pages of your community if it is in walled garden mode.
 *
 * @param string $hook         'public_pages' is the hook name
 * @param string $type         'walled_garden' is the type if this hook
 * @param array  $return_value the default return value
 * @param array  $params       an array with parameter to help extending the result
 *
 * @return array an array with all the allowed pages
 */
function simplesaml_walled_garden_hook($hook, $type, $return_value, $params) {
	$result = $return_value;

	// get virtual directory path to simplesamlphp installation
	static $simplesamlphp_directoy;
	if (!isset($simplesamlphp_directoy)) {
		$simplesamlphp_directoy = false;
		
		$setting = elgg_get_plugin_setting("simplesamlphp_directory", "simplesaml");
		if (!empty($setting)) {
			$simplesamlphp_directoy = $setting;
		}
	}
	
	// add simplesaml to the public pages
	$result[] = "saml/.*";
	$result[] = "action/simplesaml/.*";
	
	if ($simplesamlphp_directoy) {
		$result[] = $simplesamlphp_directoy . "/.*";
	}

	return $result;
}

/**
 * Add widget title link if Widget Manager is enabled.
 *
 * @param string $hook         'widget_url' is the hook name
 * @param string $type         'widget_manager' is the type if this hook
 * @param array  $return_value the default return value
 * @param array  $params       an array with parameter to help extending the result
 *
 * @return string an url to be put in the widget title
 */
function simplesaml_widget_url_hook($hook, $type, $return_value, $params) {
	$result = $return_value;

	if (!empty($params) && is_array($params)) {
		$widget = elgg_extract("entity", $params);
		if (!empty($widget)) {
			if ($widget->handler == "simplesaml") {
				$samlsource = $widget->samlsource;
				
				if (!empty($samlsource) && ($samlsource !== "all")) {
					if (simplesaml_is_enabled_source($samlsource)) {
						$result = "/saml/login/" . $samlsource;
					}
				}
			}
		}
	}

	return $result;
}

/**
 * Change the value of a plugin setting before it is saved.
 *
 * This is used to save an array as JSON in a plugin setting. This because arrays can't be saved in plugin settings.
 *
 * @param string $hook         'setting' is the hook name
 * @param string $type         'plugin' is the type if this hook
 * @param array  $return_value the default return value
 * @param array  $params       an array with parameter to help extending the result
 *
 * @return string the alternate plugin setting value
 */
function simplesaml_plugin_setting_save_hook($hook, $type, $return_value, $params) {
	$result = $return_value;

	if (!empty($params) && is_array($params)) {
		$plugin = elgg_extract("plugin", $params);
		$setting_name = elgg_extract("name", $params);
		
		if (!empty($plugin) && elgg_instanceof($plugin, "object", "plugin")) {
			if ($plugin->getID() == "simplesaml") {
				$pattern = '/^(idp_){1}[\S]+(_attributes){1}$/';
				
				if (preg_match($pattern, $setting_name)) {
					$result = json_encode($result);
				}
			}
		}
	}
	
	return $result;
}
