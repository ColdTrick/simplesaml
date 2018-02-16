<?php

namespace ColdTrick\SimpleSAML;

class PluginSettings {
	
	/**
	 * Change the value of a plugin setting before it is saved.
	 *
	 * This is used to save an array as JSON in a plugin setting. This because arrays can't be saved in plugin settings.
	 *
	 * @param \Elgg\Hook $hook 'setting', 'plugin'
	 *
	 * @return void|string
	 */
	public static function saveSetting(\Elgg\Hook $hook) {
		
		$plugin = $hook->getParam('plugin');
		if (!$plugin instanceof \ElggPlugin || $plugin->getID() !== 'simplesaml') {
			return;
		}
		
		$setting_name = $hook->getParam('name');
		$pattern = '/^(?:idp_)[\S]+(?:_attributes)$/';
		if (preg_match($pattern, $setting_name)) {
			return json_encode($hook->getValue());
		}
	}
}
