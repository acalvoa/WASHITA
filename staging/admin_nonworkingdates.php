<?php

require_once(dirname(__FILE__)."/_config.php");
require_once(dirname(__FILE__)."/php/_helpers.php");
require_once(dirname(__FILE__)."/php/AdminLogin.class.php");


require_once(dirname(__FILE__)."/php/phpGrid_LazyMofo/lazy_mofo.php");
include_once(dirname(__FILE__)."/templates/header.admin.php");
require_once(dirname(__FILE__)."/php/AdminLoginService.class.php");

AdminLoginService::ThrowIfNotLogined();

$adminLogin = AdminLoginService::CurrentLogin();
AdminLoginService::Required($adminLogin->CanEditNonWorkingDays());

?>
<div class="container">


<?
if($adminLogin->CanEditWashItems()){
     echo '<a href="'.CreateFullUrl('admin.php').'"class="btn btn-default" style="float:right;margin-top:20px;margin-right:20px;" role="button">Admin panel</a>
    <br/>';
}    
   
?>
    
    <h3>Blocked dates</h3>
     <span>Concrete date</span>
     <br/>
     <br/>
     <br/>
    <div class="row item">
        <?php

            $dbh = OpenPDOConnection();
            // create LM object, pass in PDO connection
            $lm = new lazy_mofo($dbh);
            $sql = "SELECT `ID`, `DATE`, `DESCRIPTION`, `ID` FROM `blockeddate`";
            // table name for updates, inserts and deletes
            $lm->table = 'blockeddate';
            // identity / primary key for table
            $lm->identity_name = 'ID';
            // optional, make friendly names for fields
            $lm->grid_default_order_by = "ID desc";

            //$lm->grid_add_link = '';
            $lm->grid_export_link = '';

            $lm->form_input_control['DATE'] = '--lazy_mofo_date';

            $lm->return_to_edit_after_insert=false;
            $lm->return_to_edit_after_update=false;

            $lm->grid_sql = $sql;
            
            // or set non-US date format
            $lm->date_out = 'd/m/Y';
            $lm->datetime_out = 'd/m/Y';

            function correct_date_format_and_description(){
                if(isset($_POST['DATE'])){
                    $date = DateTimeImmutable::createFromFormat("d/m/Y", $_POST['DATE']);
                    $_POST['DATE'] = $date->format("Y-m-d");
                }

                if(isset($_POST['DESCRIPTION']) && empty($_POST['DESCRIPTION'])){
                    $_POST['DESCRIPTION'] = "-";
                }
            }
            $lm->on_insert_user_function = 'correct_date_format_and_description';
            $lm->on_update_user_function = 'correct_date_format_and_description';


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