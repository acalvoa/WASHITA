<?php

abstract class WashType
{
    const WashingAndIroning = 0;
    const OnlyIroning = 1;
    const DryCleaning = 2;
    const SpecialCleaning = 3;
    
    public static function ToString($value){
          switch ($value) {
            case WashType::WashingAndIroning:
                return "Lavado y Doblado";
            case WashType::OnlyIroning:
                return "Solo Planchado";
            case WashType::DryCleaning:
                return "Lavaseco";
            case WashType::SpecialCleaning:
                return "Lavado por Prenda";
            default:
                return "Unknown";
        }
    }

    public static function ConvertFromPost($value){
          switch ($value) {
             case 'washing_and_ironing':
                return WashType::WashingAndIroning;
            case 'only_ironing':
                return WashType::OnlyIroning;
            case 'dry_cleaning':
                return WashType::DryCleaning;
            case 'special_cleaning':
                return WashType::SpecialCleaning;
            default:
                return -1;
        }
    }
}