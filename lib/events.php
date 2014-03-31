<?php
/**
 * In this file all the event handlers are defined.
 *
 */

/**
 * Take some actions during the login event of a user
 *
 * @param string   $event  'login' is the event this function handles
 * @param string   $type   'user' is the type for this event
 * @param ElggUser $object the current user trying to login
 *
 * @return void
 */
function simplesaml_login_event_handler($event, $type, $object) {
	
	if (!empty($object) && elgg_instanceof($object, "user")) {
		
		if (isset($_SESSION["saml_attributes"]) && isset($_SESSION["saml_source"])) {
			
			$saml_attributes = $_SESSION["saml_attributes"];
			$source = $_SESSION["saml_source"];
			
			if (simplesaml_is_enabled_source($source)) {
				$saml_uid = elgg_extract("elgg:external_id", $saml_attributes);
				if (!empty($saml_uid)) {
					if (is_array($saml_uid)) {
						$saml_uid = $saml_uid[0];
					}
					// save the external id so the next login will go faster
					simplesaml_link_user($object, $source, $saml_uid);
				}
				
				// save the attributes to the user
				simplesaml_save_authentication_attributes($object, $source, $saml_attributes);
				
				// save source name for single logout
				$_SESSION["saml_login_source"] = $source;
			}
			
			unset($_SESSION["saml_attributes"]);
			unset($_SESSION["saml_source"]);
		}
	}
}
