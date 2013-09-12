<?php

	$plugin = elgg_extract("entity", $vars);
	$page_owner = elgg_get_page_owner_entity();
	
	if($sources = simplesaml_get_enabled_sources()){
		
		foreach($sources as $source){
			$label = simplesaml_get_source_label($source);
			
			$icon = "";
			if($icon_url = simplesaml_get_source_icon_url($source)){
				$icon = elgg_view("output/img", array("src" => $icon_url, "alt" => $label));
			}
			
			$body = "<div>";
			$body .= "<label>" . $label . "</label><br />";
			
			if($plugin->getUserSetting($source . "_uid", $page_owner->getGUID())){
				// user is connected, offer the option to disconnect
				$body .= "<div>" . elgg_echo("simplesaml:usersettings:connected", array($label)) . "</div>";
				
				$body .= elgg_view("output/confirmlink", array(
							"text" => elgg_echo("simplesaml:usersettings:unlink_url"),
							"confirm" => elgg_echo("simplesaml:usersettings:unlink_confirm", array($label)),
							"href" => "action/simplesaml/unlink?user_guid=" . $page_owner->getGUID() . "&source=" . $source
						));
			} else {
				// user is not connected, offer the option to connect
				$body .= "<div>" . elgg_echo("simplesaml:usersettings:not_connected", array($label)) . "</div>";
				
				if($page_owner->getGUID() == elgg_get_logged_in_user_guid()){
					$body .= elgg_view("output/url", array(
								"text" => elgg_echo("simplesaml:usersettings:link_url"),
								"href" => "saml/authorize/" . $source
							));
				}
			}
			
			$body .= "</div>";
			
			echo elgg_view_image_block($icon, $body);
		}
	} else {
		echo "<div>";
		echo elgg_echo("simplesaml:usersettings:no_sources");
		echo "</div>";
	}