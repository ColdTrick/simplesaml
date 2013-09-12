<?php

	function simplesaml_get_source_label($source){
		$result = $source;
		
		if(!empty($source)){
			$lan_key = "simplesaml:sources:label:" . $source;
			
			if(elgg_echo($lan_key) != $lan_key){
				$result = elgg_echo($lan_key);
			}
		}
		
		return $result;
	}
	
	function simplesaml_get_configured_sources(){
		static $result;
		
		if(!isset($result)){
			$result = false;
			
			if(class_exists("SimpleSAML_Auth_Source")){
				if($sources = SimpleSAML_Auth_Source::getSources()){
					$result = $sources;
				}
			}
		}
		
		return $result;
	}
	
	function simplesaml_get_enabled_sources(){
		static $result;
		
		if(!isset($result)){
			$result = false;

			if($sources = simplesaml_get_configured_sources()){
				$enabled_sources = array();
				
				foreach($sources as $source){
					if(elgg_get_plugin_setting($source . "_enabled", "simplesaml")){
						$enabled_sources[] = $source;
					}
				}
				
				if(!empty($enabled_sources)){
					$result = $enabled_sources;
				}
			}
		}
		
		return $result;
	}
	
	function simplesaml_get_source_icon_url($source){
		$result = false;
		
		if(!empty($source)){
			if($setting = elgg_get_plugin_setting($source . "_icon_url", "simplesaml")){
				$result = $setting;
			}
		}
		
		return $result;
	}
	
	function simplesaml_is_enabled_source($source){
		$result = false;
		
		if(!empty($source) && ($enabled_sources = simplesaml_get_enabled_sources())){
			if(in_array($source, $enabled_sources)){
				$result = true;
			}
		}
		
		return $result;
	}
	
	function simplesaml_find_user($source, $saml_attributes){
		$result = false;
		
		if(!empty($source) && !empty($saml_attributes) && is_array($saml_attributes)){
			$saml_uid = elgg_extract("elgg:external_id", $saml_attributes);
			if(is_array($saml_uid)){
				$saml_uid = $saml_uid[0];
			}
			
			if(!empty($saml_uid)){
				$options = array(
					"type" => "user",
					"limit" => 1,
					"site_guids" => false,
					"plugin_id" => "simplesaml",
					"plugin_user_setting_name_value_pairs" => array(
						$source . "_uid" => $saml_uid
					)
				);
				
				if($users = elgg_get_entities_from_plugin_user_settings($options)){
					$result = $users[0];
				}
			}
		}
		
		return $result;
	}
	
	function simplesaml_allow_registration($source){
		$result = false;
		
		if(!empty($source)){
			if(elgg_get_plugin_setting($source . "_allow_registration", "simplesaml")){
				$result = true;
			}
		}
		
		return $result;
	}
	
	function simplesaml_unextend_login_form(){
		global $CONFIG;
		
		if(isset($CONFIG->views)){
			if(isset($CONFIG->views->extensions)){
				
				if(isset($CONFIG->views->extensions["forms/login"])){
					unset($CONFIG->views->extensions["forms/login"]);
				}
				
				if(isset($CONFIG->views->extensions["login/extend"])){
					unset($CONFIG->views->extensions["login/extend"]);
				}
			}
		}
	}
	
	function simplesaml_get_authentication_attributes(SimpleSAML_Auth_Simple $saml_auth, $source){
		$result = false;
		
		if(!empty($saml_auth) && ($saml_auth instanceof SimpleSAML_Auth_Simple) && !empty($source)){
			$result = $saml_auth->getAttributes();
			
			if($setting = elgg_get_plugin_setting($source . "_external_id", "simplesaml")){
				if($external_id = $saml_auth->getAuthData($setting)){
					$result["elgg:external_id"] = array($external_id["Value"])	;
				}
			}
		}
		
		return $result;
	}
	
	function simplesaml_link_user(ElggUser $user, $saml_source, $saml_uid){
		$result = false;
		
		if(!empty($user) && elgg_instanceof($user, "user", null, "ElggUser") && !empty($saml_source) && !empty($saml_uid)){
			if(simplesaml_is_enabled_source($saml_source)){
				// remove links from other users
				$options = array(
					"type" => "user",
					"limit" => false,
					"site_guids" => false,
					"plugin_id" => "simplesaml",
					"plugin_user_setting_name_value_pairs" => array(
						$saml_source . "_uid" => $saml_uid
					)
				);
				
				if($users = elgg_get_entities_from_plugin_user_settings($options)){
					foreach($users as $other_user){
						simplesaml_unlink_user($other_user, $saml_source);
					}
				}
				
				// now save the setting for this user
				$result = elgg_set_plugin_user_setting($saml_source . "_uid", $saml_uid, $user->getGUID(), "simplesaml");
			}
		}
		
		return $result;
	}
	
	function simplesaml_unlink_user(ElggUser $user, $saml_source){
		$result = false;
		
		if(!empty($user) && elgg_instanceof($user, "user", null, "ElggUser") && !empty($saml_source)){
			// cleanup the saml attributes
			simplesaml_save_authentication_attributes($user, $saml_source);
			
			// remove the link to the user
			$result = elgg_unset_plugin_user_setting($saml_source . "_uid", $user->getGUID(), "simplesaml");
		}
		
		return $result;
	}
	
	function simplesaml_register_user($name, $email, $saml_source, $validate = false){
		$result = false;
		
		if(!empty($name) && !empty($email) && !empty($saml_source)){
			// create a username from email
			if($username = simplesaml_generate_username_from_email($email)){
				// generate a random password
				$password = generate_random_cleartext_password();
				
				try {
					if($user_guid = register_user($username, $password, $name, $email)){
						$new_user = get_user($user_guid);
						
						if($validate){
							$params = array(
								"user" => $new_user,
								"password" => $password,
								"friend_guid" => $friend_guid,
								"invitecode" => $invitecode
							);
							
							if(!elgg_trigger_plugin_hook("register", "user", $params, true)){
								register_error(elgg_echo("registerbad"));
							} else {
								$result = $new_user;
							}
						} else {
							$result = $new_user;
						}
					}
				} catch(Exception $e){
					register_error($e->getMessage());
				}
			} else {
				register_error(elgg_echo("registration:usernamenotvalid"));
			}
		}
		
		return $result;
	}
	
	function simplesaml_generate_username_from_email($email){
		$result = false;
		
		if(!empty($email) && validate_email_address($email)){
			list($username, $dummy) = explode("@", $email);
			
			// filter invalid characters from username from validate_username()
			$username = preg_replace('/[^a-zA-Z0-9]/', "", $username);
				
			// check for min username length
			$minchars = (int) elgg_get_config("minusername");
			if (empty($minchars)) {
				$minchars = 4;
			}
				
			$username = str_pad($username, $minchars, "0", STR_PAD_RIGHT);
				
			// we have to be able to see all users
			$hidden = access_get_show_hidden_status();
			access_show_hidden_entities(true);
			
			$i = 1;
			// does this username exist
			if(get_user_by_username($username)){
				// make a new one
				while(get_user_by_username($username . $i)){
					$i++;
				}
				
				$result = $username . $i;
			} else {
				$result = $username;
			}
			
			// restore hidden entities
			access_show_hidden_entities($hidden);
		}
		
		return $result;
	}
	
	function simplesaml_save_authentication_attributes(ElggUser $user, $saml_source, $attributes = false) {
	
		if (!empty($user) && elgg_instanceof($user, "user") && !empty($saml_source) && simplesaml_is_enabled_source($saml_source)) {
			// remove the current attrributes
			elgg_unset_plugin_user_setting($saml_source . "_attributes", $user->getGUID(), "simplesaml");
			
			// are we allowed to save the attributes
			if (elgg_get_plugin_setting($saml_source . "_save_attributes", "simplesaml")) {
				// save settings
				if (!empty($attributes) && is_array($attributes)) {
					// filter some keys out of the attributes
					unset($attributes["elgg:firstname"]);
					unset($attributes["elgg:lastname"]);
					unset($attributes["elgg:email"]);
					unset($attributes["elgg:external_id"]);
		
					elgg_set_plugin_user_setting($saml_source . "_attributes", json_encode($attributes), $user->getGUID(), "simplesaml");
				}
			}
		}
	}
	
	function simplesaml_get_authentication_user_attribute($saml_source, $attribute_name = false, $user_guid = 0) {
		$result = false;
		
		$user_guid = sanitise_int($user_guid, false);
		if (empty($user_guid)) {
			$user_guid = elgg_get_logged_in_user_guid();
		}
		
		if (!empty($user_guid)) {
			if (!empty($saml_source) && simplesaml_is_enabled_source($saml_source)) {
				if ($attributes = elgg_get_plugin_user_setting($saml_source . "_attributes", $user_guid, "simplesaml")) {
					$attributes = json_decode($attributes, true);
					
					if (!empty($attribute_name)) {
						$result = elgg_extract($attribute_name, $attributes, false);
					} else {
						$result = $attributes;
					}
				}
			}
		}
		
		return $result;
	}