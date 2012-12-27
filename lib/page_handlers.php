<?php 

	function simplesaml_page_handler($page){
		$result = false;
		$include_file = "";
		
		switch($page[0]){
			case "login":
				$result = true;
				
				if(!empty($page[1])){
					set_input("saml_source", $page[1]);
				}
				
				$include_file = dirname(dirname(__FILE__)) . "/procedures/login.php";
				break;
			case "no_linked_account":
				$result = true;
				
				if(!empty($page[1])){
					set_input("saml_source", $page[1]);
				}
				
				$include_file = dirname(dirname(__FILE__)) . "/pages/no_linked_account.php";
				break;
			case "authorize":
				$result = true;
			
				if(!empty($page[1])){
					set_input("saml_source", $page[1]);
				}
			
				$include_file = dirname(dirname(__FILE__)) . "/procedures/authorize.php";
				break;
		}
		
		if($result && !empty($include_file)){
			include($include_file);
		}
		
		return $result;
	}