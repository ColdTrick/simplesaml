<?php
/**
 * More detailed settings for a specific Service Provider
 */

$plugin = elgg_extract('plugin', $vars);
$source_id = elgg_extract('source_id', $vars);
$source_id_type = elgg_extract('source_id_type', $vars);

if (!($plugin instanceof ElggPlugin) || empty($source_id)) {
	return;
}

$auto_link_options = elgg_extract('auto_link_options', $vars);
$access_type_options = elgg_extract('access_type_options', $vars);
$access_matching_options = elgg_extract('access_matching_options', $vars);

$label = simplesaml_get_source_label($source_id);
$title = elgg_echo('simplesaml:settings:sources:configuration:title', [$label]);

$body = '';

// source icon
$body .= elgg_view_field([
	'#type' => 'url',
	'#label' => elgg_echo('simplesaml:settings:sources:configuration:icon'),
	'#help' => elgg_echo('simplesaml:settings:sources:configuration:icon:description'),
	'name' => "params[{$source_id}_icon_url]",
	'value' => $plugin->getSetting("{$source_id}_icon_url"),
]);

// autolink users based on profile field
$body .= elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('simplesaml:settings:sources:configuration:auto_link'),
	'#help' => elgg_echo('simplesaml:settings:sources:configuration:auto_link:description'),
	'name' => "params[{$source_id}_auto_link]",
	'value' => $plugin->getSetting("{$source_id}_auto_link"),
	'options_values' => $auto_link_options,
]);

if ($source_id_type === 'saml') {
	// only SAML sources have this information
	// configure optional external id field
	$body .= elgg_view_field([
		'#type' => 'text',
		'#label' => elgg_echo('simplesaml:settings:sources:configuration:external_id'),
		'#help' => elgg_echo('simplesaml:settings:sources:configuration:external_id:description'),
		'name' => "params[{$source_id}_external_id]",
		'value' => $plugin->getSetting("{$source_id}_external_id"),
	]);
}

$force_authentication = $plugin->getSetting('force_authentication');
if ($force_authentication === $source_id) {
	// Only show when the current sourec has the force authentication enabled
	$body .= elgg_view_field([
		'#type' => 'text',
		'#label' => elgg_echo('simplesaml:settings:sources:configuration:force_authentication_cidrs'),
		'#help' => elgg_echo('simplesaml:settings:sources:configuration:force_authentication_cidrs:description'),
		'name' => "params[{$source_id}_force_authentication_cidrs]",
		'value' => $plugin->getSetting("{$source_id}_force_authentication_cidrs"),
	]);
}

// advanced access options
$body .= elgg_view_field([
	'#type' => 'fieldset',
	'legend' => elgg_echo('simplesaml:settings:sources:configuration:access'),
	'fields' => [
		[
			'#html' => elgg_view('output/longtext', [
				'value' => elgg_echo('simplesaml:settings:sources:configuration:access:description'),
			]),
		],
		// access matching
		[
			'#type' => 'fieldset',
			'align' => 'horizontal',
			'fields' => [
				[
					'#type' => 'select',
					'name' => "params[{$source_id}_access_type]",
					'value' => $plugin->getSetting("{$source_id}_access_type"),
					'options_values' => $access_type_options,
				],
				[
					'#type' => 'select',
					'name' => "params[{$source_id}_access_matching]",
					'value' => $plugin->getSetting("{$source_id}_access_matching"),
					'options_values' => $access_matching_options,
				],
			],
		],
		// access field
		[
			'#type' => 'text',
			'#label' => elgg_echo('simplesaml:settings:sources:configuration:access_field'),
			'#help' => elgg_echo('simplesaml:settings:sources:configuration:access_field:description'),
			'name' => "params[{$source_id}_access_field]",
			'value' => $plugin->getSetting("{$source_id}_access_field"),
		],
		// access field value
		[
			'#type' => 'text',
			'#label' => elgg_echo('simplesaml:settings:sources:configuration:access_value'),
			'#help' => elgg_echo('simplesaml:settings:sources:configuration:access_value:description'),
			'name' => "params[{$source_id}_access_value]",
			'value' => $plugin->getSetting("{$source_id}_access_value"),
		],
	],
]);

echo elgg_view_module('info', $title, $body, ['id' => "{$source_id}_wrapper", 'class' => 'hidden']);
