<?php

function simplesaml_walled_garden_hook($hook_name, $entity_type, $return_value, $params) {
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

function simplesaml_widget_url_hook($hook_name, $entity_type, $return_value, $params) {
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

function simplesaml_plugin_setting_save_hook($hook_name, $entity_type, $return_value, $params) {
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
