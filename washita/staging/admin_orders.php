<?php
// ini_set('display_errors', 'On');

ob_start("ob_gzhandler");
// required for csv export
ob_start();


require_once(dirname(__FILE__)."/_config.php");
require_once(dirname(__FILE__)."/php/AdminLogin.class.php");

require_once(dirname(__FILE__)."/php/phpGrid_LazyMofo/lazy_mofo.php");

include_once(dirname(__FILE__)."/php/_helpers.php");

require_once(dirname(__FILE__).'/php/PickupTime.class.php');
require_once(dirname(__FILE__).'/php/CurrentDateTime.class.php');
require_once(dirname(__FILE__)."/php/AdminLoginService.class.php");


   if(!$_POST && AdminLoginService::CurrentLogin() == null){
        echo 
        '<html>
         <head>
            <title>WASHITA</title>
         </head>
         <body>
            <br/>
            <form method="post">
            Password: <input type="password" name="password"></input>
            <input type="submit"></input>
            </form>
         </body>
         </html>';
         die();
    }

if(isset($_POST["password"])){
    AdminLoginService::Login($_POST["password"]);
}
AdminLoginService::ThrowIfNotLogined();

$adminLogin = AdminLoginService::CurrentLogin();

AdminLoginService::Required($adminLogin->CanViewOrders());
$currentCity = $adminLogin->CurrentCity();
  
//Export CSV
if($_POST && $_GET["_export"]=="1"){
    $from = DateTimeImmutable::createFromFormat("Y-m-d H:i", GetPostNoLongerThan('from', 20)); 
    $to = DateTimeImmutable::createFromFormat("Y-m-d H:i", GetPostNoLongerThan('to', 20)); 
    DisplayGrid($from,$to, "Export",$currentCity->Id);
    return;
}

include_once(dirname(__FILE__)."/templates/header.admin.php");

?>
    <h1 class="text-center">
        <?php echo $currentCity->Name ?>
    </h1>
    <h3 class="text-center">
        The nearest orders on
        <?php echo CurrentDateTime::Now()->format("d-M-Y H:i"); ?>
    </h3>

<?php 
if($adminLogin->CanEditWashItems() ||
   $adminLogin->CanViewInfluencers()){
     echo '<a href="'.CreateFullUrl('admin.php').'"class="btn btn-default" style="float:right;margin-top:-70px;margin-right:20px;" role="button">Admin panel</a>
    <br/>';
}
?>

    <a href="<?php echo CreateFullUrl('admin_historical_orders.php')?>"   
            class="btn btn-default" style="float:right;margin-top:-40px;margin-right:20px;" role="button">Historical</a>
        <br/>
        
     <div class="container"> 
<?php



$futurePickup = PickupTime::GetMinPickupTime();
$previousPickup = $futurePickup->Previous();
DisplayGrid($futurePickup->from,$futurePickup->to, "Next",$currentCity->Id);
DisplayGrid($previousPickup->from,$previousPickup->to, "Previous", $currentCity->Id);


function DisplayGrid(DateTimeImmutable $from, DateTimeImmutable $to,$caption,$cityId){//"d/m/Y"
    $select = "SELECT orders.ID,ORDER_NUMBER, orders.NAME, 
               city_area.NAME + ', ' + orders.ADDRESS as ADDRESS, 
               EMAIL,
               PHONE, 
               WASH_TYPE,
               CONCAT(DATE_FORMAT(PICKUP_FROM, '%d-%b-%Y %l:%i%p'),'-', DATE_FORMAT(PICKUP_TILL, '%l:%i%p')) as PICKUP,
               CONCAT(DATE_FORMAT(DROPOFF_FROM, '%d-%b-%Y %l:%i%p'),'-', DATE_FORMAT(DROPOFF_TILL, '%l:%i%p')) as DROPOFF,
               PAYMENT_STATUS
               FROM orders 
               join city_area on orders.CITY_AREA_ID = city_area.ID";
    $where = ' WHERE city_area.CITY_ID = '.$cityId;
    if(!empty($from)){
        $where.= " AND PICKUP_FROM >='".$from->format("Y-m-d H:i")."'";
    }
    if(!empty($to)){
        $where.= " AND PICKUP_FROM <='".$to->format("Y-m-d H:i")."'";
    }

    $dbh = OpenPDOConnection();
    // create LM object, pass in PDO connection
    $lm = new lazy_mofo($dbh);
    // table name for updates, inserts and deletes
    $lm->table = 'orders';
    // identity / primary key for table
    $lm->identity_name = 'ID';
    // optional, make friendly names for fields
    $lm->grid_output_control['ORDER_NUMBER'] = '--lazy_mofo_link_to_order_details';
    
    // Rename columns
    $lm->rename["ORDER_NUMBER"] = "Order No.";

    $lm->grid_output_control['WASH_TYPE'] = '--lazy_mofo_wash_type';
    $lm->grid_output_control['PAYMENT_STATUS'] = '--lazy_mofo_payment_status';
    

    $lm->grid_default_order_by = "ID desc";
    $lm->grid_sql = $select.$where;
    $lm->grid_add_link = '';
    $lm->grid_export_link = '
        <form method="post" action="'.$_SERVER["PHP_SELF"].'?_export=1&amp;[qs]" target="_blank">
               <a href="javascript:;" target="_blank" onclick="parentNode.submit();" title="Download CSV">Export CSV</a>
               <input type="hidden" name="from" value="'.$from->format("Y-m-d H:i").'"/>
               <input type="hidden" name="to" value="'.$to->format("Y-m-d H:i").'"/>
        </form>
    ';
    

    echo "<h2>".$caption." (".$from->format("d-M H:i")."-".$to->format("H:i").")</h2>";
    // use the lm controller
    $lm->run();

    echo "
    <br/>";
}
?>
    </div>

<?php
include_once(dirname(__FILE__)."/templates/footer.admin.php");

?>

