<?php
/**
 * Link a logged in user to a SAML/CAS source
 */

$source = elgg_extract('saml_source', $vars);
if (empty($source)) {
	throw new Elgg\HttpException(elgg_echo('simplesaml:error:no_source'), ELGG_HTTP_BAD_REQUEST);
}

$label = simplesaml_get_source_label($source);

if (!simplesaml_is_enabled_source($source)) {
	throw new Elgg\HttpException(elgg_echo('simplesaml:error:source_not_enabled', [$label]), ELGG_HTTP_BAD_REQUEST);
}

try {
	$saml_auth = new \SimpleSAML\Auth\Simple($source);
} catch (Exception $e) {
	throw new Elgg\HttpException(elgg_echo('simplesaml:error:class', [$e->getMessage()]), ELGG_HTTP_INTERNAL_SERVER_ERROR, $e);
}

// make sure we can forward you to the correct url
$last_forward = elgg_get_session()->get('last_forward_from');
if (!isset($last_forward)) {
	elgg_get_session()->set('last_forward_from', $_SERVER['REFERER']);
}

// login with SAML
if (!$saml_auth->isAuthenticated()) {
	// not logged in on IDP, so do that
	$saml_auth->login();
}

// user is authenticated with IDP, so link in Elgg
$saml_attributes = simplesaml_get_authentication_attributes($saml_auth, $source);
if (empty($saml_attributes)) {
	throw new Elgg\HttpException(elgg_echo('simplesaml:authorize:error:attributes', [$label]), ELGG_HTTP_BAD_REQUEST);
}

// check for additional authentication rules
if (!simplesaml_validate_authentication_attributes($source, $saml_attributes)) {
	// not authorized
	throw new Elgg\HttpException(elgg_echo('simplesaml:error:attribute_validation', [$label]), ELGG_HTTP_FORBIDDEN);
}

// get external id
$saml_uid = elgg_extract('elgg:external_id', $saml_attributes);
if (empty($saml_uid)) {
	throw new Elgg\HttpException(elgg_echo('simplesaml:authorize:error:external_id', [$label]), ELGG_HTTP_BAD_REQUEST);
}

if (is_array($saml_uid)) {
	$saml_uid = $saml_uid[0];
}

$user = elgg_get_logged_in_user_entity();
if (!simplesaml_link_user($user, $source, $saml_uid)) {
	throw new Elgg\HttpException(elgg_echo('simplesaml:authorize:error:link', [$label]), ELGG_HTTP_INTERNAL_SERVER_ERROR);
}

// save attributes
simplesaml_save_authentication_attributes($user, $source, $saml_attributes);

// report success
system_message(elgg_echo('simplesaml:authorize:success', [$label]));
forward("settings/plugins/{$user->username}/simplesaml");
