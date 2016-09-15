<?php

abstract class UserType
{
    const Usual = 0;
    const Influencer = 1;
    
    public static function ToString($value){
          switch ($value) {
            case UserType::Usual:
                return "Usual";
            case UserType::Influencer:
                return "Influencer";
            default:
                return "Unknown";
        }
    }
   
}