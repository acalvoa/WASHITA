<?php

require_once(dirname(__FILE__)."/_config.php");
require_once(dirname(__FILE__)."/php/_helpers.php");
require_once(dirname(__FILE__)."/php/AdminLogin.class.php");


//require_once(dirname(__FILE__)."/php/phpGrid_LazyMofo/lazy_mofo.php");
include_once(dirname(__FILE__)."/templates/header.admin.php");
require_once(dirname(__FILE__)."/php/AdminLoginService.class.php");
require_once(dirname(__FILE__)."/php/hybridauth/UserType.enum.php");

AdminLoginService::ThrowIfNotLogined();

$adminLogin = AdminLoginService::CurrentLogin();
AdminLoginService::Required($adminLogin->CanViewInfluencers());

?>
<div class="container">
    
    <h3>Influencers</h3>
     <br/>
     <?php 
if($adminLogin->CanViewInfluencers()){
     echo '<a href="'.CreateFullUrl('admin.php').'"class="btn btn-default" style="float:right;margin-top:-70px;margin-right:20px;" role="button">Admin panel</a>
    <br/>';
}
?>
    <div class="row item">
        <?php
            class UserInfluencerShort{
                var $ID, $NAME, $LASTNAME, $NOTIFICATION_EMAIL, $REGISTRATION_CODE, $PERSONAL_DISCOUNT_AMOUNT;
            }

            try{
               $infuencers = array();
                $mysqli = OpenMysqlConnection(); 
                $query = "SELECT ID, NAME, LASTNAME, NOTIFICATION_EMAIL, REGISTRATION_CODE, PERSONAL_DISCOUNT_AMOUNT 
                FROM `".$DBName."`.`users` 
                WHERE USER_TYPE = '".$mysqli->real_escape_string(UserType::Influencer)."'";
                
                $sql_result = $mysqli->query($query);
                if($sql_result){
                    while($row = $sql_result->fetch_assoc()){
                        $inf = new UserInfluencerShort();
                        $inf->ID = $row["ID"];
                        $inf->NAME = $row["NAME"];
                        $inf->LASTNAME = $row["LASTNAME"];
                        $inf->NOTIFICATION_EMAIL = $row["NOTIFICATION_EMAIL"];
                        $inf->REGISTRATION_CODE = $row["REGISTRATION_CODE"];
                        $inf->PERSONAL_DISCOUNT_AMOUNT = $row["PERSONAL_DISCOUNT_AMOUNT"];

                        $infuencers[] = $inf;
                    }
                    
                    $sql_result->free();
                }


                foreach ($infuencers  as $inf) {

                    echo '<p><b>'.$inf->NAME.' '.$inf->LASTNAME.' ('.$inf->NOTIFICATION_EMAIL.') / '.MoneyFormat($inf->PERSONAL_DISCOUNT_AMOUNT).' 
                            / '.$inf->REGISTRATION_CODE.'</b></p>';

                    $query = "SELECT ORDER_NUMBER, NAME, EMAIL, PRICE_WITH_DISCOUNT 
                    FROM `".$DBName."`.`orders` 
                    WHERE DISCOUNT_COUPON = '".$mysqli->real_escape_string($inf->REGISTRATION_CODE )."'
                          AND PAYMENT_STATUS = 2";
                    
                    echo '<div style="margin-left:30px;font-size:small">';
                    $sql_result = $mysqli->query($query);
                    $totalApplied = 0;
                    if($sql_result){
                        while($row = $sql_result->fetch_assoc()){
                            echo '<span>'.$row["NAME"].' ('.$row["EMAIL"].') / '.$row["ORDER_NUMBER"].' / 
                                    '.MoneyFormat($row["PRICE_WITH_DISCOUNT"]).' &diams; </span>';

                            $totalApplied++;
                        }
                        
                        $sql_result->free();
                    }
                    echo '
                    <p style="font-size:initial;color: cornflowerblue;">Total applied: '.$totalApplied.'</p>
                    </div>';
                }

                
                $mysqli->close();
            }
            catch(Exception $e) {
                
            }
            
            
        ?>
        
    </div>
</div>
<br>
<br>


<?php

include_once(dirname(__FILE__)."/templates/footer.admin.php");

?>