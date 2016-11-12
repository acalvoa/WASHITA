<?php
require_once(dirname(__FILE__)."/_config.php");
require_once(dirname(__FILE__)."/php/_helpers.php");

require_once(dirname(__FILE__)."/php/recaptcha/recaptchalib.php");
require_once(dirname(__FILE__)."/php/hybridauth/WashitaHybridAuth.php");
include_once(dirname(__FILE__)."/php/MailService.class.php");

    
$error = null;

if($_POST){   
    global $reCaptchaError;
     
    $email = GetPostNoLongerThan('email',124);
    if (!IsEmail($email))
	{
		echo "Email incorrecto!";
		exit();
	}

    $recaptcha_response_field = GetPostNoLongerThan('recaptcha_response_field',100);
    if(empty($recaptcha_response_field)){
        $error = "Error captcha";
    }
    else{
        $resp = recaptcha_check_answer ($reCaptchaPrivateKey,
                                        $_SERVER["REMOTE_ADDR"],
                                        $_POST["recaptcha_challenge_field"],
                                        $recaptcha_response_field);
        if (!$resp->is_valid) {
            $error = $resp->error;
        }
    }
    
    if($error == null){
        $auth = new WashitaHybridAuth();
        
        $id = $auth->GetUserIdByEmail($email);
        
        if($id){
            $result = $auth->SetTemporaryCodeToUpdatePasswordForUser($id);
            if($result->success){
                $temp_code = $result->value;
                $mailService = new MailService();
                $emailResultAboutPayment = $mailService->SendRestorePasswordLink($email, $temp_code);
                if(!$emailResultAboutPayment){
                    $error = "ERROR. Problema al enviar el email.";
                }
            }
        }               
    }
}  


$LINKS = '
<link rel="stylesheet" href="css/bootstrap-social.css">
';
    include_once(dirname(__FILE__)."/templates/header.general.php");


?>
<section>
    <div class="container">
         <div class="section-heading section-order">
                <h1>Recuperar contraseña</h1>
                <div class="divider"></div>
                <?php 
                            if($error){
                                  echo '
                                  <div class="alert alert-danger">
                                    <strong>Error!</strong> '.$error.'
                                  </div>
                                ';
                              }
                              else if($_POST){
                                  echo '
                                  <div class="alert alert-success">
                                    El mensaje fue enviado a tu correo.
                                  </div>
                                ';
                              }
                ?>
                <form method="post"> 
                    <div class="row item checkout-block border-between text-left">
                            <p>Ingresa tu email</p>
                            <div class="input-group">
                                <span class="input-group-addon">@</span>
                                <input type="email" name="email" class="form-control" placeholder="" maxlength="124" required/>
                            </div>
                            <?php 
                            echo recaptcha_get_html($reCaptchaPublicKey, $error); 
                            ?>
                            <input type="submit" class="btn btn-info" value="Recuperar" />
                    </div>
                </form>
        </div>
    </div>
</section>

<?php
$SCRIPTS_FOOTER.= "<script src='https://www.google.com/recaptcha/api.js'></script>";

include_once(dirname(__FILE__)."/templates/footer.general.php");
?>