<?php
require_once(dirname(__FILE__)."/_config.php");
require_once(dirname(__FILE__)."/php/_helpers.php");

require_once(dirname(__FILE__)."/php/recaptcha/recaptchalib.php");
require_once(dirname(__FILE__)."/php/hybridauth/WashitaHybridAuth.php");

    
$registrationError = null;

if($_GET){
    $provider = $_GET["provider"];
    if(!empty($provider)){
        $auth = new WashitaHybridAuth();
        $result = $auth->GetUserSessionResultBySocialProvider($provider);
        
        if($result->success){
            //value contains user
            $auth->Login($result->value);
        }
        else{
            $registrationError = $result->message;
        }
       
    }
}
if($_POST){   
    global $reCaptchaError;
     
    $email = GetPostNoLongerThan('email',124);
    if (!IsEmail($email))
	{
		echo "Email is incorrect!";
		exit();
	}
	$password = GetPostNoLongerThan('password',124);
    if (empty($password))
	{
		echo "Password cannot be empty!";
		exit();
	}

	$passwordConfirmation = GetPostNoLongerThan('password_confirmation',124);
    if($password != $passwordConfirmation){
        $registrationError = "Password is different from password confirmation field!";
    }
    
    $recaptcha_response_field = GetPostNoLongerThan('recaptcha_response_field',100);
    if(empty($recaptcha_response_field)){
        $registrationError = "Wrong captcha";
    }
    else{
        $resp = recaptcha_check_answer ($reCaptchaPrivateKey,
                                        $_SERVER["REMOTE_ADDR"],
                                        $_POST["recaptcha_challenge_field"],
                                        $recaptcha_response_field);
        if (!$resp->is_valid) {
            $registrationError = $resp->error;
        }
    }
    
    if($registrationError == null){
        $auth = new WashitaHybridAuth();
        $result = $auth->RegisterByEmailAndPassword($email, $password);
        
        if($result->success){
            //value contains user
            $auth->Login($result->value);
        }
        else{
            $registrationError = $result->message;
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
                <h1>Regístrate</h1>
                <div class="divider"></div>
                <?php 
                            if($registrationError){
                                  echo '
                                  <div class="alert alert-danger">
                                    <strong>Error!</strong> '.$registrationError.'
                                  </div>
                                ';
                              }
                ?>
                <form method="post" action="register.php"> 
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
                            <div class="input-group">
                                <span class="input-group-addon">&nbsp*&nbsp</span>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="Confirma tu contraseña" maxlength="124" required/>
                            </div>
                            <div id="password_confirmation_message"></div>
                            <?php 
                            echo recaptcha_get_html($reCaptchaPublicKey, $registrationError); 
                            ?>
                            <input type="submit" class="btn btn-info" value="Registrarse" />
                        </div>
                        <div class="col col-sm-6">
                            <p>o utiliza tu cuenta de red social</p>
                            <a href="login.php?provider=facebook" class="btn btn-block btn-social btn-facebook">
                                <span class="fa fa-facebook"></span>
                                Sign-in usando Facebook
                            </a>
                            <a href="login.php?provider=google" class="btn btn-block btn-social btn-google">
                                <span class="fa fa-google"></span>
                                Sign-in usando Google
                            </a>
                        </div>	
                    </div>	
                </form>
        </div>
    </div>
</section>

<?php
$SCRIPTS_FOOTER.= "<script src='https://www.google.com/recaptcha/api.js'></script>";


$SCRIPTS_FOOTER.= '
<script>
    $(document).ready(function() {
        $(\'input[name="password_confirmation"]\').blur(function(){
            var message = "";
            if($(\'input[name="password"]\').val() != $(this).val()){
                message = \'<div class="alert alert-danger">La contraseña no coincide</div>\';
            }
            $("#password_confirmation_message").html(message);
        });
    });

</script>';


include_once(dirname(__FILE__)."/templates/footer.general.php");
?>