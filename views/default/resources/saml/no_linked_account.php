<?php
/**
 * The user tried to login to the site by a SAML/CAS source but no linked account was found. Offer
 * the option to link or create an account
 */

if (elgg_is_logged_in()) {
	throw new Elgg\HttpException(elgg_echo('simplesaml:error:loggedin'), ELGG_HTTP_FORBIDDEN);
}

$source = elgg_extract('saml_source', $vars);
if (empty($source)) {
	throw new Elgg\HttpException(elgg_echo('simplesaml:error:no_source'), ELGG_HTTP_BAD_REQUEST);
}

$label = simplesaml_get_source_label($source);

if (!simplesaml_is_enabled_source($source)) {
	throw new Elgg\HttpException(elgg_echo('simplesaml:error:source_not_enabled', [$label]), ELGG_HTTP_BAD_REQUEST);
}

$session_source = elgg_get_session()->get('saml_source');
if ($session_source !== $source) {
	throw new Elgg\HttpException(elgg_echo('simplesaml:error:source_mismatch'), ELGG_HTTP_CONFLICT);
}

// cleanup login form
simplesaml_unextend_login_form();
$allow_registration = simplesaml_allow_registration($source);

// prepare page elements
$title_text = elgg_echo('simplesaml:no_linked_account:title', [$label]);

$content = elgg_view('simplesaml/no_linked_account', [
	'saml_source' => $source,
	'allow_registration' => $allow_registration,
]);

// build body
$body = elgg_view_layout('default', [
	'title' => $title_text,
	'content' => $content,
]);

// draw page
echo elgg_view_page($title_text, $body);
