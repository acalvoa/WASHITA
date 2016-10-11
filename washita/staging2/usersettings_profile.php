<?php
require_once(dirname(__FILE__)."/_config.php");
require_once(dirname(__FILE__)."/php/_helpers.php");

require_once(dirname(__FILE__)."/php/hybridauth/WashitaUserSettings.php");
require_once(dirname(__FILE__)."/php/hybridauth/WashitaUser.php");


require_once(dirname(__FILE__)."/php/Result.class.php");

$userSettings = new WashitaUserSettings();

$user = WashitaUser::CurrentUser();

if($_POST){   
    if(empty($user)){
        RedirectToLoginPage();
    }

    global $reCaptchaError;
     
    $email = GetPostNoLongerThan('email',124);
    if (!IsEmail($email))
	{
		echo "Email is incorrect!";
		exit();
	}
	$name = GetPostNoLongerThan('name',124);
    if (empty($name))
	{
		echo "Name cannot be empty!";
		exit();
	}  
    $lastname = GetPostNoLongerThan('lastname',124);
    if (empty($lastname))
	{
		echo "Lastname cannot be empty!";
		exit();
	}

    $city_area_id = GetPostNoLongerThan('city_area_id',3);
    if(empty($city_area_id)){
        echo "¡Ingrese su ciudad!";
		exit();
    }
    $address =  GetPostNoLongerThan('address', 1024); 
    $phone = GetPostNoLongerThan('whatsapp', 20);    


    $registrationCode = GetPostNoLongerThan('registration_code',30);

    $saveResult = Result::Success();

    if(!empty($registrationCode) && $user->RegistrationCode == null){
        $saveResult = $user->ApplyRegistrationCode($registrationCode);
    }

    if($saveResult->success){
        $user->Name = $name;
        $user->Lastname = $lastname;
        $user->NotificationEmail = $email;
        $user->CityAreaId = $city_area_id;
        $user->Address = $address;
        $user->Phone = $phone;
        $user->IsComplete = true;
        $saveResult = WashitaUser::SaveUser($user);
    }
    
    if($saveResult->success){
        $userSettings->SuccessMessage = "Settings are saved!";
    }
    else{
        $userSettings->ErrorMessage = $saveResult->message;
    }
 }
 
 $userSettingsHtml = $user != null? $userSettings->Html($user) : "";
    
 include_once(dirname(__FILE__)."/templates/usersettings_header.php");
    
    
?>
<section>
         <div class="section-heading section-order">
                <h1>Profile</h1>
                <div class="divider"></div>
                <form method="post"> 
                    <?php echo $userSettingsHtml ?>
                    
                    <div class="row text-right">
                        <div class="col col-sm-12">
                            <input type="submit" class="btn btn-info" value="Save" />
                        </div>
                    </div>
                </form>
        </div>
</section>

<?php
    include_once(dirname(__FILE__)."/templates/usersettings_footer.php");
?>