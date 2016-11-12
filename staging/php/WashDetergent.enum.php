<?php

abstract class WashDetergent
{
    const None = 0;
    const Normal = 1;
    const EcoFriendly = 2;
    const Hypoallergenic = 3;
    const SoftForInfants = 4;
    
    public static function ToString($value){
          switch ($value) {
            case WashDetergent::None:
                return "None";
            case WashDetergent::Normal:
                return "Normal";
            case WashDetergent::EcoFriendly:
                return "Eco-Friendly";
            case WashDetergent::Hypoallergenic:
                return "Hipoalergénico";
            case WashDetergent::SoftForInfants:
                return "Suave Bebés y Niños";
            default:
                return "None";
        }
    }

    public static function ConvertFromPost($value){
          switch ($value) {
            case "normal":
                return WashDetergent::Normal;
            case "ecofriendly":
                return WashDetergent::EcoFriendly;
            case "hypoallergenic":
                return WashDetergent::Hypoallergenic;
            case "soft-for-infants":
                return WashDetergent::SoftForInfants;
            default:
                return WashDetergent::None;
        }
    }
}