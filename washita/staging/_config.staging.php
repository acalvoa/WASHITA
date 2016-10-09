<?php

$site_root = 'http://washita.teamnodes.com';
$DBServer = 'localhost';
$DBName   = 'washita';


$smtpOrderVina = "eduardo.labarca@gmail.com";
$smtpOrderBccVina = "eduardo.labarca@gmail.com,eduardo.labarca@singularityu.org"; // additional copy
$smtpOrderSantiago = "eduardo.labarca@gmail.com";
$smtpOrderBccSantiago = "eduardo.labarca@gmail.com,eduardo.labarca@singularityu.org"; // additional copy

$smtpAdmin = 'eduardo.labarca@gmail.com'; // where to send email
$smtpAdminCc = 'eduardo.labarca@gmail.com'; // where to send email

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
$ONECLICK_URL_INSCRIPTION = $site_root+"/php/transbank/ep_webpay.php?action=FINISH_ONECLICK_INSCRIPTION";
/***************************/
?>