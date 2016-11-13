<?php
require_once(dirname(__FILE__)."/_config.php");
require_once(dirname(__FILE__)."/php/_helpers.php");

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
<section>
    <div class="container">
         <div class="section-heading section-order">
                <h1>Ingresar</h1>
                <div class="divider"></div>
                    <?php 
                              if($error){
                                  echo '
                                  <div class="alert alert-danger">
                                    <strong>¡Error!</strong> '.$error.'
                                  </div>
                                ';
                              }
                              
                ?>
                <form method="post" action="login.php"> 
                    <div class="row item checkout-block border-between">
                        <div class="col col-sm-6">
                            <p>con tu email y contraseña</p>
                            <div class="input-group">
                                <span class="input-group-addon">@</span>
                                <input type="email" name="email" class="form-control" placeholder="Email" maxlength="124" required/>
                            </div>
                            <div class="input-group">
                                <span class="input-group-addon">&nbsp*&nbsp</span>
                                <input type="password" name="password" class="form-control" placeholder="Contraseña" maxlength="124" required/>
                            </div>
                              
                            <input type="submit" class="btn btn-info" value="Log-in" />
                            
                           
                        </div>
                        <div class="col col-sm-6">
                            <p>o utilizando tu cuenta</p>
                            <a href="login.php?provider=facebook" class="btn btn-block btn-social btn-facebook">
                                <span class="fa fa-facebook"></span>
                                 Facebook
                            </a>
                            <a href="login.php?provider=google" class="btn btn-block btn-social btn-google">
                                <span class="fa fa-google"></span>
                                Google
                            </a>
                        </div>	
                    </div>
                     <div class="row item">
                             <p>
                                ¿Primera vez? <b><a href="register.php">Regístrate aquí</a></b>
                           </p>
                           <p>
                                ¿Olvidaste tu contraseña? <a href="password_restore.php">Recuperar contraseña</a>
                           </p>
                        </div>
                    </div>
                </form>
        </div>
    </div>
</section>

<?php
}
else
{
?>
<section>
    <div class="container">
      <div class="section-heading section-order">
          <form id="checkout_form" method="post" action="<?php echo $GLOBALS['TBK_AUTHORIZE_ONECLICK'];?>">
            <div class="row item checkout-block">
                <div class="input-group-vertical">
                    <p>Monto a pagar: $<?php echo $order->ActualPriceWithDiscount ?></p>
                </div>
                <div class="input-group-horizontal">
                    <div class="payelement">
                        <div class="logofield" style="padding-top:5px;"><img class="webpay-logo" src="img/oneclick.png" height="80"></div>
                    </div>
                </div>
            </div>
            <div class="row item checkout-block oneclick_tab">
                <div class="input-group-vertical">
                    <p>Elige La tarjeta de pago</p>
                </div>
                <div class="input-group-horizontal">
                    <div class="tc_input_row">
                        <select name="TBK_USER" class="form-control" required>
                            <option value="-1">Seleccione la tarjeta de pago</option>
                            <?php 
                                $providers = OneClick::GETPROVIDERS();
                                foreach ($providers as $provider) {
                                    echo '<option value="'.$provider['TBK_USER'].'" >('.strtoupper($provider['CREDIT_CARD_TYPE']).')  XXXX XXXX XXXX '.$provider['LAST4NUMBER'].'</option>';
                                }
                            ?>                               
                        </select>
                    </div>
                    <div class="tc_add_row">
                        <button type="button" class="add_tc_btn" id="add_tc_action">+ Agregar tarjeta</button>
                    </div>
                </div>
            </div>
            <div class="row item checkout_footer oneclick_tab">
                <button type="submit" class="pay_btn hvr-glow">CONFIRMAR PEDIDO</button>
            </div>
          </form>
        </div>
      </div>
    </div>
</section>
<script>
$(document).ready(function() {
  $("#add_tc_action").on("click", function(){
      location.href="php/transbank/ep_webpay.php?action=ONECLICK_INSCRIPTION";
  });
});
</script>
<?php
}
    include_once(dirname(__FILE__)."/templates/footer.general.php");
?>