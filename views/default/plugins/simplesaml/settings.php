<?php

	$plugin = elgg_extract("entity", $vars);
	
	echo "<div>";
	echo elgg_echo("simplesaml:settings:simplesamlphp_path");
	echo elgg_view("input/text", array("name" => "params[simplesamlphp_path]", "value" => $plugin->simplesamlphp_path));
	echo "<div class='elgg-subtext'>" . elgg_echo("simplesaml:settings:simplesamlphp_path:description") . "</div>";
	echo "</div>";
	
	echo "<div>";
	echo elgg_echo("simplesaml:settings:simplesamlphp_directory");
	echo elgg_view("input/text", array("name" => "params[simplesamlphp_directory]", "value" => $plugin->simplesamlphp_directory));
	echo "<div class='elgg-subtext'>" . elgg_echo("simplesaml:settings:simplesamlphp_directory:description", array(elgg_get_site_entity()->url)) . "</div>";
	echo "</div>";
	
	if(is_callable("simplesaml_get_configured_sources") && ($sources = simplesaml_get_configured_sources()) !== false){
		if(!empty($sources)){
			$enabled_sources = array();
			
			echo "<table class='elgg-table mbm'>";
			
			echo "<tr>";
			echo "<th class='center'>" . elgg_echo("enable") . "</th>";
			echo "<th>" . elgg_echo("simplesaml:settings:sources:name") . "</th>";
			echo "<th class='center'>" . elgg_echo("simplesaml:settings:sources:allow_registration") . "</th>";
			echo "<th class='center'>" . elgg_echo("simplesaml:settings:sources:save_attributes") . "</th>";
			echo "</tr>";
			
			foreach($sources as $source){
				$source_auth_id = $source->getAuthId();
				$enabled = array();
				$registration = array();
				$save_attributes = array();
				
				if($plugin->getSetting($source_auth_id . "_enabled")){
					$enabled = array("checked" => "checked");
					
					$enabled_sources[] = $source_auth_id;
				}
				
				if($plugin->getSetting($source_auth_id . "_allow_registration")){
					$registration = array("checked" => "checked");
				}
				
				if($plugin->getSetting($source_auth_id . "_save_attributes")){
					$save_attributes = array("checked" => "checked");
				}
				
				echo "<tr>";
				echo "<td class='center'>" . elgg_view("input/checkbox", array("name" => "params[" . $source_auth_id . "_enabled]", "value" => "1") + $enabled) . "</td>";
				echo "<td>" . $source_auth_id . "</td>";
				echo "<td class='center'>" . elgg_view("input/checkbox", array("name" => "params[" . $source_auth_id . "_allow_registration]", "value" => "1") + $registration) . "</td>";
				echo "<td class='center'>" . elgg_view("input/checkbox", array("name" => "params[" . $source_auth_id . "_save_attributes]", "value" => "1") + $save_attributes) . "</td>";
				echo "</tr>";
			}
			
			echo "</table>";
			
			// settings for enabled sources
			if(!empty($enabled_sources)){
				
				foreach($enabled_sources as $source){
					$label = simplesaml_get_source_label($source);
					$title = elgg_echo("simplesaml:settings:sources:configuration:title", array($label));
					
					$body = "<div>";
					$body .= elgg_echo("simplesaml:settings:sources:configuration:icon");
					$body .= elgg_view("input/url", array("name" => "params[" . $source . "_icon_url]", "value" => $plugin->getSetting($source . "_icon_url")));
					$body .= "<div class='elgg-subtext'>" . elgg_echo("simplesaml:settings:sources:configuration:icon:description") . "</div>";
					$body .= "</div>";
					
					$body .= "<div>";
					$body .= elgg_echo("simplesaml:settings:sources:configuration:external_id");
					$body .= elgg_view("input/text", array("name" => "params[" . $source . "_external_id]", "value" => $plugin->getSetting($source . "_external_id")));
					$body .= "<div class='elgg-subtext'>" . elgg_echo("simplesaml:settings:sources:configuration:external_id:description") . "</div>";
					$body .= "</div>";
					
					echo elgg_view_module("inline", $title, $body);
				}
			}
		} else {
			// SimpleSAMLPHP is not yet configured
			echo "<div>";
			echo elgg_echo("simplesaml:settings:warning:configuration:sources");
			echo "</div>";
		}
	} else {
		// SimpleSAMLPHP is not yet loaded
		echo "<div>";
		echo elgg_echo("simplesaml:settings:warning:configuration:simplesamlphp");
		echo "</div>";
	}