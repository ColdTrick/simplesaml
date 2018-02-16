<?php

$source = elgg_extract('saml_source', $vars);

echo elgg_view_field([
	'#type' => 'hidden',
	'name' => 'saml_source',
	'value' => $source,
]);

$label = simplesaml_get_source_label($source);
echo elgg_format_element('div', ['class' => 'mbm'], elgg_echo('simplesaml:forms:register:description', [$label]));

// check for missing fields
$saml_attributes = elgg_get_session()->get('saml_attributes');

// we need name
if (!elgg_extract('elgg:firstname', $saml_attributes) && !elgg_extract('elgg:lastname', $saml_attributes)) {
	// no name fields, so ask
	echo elgg_view_field([
		'#type' => 'text',
		'#label' => elgg_echo('name'),
		'name' => 'displayname',
		'value' => elgg_extract('displayname', $vars),
		'required' => true,
	]);
}

// we need email
if (!elgg_extract('elgg:email', $saml_attributes)) {
	// no email field, so ask
	echo elgg_view_field([
		'#type' => 'email',
		'#label' => elgg_echo('email'),
		'name' => 'email',
		'value' => elgg_extract('email', $vars),
		'required' => true,
	]);
}

// form footer
$footer = elgg_view_field([
	'#type' => 'submit',
	'value' => elgg_echo('register'),
]);
elgg_set_form_footer($footer);
