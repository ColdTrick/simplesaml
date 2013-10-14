<?php

	function simplesaml_login_event_handler($event, $type, $object) {
		
		if (!empty($object) && elgg_instanceof($object, "user")) {
			
			if (isset($_SESSION["saml_attributes"]) && isset($_SESSION["saml_source"])) {
				
				$saml_attributes = $_SESSION["saml_attributes"];
				$source = $_SESSION["saml_source"];
				
				if (simplesaml_is_enabled_source($source)) {
					if ($saml_uid = elgg_extract("elgg:external_id", $saml_attributes)) {
						if (is_array($saml_uid)) {
							$saml_uid = $saml_uid[0];
						}
						// save the external id so the next login will go faster
						simplesaml_link_user($object, $source, $saml_uid);
					}
					
					// save the attributes to the user
					simplesaml_save_authentication_attributes($object, $source, $saml_attributes);
				}
				
				unset($_SESSION["saml_attributes"]);
				unset($_SESSION["saml_source"]);
			}
		}
	}