<?php

namespace ColdTrick\SimpleSAML;

class Login {
	
	/**
	 * Take some actions during the login event of a user
	 *
	 * @param \Elgg\Event $event 'login:after', 'user'
	 *
	 * @return void
	 */
	public static function loginEvent(\Elgg\Event $event) {
		
		$user = $event->getObject();
		if (!($user instanceof \ElggUser)) {
			return;
		}
		
		$saml_attributes = elgg_get_session()->get('saml_attributes');
		$source = elgg_get_session()->get('saml_source');
		
		// simplesaml login?
		if (!isset($saml_attributes) || !isset($source)) {
			return;
		}
		
		// source enabled
		if (!simplesaml_is_enabled_source($source)) {
			return;
		}
		
		// validate additional authentication rules
		if (!simplesaml_validate_authentication_attributes($source, $saml_attributes)) {
			return;
		}
		
		// link the user to this source
		$saml_uid = elgg_extract('elgg:external_id', $saml_attributes);
		if (!empty($saml_uid)) {
			if (is_array($saml_uid)) {
				$saml_uid = $saml_uid[0];
			}
			
			// save the external id so the next login will go faster
			simplesaml_link_user($user, $source, $saml_uid);
		}
		
		// save the attributes to the user
		simplesaml_save_authentication_attributes($user, $source, $saml_attributes);
		
		// save source name for single logout
		elgg_get_session()->set('saml_login_source', $source);
		
		// cleanup
		elgg_get_session()->remove('saml_attributes');
		elgg_get_session()->remove('saml_source');
	}
}
