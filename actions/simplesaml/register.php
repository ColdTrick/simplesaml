<?php

elgg_make_sticky_form('simplesaml/register');

if (elgg_is_logged_in()) {
	return elgg_error_response(elgg_echo('simplesaml:error:loggedin'));
}

$source = get_input('saml_source');
$session_source = elgg_get_session()->get('saml_source');
if (empty($source) || empty($session_source)) {
	return elgg_error_response(elgg_echo('simplesaml:error:no_source'));
}

$label = simplesaml_get_source_label($source);
if (!simplesaml_is_enabled_source($source)) {
	return elgg_error_response(elgg_echo('simplesaml:error:source_not_enabled', [$label]));
}

if ($source !== $session_source) {
	return elgg_error_response(elgg_echo('simplesaml:error:source_mismatch'));
}

$saml_attributes = elgg_get_session()->get('saml_attributes');
if (!simplesaml_validate_authentication_attributes($source, $saml_attributes)) {
	// not authorized
	return elgg_error_response(elgg_echo('simplesaml:error:attribute_validation', [$label]));
}

$displayname = get_input('displayname');
$user_email = get_input('email');

// prepare for registration
$name = '';
if (!empty($saml_attributes['elgg:firstname']) || !empty($saml_attributes['elgg:lastname'])) {
	$firstname = elgg_extract('elgg:firstname', $saml_attributes);
	if (is_array($firstname)) {
		$firstname = $firstname[0];
	}
	$lastname = elgg_extract('elgg:lastname', $saml_attributes);
	if (is_array($lastname)) {
		$lastname = $lastname[0];
	}
	
	if (!empty($firstname)) {
		$name = $firstname;
	}
	
	if (!empty($lastname)) {
		if (!empty($name)) {
			$name .= ' ' . $lastname;
		} else {
			$name = $lastname;
		}
	}
} elseif (!empty($displayname)) {
	$name = $displayname;
} else {
	return elgg_error_response(elgg_echo('simplesaml:action:register:error:displayname'));
}

$email = '';
$validate = false;
if (!empty($saml_attributes['elgg:email'])) {
	$email = elgg_extract('elgg:email', $saml_attributes);
	
	if (is_array($email)) {
		$email = $email[0];
	}
} elseif (!empty($user_email)) {
	$email = $user_email;
	$validate = true;
} else {
	return elgg_error_response(elgg_echo('registration:emailnotvalid'));
}

$username = elgg_extract('elgg:username', $saml_attributes);
if (is_array($username)) {
	$username = $username[0];
}

// register user
$user = simplesaml_register_user($name, $email, $source, $validate, $username);
if (empty($user)) {
	return elgg_error_response(elgg_echo('registerbad'), elgg_generate_url('default:saml:no_linked_account', [
		'saml_source' => $source,
	]));
}

// link user to the saml source
// make sure we can find hidden (unvalidated) users
elgg_call(ELGG_SHOW_DISABLED_ENTITIES, function() use ($saml_attributes, $source, $user) {
	$saml_uid = elgg_extract('elgg:external_id', $saml_attributes);
	if (!empty($saml_uid)) {
		if (is_array($saml_uid)) {
			$saml_uid = $saml_uid[0];
		}
		simplesaml_link_user($user, $source, $saml_uid);
	}
	
	// save attributes
	simplesaml_save_authentication_attributes($user, $source, $saml_attributes);
});

// cleanup session
elgg_get_session()->remove('saml_source');
elgg_get_session()->remove('saml_attributes');

$forward_url = '';

// try to login the user
try {
	// check for the persistent login plugin setting
	$persistent = false;
	if (elgg_get_plugin_setting($source . '_remember_me', 'simplesaml')) {
		$persistent = true;
	}
	
	// login the user
	login($user, $persistent);
	
	// get forward url
	$forward_url = elgg_get_session()->remove('last_forward_from');
} catch (Exception $e) {
	// make sure we don't force login
	elgg_get_session()->set('simplesaml_disable_sso', true);
}

// notify user about registration
return elgg_ok_response('', elgg_echo('registerok', [elgg_get_site_entity()->name]), $forward_url);
