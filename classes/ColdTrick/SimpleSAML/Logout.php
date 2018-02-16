<?php

namespace ColdTrick\SimpleSAML;

use Elgg\Http\OkResponse;

class Logout {
	
	/**
	 * Hook on the logout action to make sure we can logout on SimpleSAML
	 *
	 * @param \Elgg\Hook $hook 'action', 'logout'
	 *
	 * @return void
	 */
	public static function action(\Elgg\Hook $hook) {
		global $SIMPLESAML_SOURCE;
		
		$login_source = elgg_get_session()->get('saml_login_source');
		if (!isset($login_source)) {
			return;
		}
		
		// store session data because session is destroyed
		$SIMPLESAML_SOURCE = $login_source;
	
		// after session is destroyed forward to saml logout
		elgg_register_plugin_hook_handler('response', 'action:logout', '\ColdTrick\SimpleSAML\Logout::response', 99999);
	}
	
	/**
	 * Hook on the forward function to make sure we can logout on SimpleSAML
	 *
	 * @param \Elgg\Hook $hook 'response', 'action:logout'
	 *
	 * @return void
	 */
	public static function forward(\Elgg\Hook $hook) {
		global $SIMPLESAML_SOURCE;
		
		$responce = $hook->getValue();
		if (!$responce instanceof OkResponse || empty($SIMPLESAML_SOURCE)) {
			return;
		}
		
		// do we have a logout source
		try {
			$source = new \SimpleSAML\Auth\Simple($SIMPLESAML_SOURCE);
	
			// logout of the external source
			$source->logout($responce->getForwardURL());
		} catch (\Exception $e) {
			// do nothing
		}
	}
}
