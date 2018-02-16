<?php

namespace ColdTrick\SimpleSAML;

class WalledGarden {
	
	/**
	 * Extend the allowed pages of your community if it is in walled garden mode.
	 *
	 * @param \Elgg\Hook $hook 'public_pages', 'walled_garden'
	 *
	 * @return array
	 */
	public static function publicPages(\Elgg\Hook $hook) {
		
		$return_value = $hook->getValue();
		
		// add simplesaml to the public pages
		$return_value[] = 'saml/.*';
		$return_value[] = 'action/simplesaml/.*';
		
		$setting = elgg_get_plugin_setting('simplesamlphp_directory', 'simplesaml');
		if ($setting) {
			$return_value[] = $setting . '/.*';
		}
		
		return $return_value;
	}
}
