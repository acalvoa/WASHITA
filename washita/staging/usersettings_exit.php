<?php

require_once(dirname(__FILE__)."/php/_helpers.php");

// Unset all of the session variables.
$_SESSION = array();

// unset cookies
if (isset($_SERVER['HTTP_COOKIE'])) {
    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
    foreach($cookies as $cookie) {
        $parts = explode('=', $cookie);
        $name = trim($parts[0]);
        setcookie($name, '', time()-1000);
        setcookie($name, '', time()-1000, '/');
    }
}

// Finally, destroy the session.
session_destroy();


RedirectToLoginPage();
