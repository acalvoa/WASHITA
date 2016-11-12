<?php
require_once(dirname(__FILE__)."/php/_helpers.php");
require_once(dirname(__FILE__)."/php/hybridauth/WashitaUser.php");

$subscriptionRequest = GetGet("subscription");
$_SESSION["subscription_request"]= $subscriptionRequest;


$sessionUser = WashitaUser::CurrentSessionUser();
if($subscriptionRequest && $sessionUser!= null && $sessionUser->IsComplete){
    RedirectToPage("usersettings_subscription.php");
}
else{
    RedirectToPage("usersettings_profile.php");
}

