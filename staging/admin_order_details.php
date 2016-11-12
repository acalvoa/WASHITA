<?php

require_once(dirname(__FILE__)."/_config.php");
require_once(dirname(__FILE__)."/php/phpGrid_LazyMofo/lazy_mofo.php");

include_once(dirname(__FILE__)."/php/_helpers.php");
require_once(dirname(__FILE__).'/php/PickupTime.class.php');
require_once(dirname(__FILE__).'/php/Order.class.php');
require_once(dirname(__FILE__).'/php/OrderFeedback.class.php');
include_once(dirname(__FILE__)."/php/MailService.class.php");
include_once(dirname(__FILE__)."/php/Price.class.php");
require_once(dirname(__FILE__).'/php/controls/StarRating.control.php');
require_once(dirname(__FILE__).'/php/OrderWashItemLine.class.php');
require_once(dirname(__FILE__).'/php/OrderCustomItemLine.class.php');

require_once(dirname(__FILE__).'/php/WashType.enum.php');
require_once(dirname(__FILE__).'/php/WashDetergent.enum.php');
require_once(dirname(__FILE__)."/php/AdminLogin.class.php");

require_once(dirname(__FILE__)."/php/AdminLoginService.class.php");


// Check password
AdminLoginService::ThrowIfNotLogined();
$adminLogin = AdminLoginService::CurrentLogin();
AdminLoginService::Required($adminLogin->CanViewOrders());
$currentCity = $adminLogin->CurrentCity();


$orderNumber = $_GET["orderNumber"];
$order = Order::GetOrderByNumber($orderNumber);

$resendComfirmation = false;

$emailResultAboutPayment = false;


function PutActualData($order){
    global $DBName;
            $actualPrice = NumberFromChileanString(GetPost('actual_price'));
            $actualWeight = GetPost('actual_weight');

            $mysqli = OpenMysqlConnection(); 
            
            // Write to the database requested data
            $query = "UPDATE `".$DBName."`.`orders`
                    SET ACTUAL_WEIGHT='".$mysqli->real_escape_string($actualWeight)."',
                        ACTUAL_PRICE_WITH_DISCOUNT='".$mysqli->real_escape_string($actualPrice)."'
                    WHERE ORDER_NUMBER='".$mysqli->real_escape_string($order->OrderNumber)."'";

            if(!$mysqli->query($query)){
                echo "ERROR, no fue posible actualizar el peso actual a ésta orden!!!";
                exit(); 
            }                          
             

            if($order->WashType == WashType::WashingAndIroning){
                $orderWashitemLinesPost = isset($_POST['washitems'])? $_POST['washitems'] : "";
                $orderWashitemLines = OrderWashItemLine::ConvertFromPost($order->WashType, $orderWashitemLinesPost);
                OrderWashItemLine::SetActualItemsForOrder($order->OrderNumber, $orderWashitemLines);

                $ironing_items_post = isset($_POST['ironing_items_post']) ? $_POST['ironing_items_post']: "";
                $ironingItemLines =  OrderCustomItemLine::ConvertFromPost(WashType::OnlyIroning, $ironing_items_post);
                OrderCustomItemLine::SetCustomOrderItems($order->OrderNumber, $ironingItemLines, true);
            }
            else if($order->WashType == WashType::OnlyIroning){
                //
            }
            else if($order->WashType == WashType::DryCleaning){
                $orderDryCleaningItemLinesPost = isset($_POST['dry_cleaning_items_post']) ? $_POST['dry_cleaning_items_post']: "";
                $orderDryCleaningItemLines  = OrderWashItemLine::ConvertFromPost($order->WashType, $orderDryCleaningItemLinesPost);
                OrderWashItemLine::SetActualItemsForOrder($order->OrderNumber, $orderDryCleaningItemLines);
            }

            
            $mailService = new MailService();
            $order = Order::GetOrderByNumber($order->OrderNumber);

            if($order->ActualPriceWithDiscount > 0){
                $emailResultAboutPayment = $mailService->PaymentRequest($order->OrderNumber);
            }
            else{//zero price
                Order::SetOrderPaymentStatus($order->OrderNumber, OrderPaymentStatus::Paid);
                $order->UseCoupon();
                $order = Order::GetOrderByNumber($order->OrderNumber);

                $emailResultAboutPayment = $mailService->ZeroPaymentConfirmation($order->OrderNumber);
            }

        return $order;
}

if(IsSamePagePost()){   

    global $DBName;
    $actionType = GetPost('action_type');
    if($actionType === "resend_confirmation"){
        // SEND EMAIL
        $mailService = new MailService();
        $mailService->SendNotification($orderNumber);
     }
     else if($actionType === "put_actual_data"){
        $order = PutActualData($order);
     }
}

?>


 
<?php 
$LINKS.= '<style>
                .ironing-add-more-item{margin-top:10px;}   
            </style>';
    include_once(dirname(__FILE__)."/templates/header.admin.php"); 
?>
<div class="container">
    <?php
        if($resendComfirmation){
            echo '
            <div class="alert alert-success">
            <strong>Success!</strong> Email with confirmation is sent to the customer.
            </div>';
        }

        if($emailResultAboutPayment){
            echo '
            <div class="alert alert-success">
            <strong>Success!</strong> Email about the payment request is sent to the customer.
            </div>';
        } 
    ?>
    
    <h3 >Orden N° 
    <?php echo $orderNumber ?>
    </h3>
     <a class="btn btn-default" onclick="window.history.back()" href="#" style="float:right;margin-top:-40px;margin-right:20px;" role="button">Back</a>
        
     <br/>
    <div class="row item">
        <div class="col col-sm-6">

            <fieldset class="form-group">
                <label>Dirección</label>
                <p><?php echo $order != null? $order->GetFullAddress() : "" ?></p>
            </fieldset>
            <fieldset class="form-group">
                <label>Nombre</label>
                <p><?php echo $order != null? $order->Name : "" ?></p>
            </fieldset>
            <fieldset class="form-group">
                <label>Email</label>
                <p><?php echo $order != null? $order->Email : "" ?></p>
            </fieldset>
            <fieldset class="form-group">
                <label>Teléfono</label>
                <p><?php echo ($order != null && !empty($order->Phone))? $order->Phone : "-" ?></p>
            </fieldset>
            <fieldset class="form-group">
                <label>Tipo de Servicio</label>
                <p><?php echo $order != null? $order->WashingTypeText() : "-" ?></p>
            </fieldset>

            <?php 
            $initWashItemLines = [];
            $initIroningItemLines = [];
            $initDryCleaningItemLines = [];

                if($order->WashType == WashType::WashingAndIroning){
                    $initWashItemLines = OrderWashItemLine::GetInitialItemsForOrder($orderNumber);
                    $initIroningItemLines = OrderCustomItemLine::GetCurrentItemsForOrder(WashType::OnlyIroning, $orderNumber, false);
                    
                    echo'
                    <fieldset class="form-group">
                        <label>Wash items</label>
                        <p>'.OrderWashItemLine::LinesToString($initWashItemLines).'</p>
                    </fieldset>
                    <fieldset class="form-group">
                        <label>Ironing items</label>
                            <p>'.OrderCustomItemLine::LinesToString($initIroningItemLines).'</p>
                    </fieldset> 
                    ';

                    // echo'
                    // <fieldset class="form-group">
                    //     <label>Detergent</label>
                    //     <p>'.WashDetergent::ToString($order->WashDetergent).'</p>
                    // </fieldset>
                    // ';
                    
                }
                else if($order->WashType == WashType::OnlyIroning){
                    //
                }
                else if($order->WashType == WashType::DryCleaning){
                    $initDryCleaningItemLines = OrderWashItemLine::GetInitialItemsForOrder($orderNumber);
                     echo'
                    <fieldset class="form-group">
                        <label>Dry cleaning items</label>
                        <p>'.OrderWashItemLine::LinesToString($initDryCleaningItemLines).'</p>
                    </fieldset>
                    ';
                }
            ?>
            <?php
                    $actualWashItemLines = OrderWashItemLine::GetActualItemsForOrder($orderNumber);
                    if(!empty($actualWashItemLines)){
                        echo '
                         <fieldset class="form-group">
                                <label class="changed-value">Prendas Validadas a Lavar por staff</label>
                                <p class="changed-value">'.OrderWashItemLine::LinesToString($actualWashItemLines).'</p>
                                <small class="text-muted">Lista de prendas validadas. Se solicitará pago adicional para lavaseco si es que el nuevo valor es mayor que el inicial.</small>
                        </fieldset>
                        ';
                    }

                    $actualIroningItemLines = OrderCustomItemLine::GetCurrentItemsForOrder(WashType::OnlyIroning, $orderNumber, true);
                    if(!empty($actualIroningItemLines)){
                        echo '
                         <fieldset class="form-group">
                                <label class="changed-value">Prendas Validadas a Planchar</label>
                                <p class="changed-value">'.OrderCustomItemLine::LinesToString($actualIroningItemLines).'</p>
                                <small class="text-muted">Lista de prendas validadas a planchar.  Se solicitará pago adicional para lavaseco si es que el nuevo valor es mayor que el inicial.</small>
                        </fieldset>
                        ';
                    }
            ?>
            
            
        </div>    
        <div class="col col-sm-6">
            <?php 
                if($order != null && $order->IsWeightRequired()){
                    echo '
                    <fieldset class="form-group">
                        <label>Peso, Kg</label>
                        <p>'.(!empty($order->Weight)? NumberFormatWithTens($order->Weight) : "-").'</p>
                        <small class="text-muted">Precio según ha ingresado el cliente</small>
                    </fieldset>
                    ';
                }
            ?>

            <fieldset class="form-group">
                <label>Cupón de Descuento</label>
                <p><?php echo ($order != null && !empty($order->DiscountCoupon))? $order->DiscountCoupon : "-" ?></p>
            </fieldset>
            <fieldset class="form-group">
                <label>Precio</label>
                <p><?php echo MoneyFormat($order->PriceWithDiscount) ?></p>
                  <?php
                     if($order != null && !empty($order->IsWeightRequired)){
                        echo '<small class="text-muted">Precio por '.NumberFormatWithTens($order->Weight).' Kg con descuento si existe</small>';
                     }
                     else{
                        echo '<small class="text-muted">Precio con descuento si existe</small>';
                     }
                  ?>
            </fieldset>

        <?php
        if($order != null && $order->IsWeightRequired() && !empty($order->ActualWeight)){
            echo  
        '<fieldset class="form-group changed-value">
            <label>Peso Validado (Kg)</label>
            <p class="changed-value"> '.NumberFormatWithTens($order->ActualWeight).'</p>
            <small class="text-muted">Peso validado por staff de Washita</small>
         </fieldset>';
        }
        if($order != null && $order->ActualPriceWithDiscount != null){
            echo  
        '<fieldset class="form-group changed-value">
            <label>Precio validado (con descuento si aplica)</label>
            <p>'.MoneyFormat($order->ActualPriceWithDiscount).'</p>
            <small class="text-muted">Precio Final</small>
         </fieldset>';
        }
       ?>
        <fieldset class="form-group">
            <label>Estado del Pago</label>
            <p>  <?php echo OrderPaymentStatus::ToString($order->PaymentStatus) ?></p>
         </fieldset> 
        </div>    
    </div>
    <div class="row item">
       


    <form method="post" id="actual_form">
            <h3>Precio Validado por Staff*</h3>
                <fieldset class="form-group">
                    <label>Ingresar precio final</label>
                    <input id="actual_price" name="actual_price" class="form-control numbersOnly" type="number" min="0" step="1" 
                            placeholder="Price" value="<?php echo ($order != null? $order->ActualPriceWithDiscount:0)?>"
                            required>
                    <small class="text-muted">Ingresar el precio validado para solicitar pago al cliente</small>
                </fieldset>


        <?php 
           if($order != null && $order->IsWeightRequired()){
            echo '      
                <h3>Peso Validado*</h3>
                <fieldset class="form-group">
                    <label>Ingrese Peso actual <span id="actual_weight_new"></span></label>
                    <input id="actual_weight" name="actual_weight" class="form-control decimals-with-tens" type="number" min="0" max="1000" step="0.1" 
                        value="'.($order != null? $order->ActualWeight:0).'"
                        placeholder="Ingrese peso actual, mayor que '.NumberFormatWithTens($order->Weight).'"
                        required>
                    <small class="text-muted">Ingrese el peso validado por el staff.</small>
                </fieldset>
            ';
        }
    ?>
        <br>

        <?php 
        if($order != null)
        {
            echo '<h3>Corregir Cantidad de Prendas</h3>';

                if($order->WashType == WashType::WashingAndIroning){
                    echo '<button type="button" id="btn_actual_items" class="btn btn-primary" data-toggle="modal" data-target="#modal_possible_items">Modificar Prendas</button>
                    
                     <!-- Modal -->
                                    <div id="modal_possible_items" class="modal fade modal-possible-items" role="dialog">
                                        <div class="modal-dialog">
                                            <!-- Modal content-->
                                            <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h4 class="modal-title">Calculadora de peso</h4>
                                            </div>
                                            <div class="modal-body">
                                                <p>Esta calculadora sólo da un aproximado, confirmaremos el peso real cuando nos llegue tu ropa.</p>
                                                <div class="modal-possible-items-placeholder">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <span class="modal-possible-items-weight">
                                                    Total <span id="modal_possible_items_weight">1</span> 
                                                    <span class="modal-possible-items-kilos">Kg</span>
                                                </span> 
                                            </div>
                                            <div class="modal-footer">
                                                <p>
                                                    <span class="highliht-text">Importante</span>.
                                                    Al enviarnos esta lista con prendas, nos ayudas a
                                                    controlar mejor que no hayan prendas extraviadas y te
                                                    sirve de comprobante.
                                                    <br/><b>TE RECOMENDAMOS USARLA</b> 
                                                </p>
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                            </div>
                                            </div>

                                        </div>
                                    </div>
                                    <!-- End Modal -->

                    <div id="modal_selected_items_placeholder" style="margin:0"></div>                           
                    <br/>

                    <div class="checkbox checkbox_ironing">
                        <label><input type="checkbox" id="checkbox_ironing" value="">Agregar planchado desde $3.000 x Kg</label>
                    </div>
                    <div id="ironing_placeholder" style="display: none;">
                        <div class="ironing_placeholder_items">
                        </div>
                        <button type="button" class="btn btn-primary ironing-add-more-item">
                                    <i class="fa fa-plus"></i> append one more
                        </button>
                        <div id="ironing_items_post_hidden"></div>

                    </div>   
                    
                    ';
                }
                else if($order->WashType == WashType::OnlyIroning){
                   //
                }
                else if($order->WashType == WashType::DryCleaning){
                    echo '
                                <div id="dry_cleaning_container">
                                    <div id="dry-cleaning-possible-items-placeholder">
                                    </div>
                                    <div id="dry_cleaning_items_post_hidden"></div>
                               </div> <!--dry_cleaning_container-->
                    ';
                }
            }
            ?>
            <br/>
            <br/>
                 <!--- ACTUAL DATA -->
           
                                          
                <input name="action_type" type="hidden" value="put_actual_data">
                <button type="submit" class="btn btn-primary">Enviar</button><span id="actual_form_saving_message"></span>
            </form>     
        </div>
           
        
        <br>
        <br>
        <h3>Feedback</h3>
         <?php
               $orderFeedback = OrderFeedback::GetOrderFeedbackByOrderNumber($orderNumber);         
        ?>   
        <div class="row item form-horizontal">
            <?php 
            if($orderFeedback != null){
                echo '
                         <div class="form-group">
                                <label for="rating_overall" class="col-sm-6 control-label">
                                    Evaluación General
                                </label>
                                <div class="col-sm-6 text-left star-rating-large">
                                    '.RatingControl('rating_overall', $orderFeedback->RatingOverall).'
                                </div>
                            </div>

                           <div class="form-group">
                                <label for="rating_easiness" class="col-sm-6 control-label">
                                    ¿Qué tan fácil te resulto hacer el pedido ? 
                                </label>
                                <div class="col-sm-6 text-left">
                                    '.RatingControl('rating_easiness', $orderFeedback->RatingEasiness).'
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="rating_ironing" class="col-sm-6 control-label">
                                    ¿Qué tan bien planchada quedo la ropa?  
                                </label>
                                <div class="col-sm-6 text-left">
                                    '.RatingControl('rating_ironing', $orderFeedback->RatingIroning).'
                                </div>
                            </div>';
                            if($order->HasWashing()){
                                echo '
                                 <div class="form-group">
                                    <label for="rating_washing" class="col-sm-6 control-label">
                                       ¿Qué tan bien lavada quedo la ropa?  
                                    </label>
                                    <div class="col-sm-6 text-left">
                                        '.RatingControl('rating_washing', $orderFeedback->RatingWashing).'
                                    </div>
                                </div>';
                            }    
                            
                        echo 
                        '
                            <div class="form-group">
                                <label for="rating_pickup" class="col-sm-6 control-label">
                                     ¿Cómo estuvo la coordinación para recoger y retornar tu ropa?   
                                </label>
                                <div class="col-sm-6 text-left">
                                    '.RatingControl('rating_pickup', $orderFeedback->RatingPickup).'
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="rating_recommend" class="col-sm-6 control-label">
                                     ¿Recomendarías WASHita a tus amigos? 
                                </label>
                                <div class="col-sm-6 text-left">
                                    '.RatingControl('rating_recommend', $orderFeedback->RatingRecommend).'
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="details" class="col-sm-6 control-label">
                                    ¿Qué cambiarias o mejorarias? 
                                </label>
                                <div class="col-sm-6">
                                <p>
                                '.$orderFeedback->Text.'
                                </p>
                                </div>
                            </div>
            
                        ';
                }
                else{
                        echo ' <div class="col-sm-12">-</div>';   
                }
            ?>
         </div>   
        <br>
<?php
if($order != null)
        echo '
         <form method="post">
            <h3>Reenviar email de confirmación de pedido a cliente.</h3>
            <fieldset class="form-group">
                <input name="action_type" type="hidden" value="resend_confirmation">
            </fieldset>
            <button type="submit" class="btn btn-primary">Reenviar</button>
        </form>
        ';
?>        

        <br>
        <?php

            $sql = "SELECT `ID`,`FLOW_NUMBER`, `STATUS`, `TRANSACTION_AMOUNT`, `PAYER_EMAIL`, `DESCRIPTION`, `RESPONSE_TYPE`, `CREATE_DATE` FROM `flow_payment` 
                WHERE ORDER_NUMBER = '".$orderNumber."'";

            $dbh = OpenPDOConnection();
            // create LM object, pass in PDO connection
            $lm = new lazy_mofo($dbh);
            // table name for updates, inserts and deletes
            $lm->table = 'flow_payment';
            // identity / primary key for table
            $lm->identity_name = 'ID';
            // optional, make friendly names for fields
            $lm->grid_default_order_by = "ID desc";

            $lm->grid_add_link = '';
            $lm->grid_export_link = '';
            $lm->grid_sql = $sql;
            
            //TODO
            $lm->rename["FLOW_NUMBER"] = "Flow n.";
            // etc... 

            echo "<h3>Historial de transacciones en FLOW</h3>";
            // use the lm controller
            $lm->run();
            
        ?>
        
    </div>
</div>
<br>
<br>


<?php
$jsInitWashItems = 'var actualWashItems = {};'; 
$isModifiedByAdmin = ($order->ActualWeight !== null) || ($order->ActualPriceWithDiscount !== null);

$washItemLines = $isModifiedByAdmin? $actualWashItemLines : $initWashItemLines;
foreach ($washItemLines as $washItemLine) {
    $jsInitWashItems.='actualWashItems["'.$washItemLine->WashItem->Id.'"]='.$washItemLine->Count.';';
}

$jsInitIroningItemLines = 'var ironingItemLines = [];'; 
$ironingItemLines = $isModifiedByAdmin? $actualIroningItemLines : $initIroningItemLines;

for($x =0; $x < count($ironingItemLines); $x++){
    $ironingItemLine = $ironingItemLines[$x];

    $jsInitIroningItemLines.='var customItem = new WashItem(WASHING_IRONING_TYPE, '.$x.', "'.$ironingItemLine->Name.'", 0, 0, 0,0,"");';
    $jsInitIroningItemLines.='var customItemLine = new WashItemLine('.$ironingItemLine->Count.', customItem);';
    $jsInitIroningItemLines.='ironingItemLines.push(customItemLine);';
} 


$jsInitDryCleaningItems = 'var actualDryCleaningItems = {};'; 
$dryCleaningItemLines = $isModifiedByAdmin? $actualWashItemLines : $initDryCleaningItemLines;
foreach ($dryCleaningItemLines as $dryCleaningItemLine) {
    $jsInitDryCleaningItems.='actualDryCleaningItems["'.$dryCleaningItemLine->WashItem->Id.'"]='.$dryCleaningItemLine->Count.';';
}

 $SCRIPTS_FOOTER.=
    '
    <script type="text/javascript" src="js/washitems.js"></script>

    <script>
    '.$jsInitWashItems.'

    '.$jsInitIroningItemLines.'

    '.$jsInitDryCleaningItems.'
        $(document).ready(function() {
            appOrder.sanitizeNumberInput();
            ';

if($order->WashType == WashType::WashingAndIroning){
$SCRIPTS_FOOTER.= '    
            var washingControl = new WashingWashItemsControl("#modal_possible_items", true);
            washingControl.OnWashItemChoosed = function(){
                    $("#modal_selected_items_placeholder").html(this.GetHtmlForChosenItems());
            };

            washingControl.SetWashItems(actualWashItems, function(){
                $("#modal_selected_items_placeholder").html(washingControl.GetHtmlForChosenItems());
            });


            var ironingControl = new IroningWashItemsControl("#ironing_placeholder","#checkbox_ironing");
            ironingControl.setItemsLines(ironingItemLines);
            if(ironingItemLines.length > 0){
                $("#checkbox_ironing").prop("checked", true);
                ironingControl.enable();
            }
            $("#actual_form").submit(function( event ) {
                event.preventDefault();
                //updateHiddenIroningItems
                     var ironing_items_post_hidden = $("#ironing_items_post_hidden");
                    ironing_items_post_hidden.html("");

                    if(ironingControl.totalItems() > 0){
                        $.each(ironingControl.washProduct.itemLines, function( key, washItemLine ) {
                            var inputHtml =  \'<input type="checkbox" style="display:none" name="ironing_items_post[]" value="\'+washItemLine.count+\',\'+washItemLine.item.Name+\'" checked>\';
                            
                            var hiddenInputItem = $.parseHTML(inputHtml);
                            ironing_items_post_hidden.append(hiddenInputItem);
                        });
                    }

                var data = $(this).serialize();
                $(this).find(":input").prop("disabled", true);
                $("#actual_form_saving_message").html("Saving is in process ...");
                
                $.post("'.$_SERVER['REQUEST_URI'].'", data)
                .done(function(data) {
                    location.reload();
                });
            });

           

            ';
}
else if($order->WashType == WashType::OnlyIroning){
$SCRIPTS_FOOTER.= '
            $("#actual_form").submit(function( event ) {
                event.preventDefault();

                var data = $(this).serialize();
                $(this).find(":input").prop("disabled", true);
                $("#actual_form_saving_message").html("Saving is in process ...");
                
                $.post("'.$_SERVER['REQUEST_URI'].'", data)
                .done(function(data) {
                    location.reload();
                });
            });
     ';
}
else if($order->WashType == WashType::DryCleaning){
$SCRIPTS_FOOTER.= '
            var drycleaningControl = new DryCleainigWashItemsControl("#dry-cleaning-possible-items-placeholder", true);
            drycleaningControl.SetWashItems(actualDryCleaningItems, function(){
                drycleaningControl.showInputItems();
            });

            $("#actual_form").submit(function( event ) {
                event.preventDefault();

                //Dry cleaning
                var dry_cleaning_items_post_hidden = $("#dry_cleaning_items_post_hidden");
                dry_cleaning_items_post_hidden.html("");

                if(drycleaningControl.HasAnyItem()){
                    $.each(drycleaningControl.washProduct.itemLines, function( key, washItemLine ) {
                        if(washItemLine.count > 0){
                            var inputHtml =  \'<input type="checkbox" style="display:none" name="dry_cleaning_items_post[]" value="\'+washItemLine.item.Id+\',\'+washItemLine.count+\'" checked>\';
                         
                            var hiddenInputItem = $.parseHTML(inputHtml);
                            dry_cleaning_items_post_hidden.append(hiddenInputItem);
                        }
                    });
                }
            
            
                var data = $(this).serialize();
                $(this).find(":input").prop("disabled", true);
                $("#actual_form_saving_message").html("Saving is in process ...");
                
                $.post("'.$_SERVER['REQUEST_URI'].'", data)
                .done(function(data) {
                    location.reload();
                });
            });


     ';
}

$SCRIPTS_FOOTER.='
                        $("#actual_weight").bind("change paste keyup", function() {
                                var sanitized = getSanitizedAndRoundedUpNumber(this.value, 1);

                                var minValue = $(this).attr("min");
                                if(minValue && sanitized < minValue){
                                    sanitized = minValue;
                                }
                                
                                $("#actual_weight_new").html("(New value: "+sanitized+")");
                        });
                    
                    
            
      ';

//end of document ready
$SCRIPTS_FOOTER.='
});
</script>';

include_once(dirname(__FILE__)."/templates/footer.admin.php");



?>