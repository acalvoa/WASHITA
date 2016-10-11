<?php
include_once(dirname(__FILE__)."/_helpers.php");
include_once(dirname(__FILE__)."/City.class.php");

class AdminLogin {

    private $_currentCity = null;
    private $_canChangeCity = null;
    private $_canEditWashItems = false;
    private $_canViewOrders = false;
    private $_canViewInfluencers = false;

    public function __construct($currentCity,$canChangeCity,$canEditWashItems,$canViewOrders, $canViewInfluencers){
        $this->_currentCity = $currentCity;
        $this->_canChangeCity = $canChangeCity;
        $this->_canEditWashItems = $canEditWashItems;
        $this->_canViewOrders = $canViewOrders;
        $this->_canViewInfluencers = $canViewInfluencers;
    }

    public function SetCurrentCityById($cityId){
        if(!$this->_canChangeCity &&
            $this->_currentCity != null &&
            $this->_currentCity->Id != $cityId){
            echo "You cannot change city";
            die;
        }

        $this->_currentCity = City::GetCityById($cityId);
    }

    public function CurrentCity(){
        return $this->_currentCity;
    }

    public function CanEditWashItems(){
        return $this->_canEditWashItems;
    }

    public function CanViewOrders(){
        return $this->_canViewOrders;
    } 

    public function CanChangeCity(){
        return $this->_canChangeCity;
    } 

    public function CanViewInfluencers(){
        return $this->_canViewInfluencers;
    }  
}
