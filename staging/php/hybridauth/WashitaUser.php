<?php
    require_once(dirname(__FILE__)."/WashitaUserSession.php");
    require_once(dirname(__FILE__)."/UserType.enum.php");
    require_once(dirname(__FILE__)."/RegistrationCode.class.php");
    require_once(dirname(__FILE__)."/../DiscountCoupon.class.php");
    require_once(dirname(__FILE__)."/../Result.class.php");

class WashitaUser{

    var $Id;
    /**
     * @var string
     */
    var $Name;
    /**
     * @var string
     */
    var $Lastname;
    /**
     * @var string
     */
     var $Email;

     var $Phone;
     var $Address;
     var $CityAreaId;

     /**
     * @var string
     */
     var $NotificationEmail;
     
     /**
     * @var UserType
     */
     var $UserType;

     /**
     * @var RegistrationCode
     */
     var $RegistrationCode;

     var $PersonalDiscountAmount;

    /**
     * @var boolean
     */
    var $IsComplete;

    public static function CurrentSessionUser(){
        return isset($_SESSION["UserLogined"])? unserialize($_SESSION["UserLogined"]) : null;
    }

    public static function CurrentUser(){
        $userId = isset($_SESSION["UserId"])? $_SESSION["UserId"] : null;
        if(!empty($userId)){
            return self::GetUserById($userId);
        }

        return null;
    }


    public function FullName(){
        return $this->Name.' '.$this->Lastname;
    }

    /**
     * @return WashitaUserSession
     */
    public function ConverToUserSession(){
        return WashitaUserSession::Create($this->Id, $this->Name, $this->Lastname, $this->IsComplete, $this->UserType);
    }



    public function ApplyRegistrationCode($registrationCode){
        $result = new Result();
        $result->success = false;

        $regCodeObj = RegistrationCode::GetRegistrationCode($registrationCode);
        if($regCodeObj == null){
            $result->message = "Código de registro inválido";
        }
        else if($regCodeObj->IsUsed){
            $result->message = "Este código de registro ya está utilizado";
        }
        else{
            $result = self::setUserTypeAndRegistrationCode($this->Id, $regCodeObj->UserType, $regCodeObj->Code);
            if($result->success){
                $this->UserType = $regCodeObj->UserType;
                $this->PersonalDiscountAmount = $regCodeObj->InitialPersonalDiscount;

                if($regCodeObj->InitialPersonalDiscount > 0){
                    $result = self::AddToPersonalDiscount($this->Id, $regCodeObj->InitialPersonalDiscount);
                }

                if($result->success){
                    $result = DiscountCoupon::CreateDiscountForInfluencer($this->Id,$regCodeObj->Code);
                }
            }
            if($result->success){
                RegistrationCode::SetRegistrationCodeAsUsed($registrationCode);
                $regCodeObj->IsUsed = true;
                $this->RegistrationCode = $regCodeObj;
            }
        }

        return $result;
    }

    private static function setUserTypeAndRegistrationCode($userId, $userType, $registrationCode)
	{
        global $DBName;
        $result = new Result();
        $result->success = false;           

        $mysqli = OpenMysqlConnection(); 
        try {
            $query = "UPDATE `".$DBName."`.users 
            SET `USER_TYPE`=".$mysqli->real_escape_string($userType).",
                `REGISTRATION_CODE`='".$mysqli->real_escape_string($registrationCode)."'
            WHERE ID = '".$mysqli->real_escape_string($userId)."'";
            

            if($mysqli->query($query)){
                $result->success = true;           
            }
        }
        catch(Exception $e) {
        }
        $mysqli->close();
        
        if(!$result->success){
            $result->message = "Internal error Saving user type";
        }
		return $result;
	}


        /**
     * @param integer $id
     * @return WashitaUser
     */
    public static function GetUserById( $id )
	{
        global $DBName;

        $mysqli = OpenMysqlConnection(); 
        $user = null;

        try {
            $query = "SELECT users.ID as usersID, users.NAME as usersNAME, users.LASTNAME as usersLASTNAME, 
                             users.EMAIL as usersEMAIL, users.NOTIFICATION_EMAIL as usersNOTIFICATION_EMAIL,
                             users.USER_TYPE as usersUSER_TYPE, users.IS_COMPLETE as usersIS_COMPLETE,
                             users.PHONE as usersPHONE, users.ADDRESS as usersADDRESS, 
                             users.CITY_AREA_ID as usersCITY_AREA_ID, 
                             users.PERSONAL_DISCOUNT_AMOUNT as usersPERSONAL_DISCOUNT_AMOUNT,  
                             registration_code.ID as registration_codeID, 
                             registration_code.CODE as registration_codeCODE,
                             registration_code.USER_TYPE as registration_codeUSER_TYPE, 
                             registration_code.IS_USED as registration_codeIS_USED,
                             registration_code.INITIAL_PERSONAL_DISCOUNT as registration_codeINITIAL_PERSONAL_DISCOUNT
                      FROM `".$DBName."`.users 
                      left join `".$DBName."`.registration_code on users.REGISTRATION_CODE = registration_code.CODE             
                      WHERE users.ID = '".$mysqli->real_escape_string($id)."'";
            $sql_result = $mysqli->query($query);
            if($sql_result){
                $row = $sql_result->fetch_assoc();
                
                $user = new WashitaUser();
                $user->Id = $id;
                $user->Name = $row["usersNAME"];
                $user->Lastname = $row["usersLASTNAME"];
                $user->Email = $row["usersEMAIL"];
                $user->NotificationEmail = $row["usersNOTIFICATION_EMAIL"];
                $user->Address = $row["usersADDRESS"];
                $user->Phone = $row["usersPHONE"];
                $user->CityAreaId = $row["usersCITY_AREA_ID"];

                $user->PersonalDiscountAmount = $row["usersPERSONAL_DISCOUNT_AMOUNT"];

                

                $user->UserType = $row["usersUSER_TYPE"];
                $user->IsComplete = $row["usersIS_COMPLETE"];
                
                $user->RegistrationCode = RegistrationCode::CreateRegistrationCodeFromRow($row);

                $sql_result->free();
            }
        }
        catch(Exception $e) {
        }
        $mysqli->close();
        
		return $user;
	}

    /**
     * @param WashitaUser $user
     * @return Result
     */
    public static function SaveUser(WashitaUser $user )
	{
        global $DBName;
        $result = new Result();
        $result->success = false;           

        $mysqli = OpenMysqlConnection();

        try {
            $query = "UPDATE `".$DBName."`.users 
            SET `NAME`='".$mysqli->real_escape_string($user->Name)."'
            , `LASTNAME`='".$mysqli->real_escape_string($user->Lastname)."'
            , `NOTIFICATION_EMAIL`='".$mysqli->real_escape_string($user->NotificationEmail)."'
            , `IS_COMPLETE`='".$mysqli->real_escape_string($user->IsComplete)."'
            , `CITY_AREA_ID`='".$mysqli->real_escape_string($user->CityAreaId)."'
            , `ADDRESS`='".$mysqli->real_escape_string($user->Address)."'
            , `PHONE`='".$mysqli->real_escape_string($user->Phone)."'
            WHERE ID = '".$mysqli->real_escape_string($user->Id)."'";
            
            if($mysqli->query($query)){
                $result->success = true;           
            }
        }
        catch(Exception $e) {
        }
        $mysqli->close();
        
        if(!$result->success){
            $result->message = "Internal error Saving user settings";
        }else{
            self::SaveUserDataInSession($user->ConverToUserSession());
        }
        
		return $result;
	}

    public static function SaveUserDataInSession(WashitaUserSession $user){
        $_SESSION['UserId'] = $user->Id;
        $_SESSION['UserShortName'] = $user->ShortName != "" ? $user->ShortName : "Me";
        $_SESSION['LastLogin'] = time();
        $_SESSION["UserLogined"] = serialize($user);
    }


    /**
     * @return Result
     */
    public static function AddToPersonalDiscount($userId,$amount)
	{
        global $DBName;
        $result = new Result();
        $result->success = false;           

        $mysqli = OpenMysqlConnection();

        try {
            $query = "UPDATE `".$DBName."`.users 
            SET PERSONAL_DISCOUNT_AMOUNT=PERSONAL_DISCOUNT_AMOUNT + ".$mysqli->real_escape_string($amount)."
            WHERE ID = '".$mysqli->real_escape_string($userId)."'";
            
            if($mysqli->query($query)){
                $result->success = true;           
            }
        }
        catch(Exception $e) {
        }
        $mysqli->close();
        
        if(!$result->success){
            $result->message = "Internal error Adding personal discount";
        }
        
		return $result;
	}

    /**
     * @return Result
     */
    public static function SubtractFromPersonalDiscount($amount)
	{
        global $DBName;
        $result = new Result();
        $result->success = false;           


        $sessionUser = self::CurrentSessionUser();
        if($sessionUser != null){
            $mysqli = OpenMysqlConnection();
            try {
                $query = "UPDATE `".$DBName."`.users 
                SET PERSONAL_DISCOUNT_AMOUNT=PERSONAL_DISCOUNT_AMOUNT - ".$mysqli->real_escape_string($amount)."
                WHERE ID = '".$mysqli->real_escape_string($sessionUser->Id)."'";
                
                if($mysqli->query($query)){
                    $result->success = true;           
                }
            }
            catch(Exception $e) {
            }
            $mysqli->close();
            
            if(!$result->success){
                $result->message = "Hubo un problema para aplicar su crédito personal";
            }
        }
        
		return $result;
	}


    

}