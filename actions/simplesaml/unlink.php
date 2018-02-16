<?php

$source = get_input('source');
$user_guid = (int) get_input('user_guid');

if (empty($source) || empty($user_guid)) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

$user = get_user($user_guid);
if (empty($user) || !$user->canEdit()) {
	return elgg_error_response(elgg_echo('actionunauthorized'));
}

$label = simplesaml_get_source_label($source);

if (!simplesaml_is_enabled_source($source)) {
	return elgg_error_response(elgg_echo('simplesaml:error:source_not_enabled', [$label]));
}

if (!simplesaml_unlink_user($user, $source)) {
	return elgg_error_response(elgg_echo('simplesaml:action:unlink:error', [$label]));
}

return elgg_ok_response('', elgg_echo('simplesaml:action:unlink:success', [$label]));
