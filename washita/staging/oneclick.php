<?php
require_once(dirname(__FILE__)."/_config.php");
require_once(dirname(__FILE__)."/php/_helpers.php");
require_once(dirname(__FILE__).'/php/Order.class.php');
require_once(dirname(__FILE__)."/php/recaptcha/recaptchalib.php");
require_once(dirname(__FILE__)."/php/hybridauth/WashitaHybridAuth.php");
include_once(dirname(__FILE__)."/php/transbank/OneClick.php");

$error = null;

if($_GET){
    $provider = $_GET["provider"];
    if(!empty($provider)){
        $auth = new WashitaHybridAuth();
        $result = $auth->GetUserSessionResultBySocialProvider($provider);
        
        if($result->success){
            //value contains user
            $auth->LoginAndGo($result->value);
        }
        else{
            $error = $result->message;
        }
       
    }
}
if($_POST){   
    global $reCaptchaError;
     
    $email = GetPostNoLongerThan('email',124);
    if (!IsEmail($email))
	{
		echo "Email incorrecto";
		exit();
	}
	$password = GetPostNoLongerThan('password',124);
    if (empty($password))
	{
		echo "Debe ingresar contraseña";
		exit();
	}   
    $auth = new WashitaHybridAuth();
     $userSessionResult = $auth->GetUserSessionResultByEmailAndPassword($email, $password);
     if($userSessionResult->success){
         $auth->LoginAndGo($userSessionResult->value);
     }
     else{
        $error = $userSessionResult->message;
     }    
}   

    $LINKS = '
    <link rel="stylesheet" href="css/bootstrap-social.css">
    ';
    include_once(dirname(__FILE__)."/templates/header.general.php");
    $orderNumber = $_GET["orderNumber"];
    $order = Order::GetOrderByNumber($orderNumber);
    $descritption = "Washita.cl, Orden N°".$order->OrderNumber.". Pago.";
$user = WashitaUser::CurrentUser();
if(is_null($user)){
?>


<?php
}
else
{
?>

 <?php 
}
  if(!isset($SCRIPTS_FOOTER)){
  $SCRIPTS_FOOTER = "";
  }
  $SCRIPTS_FOOTER.= '
  <script>
  $(document).ready(function() {
    $("#add_tc_action").on("click", function(){
        location.href="php/transbank/ep_webpay.php?action=ONECLICK_INSCRIPTION&order=<?php echo $order->OrderNumber; ?>";
    });
    $("#rm_tc_action").on("click", function(){
        location.href="php/transbank/ep_webpay.php?action=ONECLICK_REMOVE_INSCRIPTION&order=<?php echo $order->OrderNumber; ?>";
    });
  });
  </script>';
    include_once(dirname(__FILE__)."/templates/footer.general.php");
?>