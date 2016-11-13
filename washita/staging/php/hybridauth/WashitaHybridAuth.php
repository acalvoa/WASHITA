<?php
    include_once(dirname(__FILE__)."/../_helpers.php");
    require_once(dirname(__FILE__)."/../Result.class.php");
    
    require_once(dirname(__FILE__)."/config.php");
    require_once(dirname(__FILE__)."/Hybrid/Auth.php");
    
    require_once(dirname(__FILE__)."/WashitaUser.php");
    require_once(dirname(__FILE__)."/UserType.enum.php");
    require_once(dirname(__FILE__)."/WashitaUserSession.php");
    require_once(dirname(__FILE__)."/WashitaUserSettings.php");
    require_once(dirname(__FILE__)."/RegistrationCode.class.php");
    
class WashitaHybridAuth{

    /**
     * @param string $email
     * @param string $password
     * @return Result
     */
	public function GetUserSessionResultByEmailAndPassword( $email, $password )
	{
        global $DBName;
        global $BadLoginLimit;
        global $LockOutTime;
        
        $result = new Result();
        $result->success = false;  


        $mysqli = OpenMysqlConnection(); 
        $userId = null;
        try {
            $query = "SELECT * FROM `".$DBName."`.users WHERE EMAIL = '".$mysqli->real_escape_string($email)."'";
            $sql_result = $mysqli->query($query);
            if($sql_result){
                $row = $sql_result->fetch_assoc();
                
                // Check login attempts
                if($row["FAILED_LOGIN_COUNT"] >= $BadLoginLimit &&
                    ((time() - $row["FIRST_FAILED_LOGIN_TIME"]) < $LockOutTime)
                  ){
                    $result->message = "The account is locked. Too many attempts of incorrect password. Have another try later or restore the password.";  
                }
                elseif(crypt($password, $row["PASSWORD"]) == $row["PASSWORD"]) {
                    // password is correct
                    // reset failed login
                    $updateLoginQuery = $this->GetQueryToSetFailedLogin($mysqli->real_escape_string($email), 0, 0);
                    if($mysqli->query($updateLoginQuery)){
                        $result->success = true;  
                        $userId = $row["ID"];
                    }
                    else{
                        $result->message = "Internal login error 1.";  
                    }
                }
                else{  // password is incorrect
                    $result->message = "User or password is incorrect";  
                    
                    $faileLoginTime = $row["FIRST_FAILED_LOGIN_TIME"] > 0? null : time();
                    $faileLoginCount = $row["FAILED_LOGIN_COUNT"]+1;
                    $updateLoginQuery = $this->GetQueryToSetFailedLogin($mysqli->real_escape_string($email), $faileLoginCount, $faileLoginTime);
                    
                    
                    if(!$mysqli->query($updateLoginQuery)){
                        $result->message = "Internal login error 2.";  
                    }
                }
                $sql_result->free();
            }
            else{
                $result->message = "User or password is incorrect";  
            }
        }
        catch(Exception $e) {
                $result->message = "Internal login error 4.";  
        }
        $mysqli->close();
        
        if($result->success){
            $user = WashitaUser::GetUserById($userId);
            if($user != null){
                $result->value = $user->ConverToUserSession();
            }  
        }  
        
		return $result;
	}
    
    private function GetQueryToSetFailedLogin($email,$count,$firstFailedLoginTime =null){
        global $DBName;
        if($firstFailedLoginTime == null){
            return "UPDATE `".$DBName."`.users SET FAILED_LOGIN_COUNT='".$count."' WHERE EMAIL = '".$email."'";
        }
        else{
            return "UPDATE `".$DBName."`.users SET FAILED_LOGIN_COUNT='".$count."', FIRST_FAILED_LOGIN_TIME='".$firstFailedLoginTime."' WHERE EMAIL = '".$email."'";            
        }
    }
    
    public function Login(WashitaUserSession $user)
	{
        WashitaUser::SaveUserDataInSession($user);
        
        if(!$user->IsComplete || 
            (isset($_SESSION["subscription_request"]) && $_SESSION["subscription_request"])){
            // redirect to settings
            RedirectToUserSettingsPage();
        }
        else{
            RedirectToProcessPage();
        }
    }
    public function LoginAndGo(WashitaUserSession $user)
    {
        WashitaUser::SaveUserDataInSession($user);
        return true;
    }
    public function GetUserIdByEmail( $email )
	{
        global $DBName;
        
        $mysqli = OpenMysqlConnection(); 
        
        $id = null;
        try {
            
            $query = "SELECT ID FROM `".$DBName."`.users WHERE email = '".$mysqli->real_escape_string($email)."'";
            $sql_result = $mysqli->query($query);
            
            if($sql_result){
                $row = $sql_result->fetch_assoc();
                $id = $row["ID"];
                $sql_result->free();
            }
        }
        catch(Exception $e) {
            
        }
        
        $mysqli->close();
        
		return $id;
	}

    /**
     * @param string $email
     * @param string $password
     * @return Result
     */
    public function RegisterByEmailAndPassword($email, $password)
	{
        
        global $DBName;
        
        $id = $this->GetUserIdByEmail($email);
        
        if($id){
            return Result::Fail("User is already registered!");
        }
        
        $password_hashed = $this->better_crypt($password);

        $result = new Result();
        $mysqli = OpenMysqlConnection(); 
        $query =  
			"INSERT INTO `".$DBName."`.users
			( 
				EMAIL, 
                NOTIFICATION_EMAIL,
				PASSWORD
			) 
			VALUES
			( 
				'$email',
				'$email',
				'$password_hashed'
			)"
		;
        if($mysqli->query($query))
        {
            $result->success = true;
            $user = WashitaUserSession::Create($mysqli->insert_id,'','',false, UserType::Usual);
		    $result->value = $user;
        }
		else{
            $result = Result::Fail("Internal error 'Register by Email DB'");
        }
        $mysqli->close();
        return $result;
	}
    
    private function better_crypt($input, $rounds = 8)
    {
        $salt = "";
        $salt_chars = array_merge(range('A', 'Z'), range('a', 'z'), range(0, 9));
        for ($i = 0; $i < 22; $i++) {
            $salt .= $salt_chars[array_rand($salt_chars)];
        }
        return crypt($input, sprintf('$2a$%02d$', $rounds) . $salt);
    }
    
    private function getUserIdByProvider( $provider_name, $provider_uid )
	{
        global $DBName;
        
        $mysqli = OpenMysqlConnection(); 
        $id = null;
        try {
            
            $query = "SELECT ID FROM `".$DBName."`.users WHERE AUTH_PROVIDER_NAME = '".$mysqli->real_escape_string($provider_name)."'
                         AND AUTH_PROVIDER_UID='".$mysqli->real_escape_string($provider_uid)."'";

            $sql_result = $mysqli->query($query);
            if($sql_result){
                $row = $sql_result->fetch_assoc();
                $id = $row["ID"];
                $sql_result->free();
            }
        }
        catch(Exception $e) {
            
        }
        if($mysqli){
            $mysqli->close();
        }
        
		return $id;
	}
     /**
     * @param string $provider_name
     * @return Result
     */
	public function GetUserSessionResultBySocialProvider($provider_name)
	{
        global $Hybrid_Auth_Config;
        
        $result = new Result();
        $result->success = false;
        
        if(!empty($provider_name)){
            try{
                // create an instance for Hybridauth with the configuration file path as parameter
                $hybridauth = new Hybrid_Auth( $Hybrid_Auth_Config );
                
                // try to authenticate with the selected provider
                
                $adapter = $hybridauth->authenticate( $provider_name );
        
                // then grab the user profile
                $user_profile = $adapter->getUserProfile();
                
                $id = $this->getUserIdByProvider($provider_name, $user_profile->identifier);
                
                //if user exists
                if($id){
                    $user = WashitaUser::GetUserById($id);
                    $result->value = $user->ConverToUserSession();
                    $result->success = true;
                }
                else{//if user did not exists
                    $result = $this->create_new_hybridauth_user( $user_profile->email, $user_profile->firstName, $user_profile->lastName, $provider_name, $user_profile->identifier );
                }
       
                // this will not disconnect the user from others providers if any used nor from your application
                // echo "Logging out..";
                //$hybridauth->logout();
            }
            catch( Exception $e ){
                // Display the recived error,
                // to know more please refer to Exceptions handling section on the userguide
                switch( $e->getCode() ){
                // case 0 : echo "Unspecified error."; break;
                // case 1 : echo "Hybriauth configuration error."; break;
                // case 2 : echo "Provider not properly configured."; break;
                // case 3 : echo "Unknown or disabled provider."; break;
                // case 4 : echo "Missing provider application credentials."; break;
                // case 5 : echo "Authentification failed. "
                //             . "The user has canceled the authentication or the provider refused the connection.";
                //         break;
                case 6 : 
                        // echo "User profile request failed. Most likely the user is not connected "
                        //     . "to the provider and he should authenticate again.";
                        $hybridauth->logout();
                        break;
                case 7 : 
                        // echo "User not connected to the provider.";
                        $hybridauth->logout();
                        break;
                // case 8 : echo "Provider does not support this feature."; break;
                }
            
                // // well, basically your should not display this to the end user, just give him a hint and move on..
                // echo "<br /><br /><b>Original error message:</b> " . 
                $result->message = $e->getMessage();
            }
        }
        
        return $result;
    }
    /**
     * @return Result
     */
    private function create_new_hybridauth_user( $email, $first_name, $last_name, $provider_name, $provider_user_id )
    {
        global $DBName;
        $result = new Result();

        // let generate a random password for the user
        $password_hashed = $this->generateRandomPassword();
        
        $mysqli = OpenMysqlConnection(); 
        $query =  
			"INSERT INTO `".$DBName."`.users
			( 
				EMAIL, 
                NOTIFICATION_EMAIL,
				PASSWORD,
                NAME,
                LASTNAME,
                AUTH_PROVIDER_NAME,
                AUTH_PROVIDER_UID
			) 
			VALUES
			( 
				'$email',
				'$email',
				'$password_hashed',
                '$first_name',
                '$last_name',
                '$provider_name', 
                '$provider_user_id'
			)"
		;
        if($mysqli->query($query))
        {
            $result->success = true;
            $user = WashitaUserSession::Create($mysqli->insert_id,$first_name,$last_name,false, UserType::Usual);
		    $result->value = $user;
        }
		else{
            $result = Result::Fail("Internal error 'Register by social provider DB'");
        }
        if($mysqli){
            $mysqli->close();
        }
        
        return $result;
    }
    
    public function SetPassword($email, $password, $temp_code){
        global $DBName;
        $result = new Result();

        // let generate a random password for the user
        $password_hashed = $this->better_crypt($password);
        
        $mysqli = OpenMysqlConnection(); 
        $query =  
			"UPDATE `".$DBName."`.users
			 SET PASSWORD='".$password_hashed."'
                 WHERE EMAIL='".$mysqli->real_escape_string($email)."' 
                 AND TEMP_CODE_PASSWORD_VALID_TILL >= NOW() 
                 AND TEMP_CODE_PASSWORD ='".$mysqli->real_escape_string($temp_code)."'
                 ";
				
        if($mysqli->query($query))
        {
            $result->success = true;
        }
		else{
            $result = Result::Fail("Internal error 'Internal error Set password DB'");
        }
        if($mysqli){
            $mysqli->close();
        }
        
        return $result;
    }
    
    public function SetTemporaryCodeToUpdatePasswordForUser($userId){
        global $DBName;
        $result = new Result();

        // let generate a random password for the user
        $temp_code = $this->generateRandomPassword();
        
        $mysqli = OpenMysqlConnection(); 
        $query =  
			"UPDATE `".$DBName."`.users
			 SET TEMP_CODE_PASSWORD='".$temp_code."',
                 TEMP_CODE_PASSWORD_VALID_TILL = (NOW() + INTERVAL 1 DAY) 
                 WHERE ID=".$userId."
                 ";
				
        if($mysqli->query($query))
        {
            $result->success = true;
		    $result->value = $temp_code;
        }
		else{
            $result = Result::Fail("Internal error 'Temp code DB'");
        }
        if($mysqli){
            $mysqli->close();
        }
        
        return $result;
    }
    
    private function generateRandomPassword(){
        return   md5( str_shuffle( "0123456789abcdefghijklmnoABCDEFGHIJ" ) );
    }

    public function __toString() {
        return "Auth";
    }
}