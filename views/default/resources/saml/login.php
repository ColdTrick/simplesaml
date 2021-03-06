<?php
/**
 * Login based on a SAML/CAS source
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

$forward_url = REFERER;

// login with SAML
if (!$saml_auth->isAuthenticated()) {
	// not logged in on IDP, so do that
	$saml_auth->login();
}

// user is authenticated with IDP, so check in Elgg
$saml_attributes = simplesaml_get_authentication_attributes($saml_auth, $source);

// check for additional authentication rules
if (!simplesaml_validate_authentication_attributes($source, $saml_attributes)) {
	// not authorized
	register_error(elgg_echo('simplesaml:error:attribute_validation', [$label]));
	
	// make sure we don't force login
	elgg_get_session()->set('simplesaml_disable_sso', true);
	
	forward();
}

// save the attributes for further use
elgg_get_session()->set('saml_attributes', $saml_attributes);
elgg_get_session()->set('saml_source', $source);

// make sure we can find all users (even unvalidated)
$user = elgg_call(ELGG_SHOW_DISABLED_ENTITIES, function() use ($source, $saml_attributes) {
	return simplesaml_find_user($source, $saml_attributes);
});

if (!empty($user)) {
	// found a user, so login
	try {
		// check for the persistent login plugin setting
		$persistent = false;
		if (elgg_get_plugin_setting("{$source}_remember_me", 'simplesaml')) {
			$persistent = true;
		}
		
		// login the user
		login($user, $persistent);
		
		// forward to correct place
		$forward_url = elgg_get_session()->remove('last_forward_from');
		
		system_message(elgg_echo('loginok'));
	} catch (Exception $e) {
		// make sure we don't force login
		elgg_get_session()->set('simplesaml_disable_sso', true);
		
		throw new Elgg\HttpException($e->getMessage(), ELGG_HTTP_FORBIDDEN, $e);
	}
	
	// unset session vars
	elgg_get_session()->remove('saml_attributes');
	elgg_get_session()->remove('saml_source');
} else {
	// check if we can automaticly create an account for this user
	if (simplesaml_check_auto_create_account($source, $saml_attributes)) {
		// we have enough information to create the account so let's do that
		$forward_url = elgg_generate_action_url('simplesaml/register', [
			'saml_source' => $source,
		]);
	} else {
		// no user found, so forward to a different page
		$forward_url = "saml/no_linked_account/{$source}";
		
		system_message(elgg_echo('simplesaml:login:no_linked_account', [$label]));
	}
}

forward($forward_url);
