<?php
require_once(dirname(__FILE__)."/_config.php");
require_once(dirname(__FILE__)."/php/_helpers.php");

require_once(dirname(__FILE__)."/php/recaptcha/recaptchalib.php");
require_once(dirname(__FILE__)."/php/hybridauth/WashitaHybridAuth.php");

    
$error = null;

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
        $error = "Password is different from password confirmation field!";
    }
    
    $recaptcha_response_field = GetPostNoLongerThan('recaptcha_response_field',100);
    if(empty($recaptcha_response_field)){
        $error = "Wrong captcha";
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
        $result = $auth->SetPassword($email, $password, $_GET['temp_code']);
        
        if($result->success){
            RedirectToMessagePage("Password","<p>The password is changed.</p>");
        }
        else{
            $error = 'The password is not changed. The data is incorrect or temporary code is outdated';
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
                <h1>Change password</h1>
                <div class="divider"></div>
                <?php 
                            if($error){
                                  echo '
                                  <div class="alert alert-danger">
                                    <strong>Error!</strong> '.$error.'
                                  </div>
                                ';
                              }
                ?>
                <form method="post"> 
                    <div class="row item checkout-block border-between">
                        <div class="col col-sm-6">
                            <p>Email</p>
                            <div class="input-group">
                                <span class="input-group-addon">@</span>
                                <input type="email" name="email" class="form-control" placeholder="Email" maxlength="124" required
                                 value="<?php echo $_GET['email']?>"/>
                            </div>
                             <p>New password</p>
                            <div class="input-group">
                                    <span class="input-group-addon">&nbsp*&nbsp</span>
                                    <input type="password" name="password" class="form-control" placeholder="Password" maxlength="124" required/>
                            </div>
                            <div class="input-group">
                                <span class="input-group-addon">&nbsp*&nbsp</span>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="Password confirmation" maxlength="124" required/>
                            </div>
                            <div id="password_confirmation_message"></div>

                                <input type="hidden" name="temp_code" class="form-control" placeholder="Code" maxlength="225" required 
                                    value="<?php echo $_GET['temp_code']?>"/>
                            <?php 
                            echo recaptcha_get_html($reCaptchaPublicKey, $error); 
                            ?>
                        </div>
                       
                        
                    </div>
                    <div class="row item">
                        <input type="submit" class="btn btn-info" value="Change" />
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