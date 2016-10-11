<?php

require_once(dirname(__FILE__)."/../../../../_config.php");

// ---------------------------------------------------------
 $app_name = "phpJobScheduler";
 $phpJobScheduler_version = "3.9";
// ---------------------------------------------------------

define('DBHOST', $DBServer);// database host address - localhost is usually fine
define('DBNAME', $DBName);// database name - must already exist
define('DBUSER', $DBUser);// database username - must already exist
define('DBPASS', $DBPass);// database password for above username

define('DEBUG', false);// set to false when done testing