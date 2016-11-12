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
<?php
if($adminLogin->CanEditWashItems()){
     echo '<a href="'.CreateFullUrl('admin.php').'"class="btn btn-default" style="float:right;margin-top:20px;margin-right:20px;" role="button">Admin panel</a>
    <br/>';
}    
?>
    <h3>Blocked days</h3>
    <span>Repeat every year</span>
     <br/>
     <br/>
     <br/>

    <div class="row item">
        <?php

            $dbh = OpenPDOConnection();
            // create LM object, pass in PDO connection
            $lm = new lazy_mofo($dbh);
            $sql = "SELECT `ID`, `DAY`, `MONTH`, `DESCRIPTION`, `ID` FROM `blockedday`";
            // table name for updates, inserts and deletes
            $lm->table = 'blockedday';
            // identity / primary key for table
            $lm->identity_name = 'ID';
            // optional, make friendly names for fields
            $lm->grid_default_order_by = "ID desc";

            //$lm->grid_add_link = '';
            $lm->grid_export_link = '';

            $lm->return_to_edit_after_insert=false;
            $lm->return_to_edit_after_update=false;

            $lm->grid_sql = $sql;
            

            // use the lm controller
            $lm->run();
            
        ?>
        
    </div>
</div>
<br>


<?php
 $SCRIPTS_FOOTER.=
    '<script>
        $(document).ready(function() {
            appOrder.sanitizeNumberInput();
        });
    </script>';
include_once(dirname(__FILE__)."/templates/footer.admin.php");

?>