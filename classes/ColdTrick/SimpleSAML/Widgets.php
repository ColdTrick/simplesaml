<?php

namespace ColdTrick\SimpleSAML;

class Widgets {
	
	/**
	 * Add widget title link
	 *
	 * @param \Elgg\Hook $hook 'entity:url', 'object'
	 *
	 * @return void|string
	 */
	public static function widgetURL(\Elgg\Hook $hook) {
		
		$return_value = $hook->getValue();
		if (!empty($return_value)) {
			// url already set
			return;
		}
		
		if (elgg_is_logged_in()) {
			// already logged in
			return;
		}
		
		$widget = $hook->getEntityParam();
		if (!($widget instanceof \ElggWidget)) {
			return;
		}
		
		if ($widget->handler !== 'simplesaml') {
			return;
		}
		
		$samlsource = $widget->samlsource;
		if (empty($samlsource) || ($samlsource === 'all')) {
			return;
		}
		
		if (!simplesaml_is_enabled_source($samlsource)) {
			return;
		}
		
		return elgg_generate_url('default:saml:login', [
			'saml_source' => $samlsource;
		]);
	}
}
