<?php 

	function simplesaml_walled_garden_hook($hook_name, $entity_type, $return_value, $params){
		$result = $return_value;
	
		// get virtual directory path to simplesamlphp installation
		static $simplesamlphp_directoy;
		if(!isset($simplesamlphp_directoy)){
			$simplesamlphp_directoy = false;
			
			if($setting = elgg_get_plugin_setting("simplesamlphp_directory", "simplesaml")){
				$simplesamlphp_directoy = $setting;
			}
		}
		
		// add simplesaml to the public pages
		$result[] = "saml/.*";
		$result[] = "action/simplesaml/.*";
		
		if($simplesamlphp_directoy){
			$result[] = $simplesamlphp_directoy . "/.*";
		}
	
		return $result;
	}
	
	function simplesaml_widget_url_hook($hook_name, $entity_type, $return_value, $params) {
		$result = $return_value;
	
		if (!empty($params) && is_array($params)) {
			if ($widget = elgg_extract("entity", $params)) {
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