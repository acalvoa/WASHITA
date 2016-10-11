<?php

include_once(dirname(__FILE__)."/../../_config.php");

/**
 * HybridAuth
 * http://hybridauth.sourceforge.net | http://github.com/hybridauth/hybridauth
 * (c) 2009-2015, HybridAuth authors | http://hybridauth.sourceforge.net/licenses.html
 */
// ----------------------------------------------------------------------------------------
//	HybridAuth Config file: http://hybridauth.sourceforge.net/userguide/Configuration.html
// ----------------------------------------------------------------------------------------

$Hybrid_Auth_Config =
		array(
			"base_url" => "http://".$_SERVER['HTTP_HOST']."/php/hybridauth/",
			"providers" => array(
				// openid providers
				"OpenID" => array(
					"enabled" => false
				),
				"Yahoo" => array(
					"enabled" => false,
					"keys" => array("key" => "", "secret" => ""),
				),
				"AOL" => array(
					"enabled" => true
				),
				"Google" => array(
					"enabled" => true,
					"keys" => array("id" => "379458115064-e25ch2p11u30v7r7q9oqlobp0iknhu45.apps.googleusercontent.com", "secret" => "9fQGrAVuGgx4mzcld_JPP80I"),
                    "scope" =>  "https://www.googleapis.com/auth/userinfo.email", // optional
				),
				"Facebook" => array(
					"enabled" => true,
					"keys" => array("id" => "1174485842571344", "secret" => "c774290b1befdcf47aad07ea067ebff4"),
					"trustForwarded" => false,
                     "scope" => "email" // optional
				),
				"Twitter" => array(
					"enabled" => false,
					"keys" => array("key" => "", "secret" => ""),
					"includeEmail" => false
				),
				// windows live
				"Live" => array(
					"enabled" => false,
					"keys" => array("id" => "", "secret" => "")
				),
				"LinkedIn" => array(
					"enabled" => false,
					"keys" => array("key" => "", "secret" => "")
				),
				"Foursquare" => array(
					"enabled" => false,
					"keys" => array("id" => "", "secret" => "")
				),
			),
			// If you want to enable logging, set 'debug_mode' to true.
			// You can also set it to
			// - "error" To log only error messages. Useful in production
			// - "info" To log info and error messages (ignore debug messages)
			"debug_mode" => false,
			// Path to file writable by the web server. Required if 'debug_mode' is not false
			"debug_file" => "",
);
