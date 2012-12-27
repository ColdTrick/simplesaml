<?php 

	$source = elgg_extract("saml_source", $vars);
	$allow_registration = (bool) elgg_extract("allow_registration", $vars, false);
	
	$label = simplesaml_get_source_label($source);
	
	echo "<div class='mbm'>";
	echo elgg_echo("simplesaml:no_linked_account:description", array($label));
	echo "</div>";
	
	echo "<div id='simplesaml-no-linked-account-module-wrapper'>";
	// no registration link
	$global_registration = elgg_get_config("allow_registration");
	elgg_set_config("allow_registration", false);
	
	echo elgg_view_module("popup", elgg_echo("login"), elgg_view_form("login"), array("class" => "float"));
	
	// restore registration settings
	elgg_set_config("allow_registration", $global_registration);
	
	// allow registration
	if($allow_registration){
		$body_vars = array(
			"saml_source" => $source,
		);
		
		echo elgg_view_module("popup", elgg_echo("register"), elgg_view_form("simplesaml/register", null, $body_vars), array("class" => "float-alt"));
	}
	
	echo "</div>";
	