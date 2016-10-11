<?php 
//ini_set('display_errors', 'On');

// required for csv export
 ob_start("ob_gzhandler");
 ob_start();

require_once(dirname(__FILE__)."/_config.php");

require_once(dirname(__FILE__)."/php/phpGrid_LazyMofo/lazy_mofo.php");

require_once(dirname(__FILE__)."/php/AdminLogin.class.php");


include_once(dirname(__FILE__)."/php/_helpers.php");

require_once(dirname(__FILE__).'/php/PickupTime.class.php');

require_once(dirname(__FILE__)."/php/AdminLoginService.class.php");

AdminLoginService::ThrowIfNotLogined();

$adminLogin = AdminLoginService::CurrentLogin();

AdminLoginService::Required($adminLogin->CanViewOrders());
$currentCity = $adminLogin->CurrentCity();


//Export CSV
if($_POST && $_GET["_export"]=="1"){

    $from = DateTimeImmutable::createFromFormat("Y-m-d H:i", GetPostNoLongerThan('from', 20)); 
    $to = DateTimeImmutable::createFromFormat("Y-m-d H:i", GetPostNoLongerThan('to', 20)); 
    $onlyPaid = GetBooleanPost("onlyPaid");
    $cityId = GetPost("cityId");
    
    DisplayGrid($from,$to,$onlyPaid,$cityId);
    exit;
}


include_once(dirname(__FILE__)."/templates/header.admin.php");

?>
 <h1 class="text-center">
    <?php echo $currentCity->Name ?>
 </h1>
 <h3 class="text-center">Historical orders</h3>

<?php 
if($adminLogin->CanEditWashItems() ||
    $adminLogin->CanViewInfluencers()){
  echo '<a href="'.CreateFullUrl('admin.php').'"class="btn btn-default" style="float:right;margin-top:-70px;margin-right:20px;" role="button">Admin panel</a>
    <br/>';
}
?>
 <a href="<?php echo CreateFullUrl('admin_orders.php')?>" 
            class="btn btn-default" style="float:right;margin-top:-40px;margin-right:20px;" role="button">Nearest</a>

<div class="container"> 
       
            <form id="form" method="post">
                <div class="row item">
                    <div class="col col-xs-6 col-md-3 input-group-vertical">
                        <p>From:</p>
                        <div class="input-group datepicker date" id="order_datepicker_from">
                            <input type='text' id="from" name="from" class="form-control" />
                            <span class="input-group-addon">
                                <span class="fa fa-calendar"></span>
                            </span>
                        </div>
                    </div>
                    <div class="col col-xs-6 col-md-3 input-group-vertical">
                        <p>Till:</p>
                        <div class="input-group datepicker date" id="order_datepicker_to">
                            <input type='text' id="to" name="to" class="form-control" />
                            <span class="input-group-addon">
                                <span class="fa fa-calendar"></span>
                            </span>
                        </div>
                    </div>
                    <div class="col col-xs-6 col-md-3 input-group-vertical">
                        <p>&nbsp;</p>
                        <div class="checkbox">
                        
                            <label><input type="checkbox" id="onlyPaid" name="onlyPaid" value="True" 
                            <?php
                            if($_POST && GetBooleanPost('onlyPaid')) {
                                echo ' checked';
                            }
                            ?>
                            />Only Paid</label>
                        </div>
                        
                    </div>
                
                    
                </div>
                <br/>

                <button type="submit" class="btn">Go</button>

                </form>
                

    <br/>
    
<?php
function DisplayGrid(DateTimeImmutable $from, DateTimeImmutable $to,$onlyPaid,$cityId){//"d/m/Y"
// echo $from."|".$to."}".$onlyPaid;
 $select =  
        "SELECT orders.ID,orders.ORDER_NUMBER, orders.NAME, 
                CONCAT(city_area.NAME, ', ', orders.ADDRESS) as ADDRESS,  
                orders.EMAIL,orders.PHONE, 
         WASH_TYPE,
         CONCAT(DATE_FORMAT(PICKUP_FROM, '%d-%b-%Y %l:%i%p'),'-', DATE_FORMAT(PICKUP_TILL, '%l:%i%p')) as PICKUP,
         CONCAT(DATE_FORMAT(DROPOFF_FROM, '%d-%b-%Y %l:%i%p'),'-', DATE_FORMAT(DROPOFF_TILL, '%l:%i%p')) as DROPOFF,
         PAYMENT_STATUS,
         order_feedback.RATING_OVERALL
         FROM orders
         join city_area on orders.CITY_AREA_ID = city_area.ID
         LEFT JOIN order_feedback on orders.ORDER_NUMBER = order_feedback.ORDER_NUMBER
         ";
       
    $where = ' WHERE city_area.CITY_ID = '.$cityId;
    if($onlyPaid){
        $where.= " AND PAYMENT_STATUS = 2";
    }
    if(!empty($from)){
        $where.= " AND PICKUP_FROM >='".$from->format("Y-m-d H:i")."'";
    }
    if(!empty($to)){
        $where.= " AND PICKUP_FROM <='".$to->format("Y-m-d H:i")."'";
    }

    $dbh = OpenPDOConnection();
    //global $lm;    
    // create LM object, pass in PDO connection
    $lm = new lazy_mofo($dbh);
    // table name for updates, inserts and deletes
    $lm->table = 'orders';
    // identity / primary key for table
    $lm->identity_name = 'ID';

    // Rename columns
    $lm->rename["ORDER_NUMBER"] = "Order No.";

    $lm->rename["RATING_OVERALL"] = "Rating__";
    
    
    
    $lm->grid_default_order_by = "ID desc";

    $lm->grid_sql = $select.$where;
    
    $lm->grid_output_control['ORDER_NUMBER'] = '--lazy_mofo_link_to_order_details';
    $lm->grid_output_control['RATING_OVERALL'] = '--lazy_mofo_rating';

    $lm->grid_output_control['WASH_TYPE'] = '--lazy_mofo_wash_type';

    $lm->grid_output_control['PAYMENT_STATUS'] = '--lazy_mofo_payment_status';

    $lm->grid_add_link = '';
    $lm->grid_export_link = '
        <form method="post" action="'.$_SERVER["PHP_SELF"].'?_export=1&amp;[qs]" target="_blank">
                <a href="javascript:;" target="_blank" onclick="parentNode.submit();" title="Download CSV">Export CSV</a>
                <input type="hidden" name="from" value="'.$from->format("Y-m-d H:i").'"/>
                <input type="hidden" name="to" value="'.$to->format("Y-m-d H:i").'"/>
                <input type="hidden" name="onlyPaid" value="'.$onlyPaid.'"/>
                <input type="hidden" name="allColumns" value="'.$allColumns.'"/>
                <input type="hidden" name="cityId" value="'.$cityId.'"/>
        </form>
    ';

    echo "<h2>".$from->format("d-M")." - ".$to->format("d-M")."</h2>";

    // use the lm controller
    $lm->run();
  
    echo "<br/>";
}



if(IsSamePagePost()) //Post Data received from order list page.
{
	$search =  GetPostNoLongerThan('search', 20); 
    
    $onlyPaid = GetBooleanPost('onlyPaid');
   
    $from =  GetPostNoLongerThan('from', 20); 
    $to =  GetPostNoLongerThan('to', 20); 
    
    $fromDateTime = DateTimeImmutable::createFromFormat("d/m/Y H:i",$from." 00:00");
    $toDateTime = DateTimeImmutable::createFromFormat("d/m/Y H:i",$to." 23:59");
    
    DisplayGrid($fromDateTime,$toDateTime,$onlyPaid,$currentCity->Id);
}


?>
</div>  

 <?php 
 $SCRIPTS_FOOTER.=
    '<script>
        $(document).ready(function() {
        	 var dp = $("#order_datepicker_from").datetimepicker(
                            {
                                format: "DD/MM/YYYY", 
                                locale: "es",
                                ';
                     if(IsSamePagePost()) {
                         	$from =  GetPostNoLongerThan('from', 20); 
                            $SCRIPTS_FOOTER.=  'defaultDate: moment("'.$from.'", "DD/MM/YYYY") ';
                     }
                    else{
                           $SCRIPTS_FOOTER.=  'defaultDate: moment().hour(0).minute(0).second(0)';
                                
                    }
 $SCRIPTS_FOOTER.=
'                                        
                            }
                    );
                    
                    $("#order_datepicker_to").datetimepicker(
                            {
                                format: "DD/MM/YYYY", 
                                locale: "es",';
                                
                    if(IsSamePagePost()) {
                         	$to =  GetPostNoLongerThan('to', 20); 
                            $SCRIPTS_FOOTER.=  'defaultDate: moment("'.$to.'", "DD/MM/YYYY")';
                     }
                    else{
                           $SCRIPTS_FOOTER.= 'defaultDate: moment().hour(0).minute(0).second(0).add(1, "days")';
                                
                    }
    $SCRIPTS_FOOTER.=
' 
                            }
                    );
                    
                    
            
            ';
          
        //   if(!$_POST){
        //         $scripts.= '$("#form").submit();';//submit at first run;              
        //   }
            
     $SCRIPTS_FOOTER.=
'            
        });
    </script>';
    
 include_once(dirname(__FILE__)."/templates/footer.admin.php");

 ?>