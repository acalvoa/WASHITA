<?php
require_once(dirname(__FILE__)."/../_config.php");
require_once(dirname(__FILE__)."/DiscountCoupon.class.php");
require_once(dirname(__FILE__)."/City.class.php");

function allExceptionHandler($exception) {
    error_log("Unhandled exception!", 3, dirname(__FILE__)."/../logs/errors.log");
    
    RedirectToErrorPage("", "Fatal eror occured");
}
// set_exception_handler('allExceptionHandler');


function IsSamePagePost(){
    return parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH) == parse_url($_SERVER['PHP_SELF'], PHP_URL_PATH);
}

function GetGet($name){
    return isset($_GET[$name]) ? trim($_GET[$name]) : "";
}
function GetPost($name){
    return isset($_POST[$name]) ? trim($_POST[$name]) : "";
}
function GetPostNoLongerThan($name, $maxLength){
    return mb_strimwidth(GetPost($name), 0, $maxLength, "");
}

function GetBooleanPost($name){
    return isset($_POST[$name]) && (($_POST[$name] != "false") && $_POST[$name]);
}
function BooleanToYesOrNo($boolean){
       return $boolean? "Sí": "No";
}
function IsEmail($email){
   return preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", $email);
}

function IsPhone($phone){
   return preg_match("/^[\s+0-9-]+$/i", $phone);    
}

function MoneyFormat($number, $decimals = 0){
    return "$".number_format($number, $decimals, ',', '.');
}

function NumberFormat($number){
    $res = number_format($number, 2, ',', '.');
    return str_replace(",00", "", $res);
}

function NumberFromChileanString($str){
    return str_replace('.', '', $str);
}



function OpenMysqlConnection(){
         $mysqli = new mysqli($GLOBALS["DBServer"], $GLOBALS["DBUser"], $GLOBALS["DBPass"], $GLOBALS["DBName"]); 
        /* check connection */
        if ($mysqli->connect_error) {
            RedirectToErrorPage(null, "Internal error 001");
            exit();
        }
        $mysqli->set_charset("utf8");
	            
        return $mysqli;
}

function OpenPDOConnection(){
    // connect with pdo 
    try {
        $dbh = new PDO("mysql:host=".$GLOBALS["DBServer"].";dbname=".$GLOBALS["DBName"].";", $GLOBALS["DBUser"], $GLOBALS["DBPass"]);
    }
    catch(PDOException $e) {
        RedirectToErrorPage(null, "Internal error 001");
        exit();
        //die('pdo connection error: ' . $e->getMessage());
    }
	            
    return $dbh;
}


function RedirectToErrorPage($orderNumber,$text){
    // Redirect to success page
    // header("Location: ".$GLOBALS['site_root']."/error.php?order_number=".urlencode($orderNumber)."&text=".urlencode($text)); 
    exit();
}

function RedirectToMessagePage($header,$text){
    // Redirect to success page
    header("Location: ".$GLOBALS['site_root']."/message.php?header=".urlencode($header)."&text=".urlencode($text)); 
    exit();
}

function RedirectToSuccessPaymentPage($orderNumber,$pickuptime){
    // Redirect to success page
    header("Location: ".$GLOBALS['site_root']."/success_payment.php?order_number=".urlencode($orderNumber)."&pickuptime=".urlencode($pickuptime)); 
    exit();
}

function RedirectToSuccessOrderPage($orderNumber,$pickuptime){
    // Redirect to success page
    header("Location: ".$GLOBALS['site_root']."/success_order.php?order_number=".urlencode($orderNumber)."&pickuptime=".urlencode($pickuptime)); 
    exit();
}

function RedirectToUserSettingsPage(){
    header("Location: ".$GLOBALS['site_root']."/usersettings.php"); 
    exit();
}

function RedirectToProcessPage(){
    header("Location: ".$GLOBALS['site_root']."/process.php"); 
    exit();
}

function RedirectToLoginPage(){
    header("Location: ".$GLOBALS['site_root']."/login.php"); 
    exit();
}

function CreateFullUrl($relativePath){
    return $GLOBALS['site_root']."/".$relativePath;
}
function RedirectToPage($relativePath){
    header("Location: ".CreateFullUrl($relativePath)); 
    exit();
}

function RedirectToPageByJs($relativePath){
    $url = CreateFullUrl($relativePath);
    echo $url;
    echo "<script> window.location.replace('".$url."'); </script>"; 
    exit();
}


function DoesCurrentUrlContains($str){
    return strpos($_SERVER['REQUEST_URI'], $str) !== false;
}


function GetTempLinkForPasswordChange($email, $temp_code){
    return $GLOBALS['site_root']."/password_change.php?email=".urlencode($email)."&temp_code=".urlencode($temp_code); 
}

function CreateDateTimeImmutableFromMutable($dateTime){
    return DateTimeImmutable::createFromFormat("d-m-YYYY H:i:s",$dateTime->format("d-m-YYYY H:i:s"));
    //we can use createFromMutable starting from PHP 5.6.0. Current fatcow version is 5.5.0
    //$currentDatetime = DateTimeImmutable::createFromMutable(CurrentDateTime::Now());
}

function ChangeStringDateFormat($fromFormat,$toFormat,$string){
    $d = DateTimeImmutable::createFromFormat($fromFormat,$string);
    return $d->format($toFormat);
}



  

abstract class OrderPaymentStatus
{
    const NotPaid = 0;
    const FailedOrPartialPayment = 1;
    const Paid = 2;
    
    public static function ToString($value){
          switch ($value) {
            case OrderPaymentStatus::NotPaid:
                return "No Pagado";
            case OrderPaymentStatus::FailedOrPartialPayment:
                return "Failed or partial payment";
            case OrderPaymentStatus::Paid:
                return "Pagado";
            default:
                return "Unknown";
        }
    }
}



//    # 0 - request for transaction confirmation from flow service (confirma page)
//    # 1 - successful end of payment (exito page)  
//    # 2 - failed end of payment (fracaso page)   
abstract class FlowResponseType
{
    const TransactionConfirmationRequest = 0;
    const FailedPayment = 1;
    const SuccessedPayment = 2;
}
?>