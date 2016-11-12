<?php
include_once(dirname(__FILE__)."/_helpers.php");
include_once(dirname(__FILE__)."/City.class.php");
include_once(dirname(__FILE__)."/AdminLogin.class.php");

class AdminLoginService {
  public static function ThrowIfNotLogined(){
        $adminLogin = AdminLoginService::CurrentLogin();
        if(!isset($adminLogin) || $adminLogin == null){
            echo "Please log-in";
            die;
        }
    }

    public static function CurrentLogin(){
        return unserialize($_SESSION["admin_login"]);
    }

    public static function Required($bool){
        if(!$bool){
            echo "Not enough rights for an action";
            die;
        }
    }
    
    public static function Login($password){
        global $AdminOrdersPasswordVina, $AdminOrdersCityIdVina;
        global $AdminOrdersPasswordSantiago, $AdminOrdersCityIdSantiago;
        global $AdminOrdersPasswordSantiagoAndInfluencers;
        global $AdminOrdersPassword;


        $adminLogin = null;
        switch($password){
            case $AdminOrdersPasswordVina:
                $city = City::GetCityById($AdminOrdersCityIdVina);
                //($currentCity,$canChangeCity,$canEditWashItems,$canViewOrders,$canViewInfluencers,$canEditNonWorkingDays,$canEditDiscounts)
                $adminLogin = new AdminLogin($city,false,false,true, false, false, false);
                break;
            case $AdminOrdersPasswordSantiago:
                $city = City::GetCityById($AdminOrdersCityIdSantiago);
                //($currentCity,$canChangeCity,$canEditWashItems,$canViewOrders,$canViewInfluencers,$canEditNonWorkingDays,$canEditDiscounts)
                $adminLogin = new AdminLogin($city,false,false,true, false, false, false);
                break;
            case $AdminOrdersPasswordSantiagoAndInfluencers:
                $city = City::GetCityById($AdminOrdersCityIdSantiago);
                //($currentCity,$canChangeCity,$canEditWashItems,$canViewOrders,$canViewInfluencers,$canEditNonWorkingDays,$canEditDiscounts)
                $adminLogin = new AdminLogin($city,false,false,true, true, false, true);
                break;
            case $AdminOrdersPassword:
                $city = City::GetCityById($AdminOrdersCityIdVina);
                //($currentCity,$canChangeCity,$canEditWashItems,$canViewOrders,$canViewInfluencers,$canEditNonWorkingDays,$canEditDiscounts)
                $adminLogin = new AdminLogin($city,true,true,true, true, true, true);
                break;
        }
       
        self::Store($adminLogin);

        if($adminLogin == null){
            echo 'Wrong password';
            usleep(2000);
            die();
        }
    }

    public static function Signout(){
        $_SESSION["admin_login"] = null;
    }

    public static function Store($login){
        $_SESSION["admin_login"] = serialize($login);
    }
}