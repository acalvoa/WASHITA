<?php

require_once(dirname(__FILE__)."/_config.php");
require_once(dirname(__FILE__)."/php/_helpers.php");
require_once(dirname(__FILE__)."/php/AdminLogin.class.php");


require_once(dirname(__FILE__)."/php/phpGrid_LazyMofo/lazy_mofo.php");
include_once(dirname(__FILE__)."/templates/header.admin.php");
require_once(dirname(__FILE__)."/php/AdminLoginService.class.php");

AdminLoginService::ThrowIfNotLogined();

$adminLogin = AdminLoginService::CurrentLogin();
AdminLoginService::Required($adminLogin->CanEditDiscounts());

?>
<div class="container">


<?
     echo '<a href="'.CreateFullUrl('admin.php').'"class="btn btn-default" style="float:right;margin-top:20px;margin-right:20px;" role="button">Admin panel</a>
    <br/>';
   
?>
    
    <h3>Discounts</h3>
     <br/>
     <br/>
     <br/>
    <div class="row item">
        <?php
            $dbh = OpenPDOConnection();
            // create LM object, pass in PDO connection
            $lm = new lazy_mofo($dbh);
            $sql = "SELECT discount.ID, discount.COUPON, discount.VALUE, 
                    discount.IS_PERCENT,
                    discount.VALID_TILL,  
                    discount.USED, 
                    discount.MAX_USAGE, 
                    discount.IS_ONE_TIME_PER_EMAIL,
                    CASE 
                        WHEN discount.INFLUENCER_USER_ID IS NULL THEN '-' 
                        ELSE CONCAT('(id:', discount.INFLUENCER_USER_ID, ') ', users.email) 
                    END as INFLUENCER,
                    discount.ID 
                    from discount
                    left join users on discount.INFLUENCER_USER_ID = users.ID
                    ";
            // table name for updates, inserts and deletes
            $lm->table = 'discount';
            // identity / primary key for table
            $lm->identity_name = 'ID';
            // optional, make friendly names for fields
            $lm->grid_default_order_by = "ID desc";

            //$lm->grid_add_link = '';
            $lm->grid_export_link = '';

            $lm->form_input_control['VALID_TILL'] = '--lazy_mofo_date';
            $lm->form_input_control['IS_ONE_TIME_PER_EMAIL'] = '--checkbox';
            $lm->grid_input_control['IS_ONE_TIME_PER_EMAIL'] = '--checkbox';
            $lm->cast_user_function['IS_ONE_TIME_PER_EMAIL'] = 'to_boolean';
            $lm->form_input_control['IS_PERCENT'] = '--checkbox';
            $lm->grid_input_control['IS_PERCENT'] = '--checkbox';
            $lm->cast_user_function['IS_PERCENT'] = 'to_boolean';
            

            $lm->return_to_edit_after_insert=false;
            $lm->return_to_edit_after_update=false;

            $lm->grid_sql = $sql;
            
            // or set non-US date format
            $lm->date_out = 'd/m/Y';
            $lm->datetime_out = 'd/m/Y';


            function to_boolean($val){
                return intval($val);
            }
            function correct_date_format(){
                if(isset($_POST['VALID_TILL'])){
                    $date = DateTimeImmutable::createFromFormat("d/m/Y", $_POST['VALID_TILL']);
                    $_POST['VALID_TILL'] = $date->format("Y-m-d");
                }
            }
            $lm->on_insert_user_function = 'correct_date_format';
            $lm->on_update_user_function = 'correct_date_format';

            $lm->grid_delete_link = "";
            // use the lm controller
            $lm->run();
            
        ?>
        
    </div>
</div>
<br>
<br>


<?php
 $SCRIPTS_FOOTER.=
    '<script>
        $(document).ready(function() {
            appOrder.sanitizeNumberInput();
        });
    </script>
    
    <script>
        $(function() {
            var defaultData = $(".datepicker input").attr("data-default-value");
            if(!defaultData){
                defaultData = moment();
            }
            $(".datepicker").datetimepicker(
                {
                    format: "DD/MM/YYYY", 
                    locale: "es",
                    defaultDate: moment(defaultData).hour(0).minute(0).second(0)
                }).on("dp.change", function(e) {
                    var choosenDate = e.date.toDate();
                    $(".datepicker input").val(moment(choosenDate).format("DD/MM/YYYY"));
            });
        });
    </script>
    ';
include_once(dirname(__FILE__)."/templates/footer.admin.php");

?>