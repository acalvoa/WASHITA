<?php
//start session in all pages
if (session_status() == PHP_SESSION_NONE) { session_start(); } //PHP >= 5.4.0
//if(session_id() == '') { session_start(); } //uncomment this line if PHP < 5.4.0 and comment out line above

date_default_timezone_set("Chile/Continental");
setlocale(LC_TIME, 'es_ES');
setlocale(LC_MONETARY, 'es_ES');
setlocale(LC_NUMERIC, 'es_ES');



$site_root              = 'http://www.washita.cl/staging';

$smtpHost               = 'smtp.fatcow.com';
$smtpName               = 'orders@washita.cl'; //from what address to send
$smtpPassword           = '1234.Washita';

$smtpOrderVina = 'a-artur@yandex.ru';
// $smtpOrderBccVina = '2dot718281828@gmail.com,artur.ampilogov@gmail.com'; // additional copy
$smtpOrderSantiago = 'a-artur@yandex.ru';
// $smtpOrderBccSantiago = 'renifejuvi@stexsy.com,g1537984@mvrht.com'; // additional copy


$smtpAdmin = 'a-artur@yandex.ru'; // where to send email
$smtpAdminCc = 'a-artur@yandex.ru'; // where to send email




$DBServer = 'localhost';//'localhost'; // e.g 'localhost' or '192.168.1.100'
$DBUser   = 'root';
$DBPass   = 'shadowfax';//'washit.334411';
$DBName   = 'washita';


$OrdersNumberStart = 21000;
$OrderSendFeedbackAfterMinutes = 60; // send feedback request after 1 hour
$OrderSendFeedbackSince = '2016-04-01'; // YYYY-MM-DD min date to check 


$PricePerOneKilo = 1400; //DOES NOT includes ironing
$PricePerKiloStartingFiveKiloPack = 1200; //DOES not includes ironing
$PriceForIroningPerKilo = 3000; //only ironing
$PriceForIroningPerItem = 400; //only ironing


$Subscription_1_Kilos = 10;
$Subscription_2_Kilos = 50;
$Subscription_3_Kilos = 100;

$PriceSubscription_1 = 1000;
$PriceSubscription_2 = 2000;
$PriceSubscription_3 = 3000;

$DiscountCouponLengthMin = 6;

$DiscountInfluencerValue = 5000;
$DiscountInfluencerMaxUsageForFriends = 50; // how much times friends can apply discount
$DiscountPersonalCode = "MI_CREDITO";


$reCaptchaPublicKey = "6LemzwYUAAAAAM477SNX92cU112VADJ2v-i0A7FW";
$reCaptchaPrivateKey = "6LemzwYUAAAAACTTOQJpyP4iD2_n-s_9jDUjNqH5";
$BadLoginLimit = 5; // log in attempts before lock out
$LockOutTime = 600; //in seconds


$AdminOrdersPasswordVina = "Vina.55111";
$AdminOrdersCityIdVina = 1;

$AdminOrdersPasswordSantiago = "San99111!";
$AdminOrdersCityIdSantiago = 2;
$AdminOrdersPasswordSantiagoAndInfluencers = "San99111!123";


$AdminOrdersPassword = "3355.washita";

/**************************
*	TRANSBANK CONFIG VARIABLES
*	
*
***************************/
/* ABSOLUTE SERVER PATH*/
$TBK_MAC_PATH = "/home/washita/washita/staging/transbank/comun";
/* URL ABSOLUTE*/
$TBK_SUCCESS = "http://washita.teamnodes.com/tbk_success_payment.php";
/* URL ABSOLUTE*/
$TBK_FAIL = "http://washita.teamnodes.com/tbk_fail_payment.php";
/* URL ABSOLUTE*/
$TBK_URL_KIT = "/transbank/tbk_bp_pago.cgi";
/* CHECK MAC PATH */
$TBK_CHECK_MAC_PATH = "/home/washita/washita/staging/transbank/tbk_check_mac.cgi";
/* TYPE OF TRANSACTION*/
$TBK_TIPO_TRANSACCION = "TR_NORMAL";
/* BEGIN TRANSACTION LINK*/
$TBK_INIT_TRANS_LINK = "/php/transbank/ep_webpay.php?action=BEGIN_TRANS";
/* VERIFY TRANSACTION LINK*/
$TBK_VERIFY_TRANS_LINK = "/php/transbank/ep_webpay.php?action=VERIFY_TRANS";
/* DEFINE IF THE SYSTEM IS IN PRODUCTION MODE*/
$WSH_PROD_MODE = FALSE;
/* DEFINE THE LOG DEVELOPMENT DATABASE LOG*/
$LOG_PATH = "/home/washita/washita/staging/transbank/comun";
/* DEFINE EL PATH DEL ARCHIVO DEL CERTIFICADO DIGITAL*/
$TBK_CERT_FILE_WS = "/home/washita/washita/staging/transbank/keys/597020000547.crt";
/* DEFINE EL PATH DE LA LLAVE PRIVADA PARA WEBSERVICES TRANSBANK*/
$TBK_PRIVATE_KEY_WS = "/home/washita/washita/staging/transbank/keys/597020000547.key";
/* DEFINE EL PATH DE LA LLAVE PRIVADA PARA WEBSERVICES TRANSBANK*/
$TBK_SERVER_CERT_FILE = "/home/washita/washita/staging/transbank/keys/tbk.pem";
/* DEFINE EL LINK DEL RESULTADO DE INSCRIPCION DEL PROCESO ONECLICK*/
$ONECLICK_URL_INSCRIPTION = "http://washita.teamnodes.com/php/transbank/ep_webpay.php?action=FINISH_ONECLICK_INSCRIPTION";
/***************************/



if(file_exists(dirname(__FILE__)."/_config.localhost.php")){
    include_once(dirname(__FILE__)."/_config.localhost.php");
}
if(file_exists(dirname(__FILE__)."/_config.staging.php")){
    include_once(dirname(__FILE__)."/_config.staging.php");
}
if(file_exists(dirname(__FILE__)."/_config.production.php")){
    include_once(dirname(__FILE__)."/_config.production.php");
}

?>