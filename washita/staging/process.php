<?php 
require_once(dirname(__FILE__).'/php/PickupTime.class.php');
require_once(dirname(__FILE__)."/php/City.class.php");
require_once(dirname(__FILE__)."/php/hybridauth/WashitaUser.php");
require_once(dirname(__FILE__)."/_config.php");

include_once(dirname(__FILE__)."/templates/header.general.php");
include_once(dirname(__FILE__)."/php/transbank/OneClick.php");


// UNCOMMENT ONLY FOR TEST
//   require_once(dirname(__FILE__).'/php/CurrentDateTime.class.php');
//  CurrentDateTime::SetCurrentDateTime(DateTime::createFromFormat('d-M-Y H:i', '04-Sep-2016 07:05'));

  $user = WashitaUser::CurrentUser();
  global $DiscountPersonalCode;
?>

    <section id="order">
        <div class="container">
            <div class="section-heading section-order">
                <h1>Lavar Ahora</h1>
                <div class="divider"></div>
                <div class="page padding">
                    <form id="checkout_form" method="post" action="<?php echo $GLOBALS['TBK_INIT_TRANS_LINK'];?>">
                        
                        <div class="row item checkout-block border-between">
                            <div class="col col-sm-12">
                                <div class="input-group btn-group laundry-option" data-toggle="buttons">
                                        <?php  
                                            $laundryOption = GetGet('laundry_option');
                                            $is_washing_and_ironing = empty($laundryOption) || $laundryOption = "washing_and_ironing";
                                        ?>
                                        <label class="btn btn-primary 
                                            <?php if($is_washing_and_ironing) echo 'active'; ?>
                                            ">
                                            <input type="radio" name="laundry_option" value="washing_and_ironing" autocomplete="off"
                                            <?php if($is_washing_and_ironing) echo 'checked'; ?>
                                            > Lavado y 
                                                <br class="hidden-lg"/>
                                                Planchado
                                        </label>
                                        <label class="btn btn-primary">
                                            <br class="hidden-lg"/>
                                            <input type="radio" name="laundry_option" id="dry_cleaning" value="dry_cleaning" autocomplete="off" 
                                            > Lavaseco 
                                        </label>
                                        <!--
                                        <label class="btn btn-primary">
                                            <input type="radio" name="laundry_option" id="special_cleaning" value="special_cleaning" autocomplete="off" 
                                            > Lavado por
                                            <br class="hidden-lg"/>
                                            Prenda
                                        </label>
                                        -->
                                    </div>
                            </div>                        
                            <div class="col col-sm-6 col-no-left-border">
                                <div id="washing_ironing_container">
                                <div class="checkbox">
                                    <label><input type="checkbox" id="checkbox_washing" name="checkbox_washing" value="true" checked>
                                        <span>Lavado y Doblado</span>
                                    </label>
                                    <div id="washing_details_container">
                                        <p class="washing-checkbox-subtitle">¿Cuánta Ropa?</p>
                                                    <div class="input-group">
                                                        <div id="weight-block">
                                                                <input class="form-control decimals-with-hundreds items" 
                                                                id="weight" name="weight" type="number" min="1" max="1000" step="0.01" value="1" 
                                                                lang="es" 
                                                                />
                                                            <div class="order-kg-cell">
                                                                <span class="order-kg">Kg</span>
                                                            </div>
                                                            <div class="btn-state-washing-items-cell hidden-sm-or-less">
                                                                <button type="button" class="btn btn-primary btn-state-washing-items" data-toggle="modal">
                                                                    ¿No sabes el peso?
                                                                </button>
                                                            </div>
                                                        </div>    
                                                        <div class="table-row hidden-md-or-greater">
                                                                <div class="btn-state-washing-items-cell">
                                                                    <button type="button" class="btn btn-primary btn-state-washing-items" data-toggle="modal">
                                                                        ¿No sabes el peso?
                                                                    </button>
                                                                </div>
                                                        </div>
                                                    </div>

                                        <div id="modal_selected_items_placeholder">
                                        </div>
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
                                                            TOTAL ESTIMADO <span id="modal_possible_items_weight">1</span> 
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
                                </div><!-- washing_details_container -->

                                       
                            </div><!--checkbox-->
                                    
                                 
                                    
                                    

                                    <div id="ironing_container">
                                        <div class="checkbox checkbox_ironing">
                                            <label>
                                                <input type="checkbox" id="checkbox_ironing" value="true">
                                                <span>Agregar planchado</span>
                                            </label>
                                            <p class="washing-checkbox-subtitle">desde $3.000 x Kg</p>
                                            <div id="ironing_placeholder" style="display: none;">
                                                <div class="ironing_placeholder_items">
                                                </div>
                                                <button type="button" class="btn btn-primary ironing-add-more-item">
                                                    <i class="fa fa-plus"></i> append one more
                                                </button>
                                                <div id="ironing_items_post_hidden"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div> <!--washing_ironing_container-->

                                <div id="dry_cleaning_container" style="display: none;">
                                    <div id="dry-cleaning-possible-items-placeholder">
                                    </div>
                                    <div id="dry_cleaning_items_post_hidden"></div>
                               </div> <!--dry_cleaning_container-->

                                <div class="input-group">
                                    <span class="input-group-addon">Código Descuento </span>
                                    <input class="form-control text-uppercase" id="discount_coupon" name="discount_coupon" type="text" minlength="5" maxlength="30"
                                         value="<?php echo ($user != null && $user->PersonalDiscountAmount > 0 ? $DiscountPersonalCode :""); ?>"
                                     />
                                </div>
                                <div class="alert alert-danger collapse" id="discountWarningMessage">
                                    
                                  </div>
                            </div>
                            <div class="col col-sm-6">
                                <p>Resumen de Orden</p>
                                <div id="order_line_weight" class="order-line"><span class="order-line-label">Cantidad de Kg a <span id="selected_washing_text"></span></span><span id="one_kilo_pack_price" class="order-line-value">0</span></div>
                                <div id="order_line_ironing" class="order-line" style="display: none;"><span class="order-line-label"><span class="selected_ironing_items_total"></span> items <span class="order-line-capitalized">Planchado</span> </span>    
                                                                                <span class="order-line-value"> <span class="selected_ironing_items_total"></span> x <span id="ironing_item_price">$0</span></span>
                                </div>
                                <div class="order-line"><span class="order-line-label">Descuento</span><span id="dicount_procent" class="order-line-value">0</span></div>
                                <div class="divider-wide"></div>
                                <div>
                                    <span class="total-price-label">TOTAL ESTIMADO</span>
                                    <span id="total_price"></span> 
                                </div>
                            </div>
                        </div>

                        <div class="row item checkout-block">
                            <div class="input-group-vertical">
                                <p>Nombre*</p>
                                <input type="text" name="name" class="form-control" placeholder="" maxlength="256" required
                                    value="<?php echo ($user != null? $user->FullName():""); ?>"
                                />
                            </div>
                            <div class="col col-sm-6 input-group-vertical">
                                <p>Ciudad*</p>
                                <select name="city_area_id" class="form-control" required>
                                    <?php 
                                        $cities = City::GetAllCititesWithAreas();
                                        foreach ($cities as $city) {
                                            foreach ($city->Areas as $cityArea) {
                                                $isSelected = ($user != null && $user->CityAreaId == $cityArea->Id);
                                                echo '<option value="'.$cityArea->Id.'" '.($isSelected? "selected":"").'>'.$city->Name.' / '.$cityArea->Name.'</option>';
                                            }
                                        }

                                    ?>
                                                                   
                                </select>
                            </div>
                            <div class="col col-sm-6 input-group-vertical">
                                <p>Dirección*</p>
                                <input type="text" name="address" class="form-control" placeholder="" maxlength="1024" required
                                    value="<?php echo ($user != null? $user->Address:""); ?>"
                                >
                            </div>
                             <div class="col col-sm-6 input-group-vertical ">
                                 <p>Email*</p>
                                <input type="email" name="email" class="form-control" placeholder=""
                                    maxlength="124" required
                                    value="<?php echo ($user != null? $user->NotificationEmail:""); ?>"
                                />
                            </div>
                             <div class="col col-sm-6 input-group-vertical">
                                 <p>Whatsapp</p>
                                <input type="text" name="whatsapp" class="form-control" placeholder=""
                                    maxlength="20" 
                                    value="<?php echo ($user != null? $user->Phone:""); ?>"
                                />
                            </div>
                            <div class="col col-sm-6 col-md-3 input-group-vertical">
                                <p>Horario de Retiro*</p>
                                <input type='hidden' id="pickup_datetime" name="pickup_datetime" />
                            <?php echo '<div class="input-group datepicker date" id="order_datepicker" data-min-datetime="'.PickupTime::GetMinPickupTime()->from->format('Y/m/d H:i:s').'">' ?>
                                            <input type='text' id="pickupdate" name="pickupdate" class="form-control" />
                                            <span class="input-group-addon">
                                                <span class="fa fa-calendar"></span>
                                            </span>
                                        </div>
                                <p class="help-block">*only working days</p>
                                   
                            </div>
                            <div class="col col-sm-6 col-md-3 input-group-vertical">
                                <p class="hidden-xxs">&nbsp;</p>
                                    <div class='input-group'>
                                         <span class="input-group-addon">
                                            <span class="fa fa-clock-o"></span>
                                         </span>
                                        <select id="pickuptime" name="pickuptime" class="selectpicker form-control" data-live-search="true" required>
                                                <?php echo '<option value="'.PickupTime::TodayMorning()->formatRange("H:i","-").'">'.PickupTime::TodayMorning()->formatRange("ga"," - ").'</option>';?>
                                                <?php echo '<option value="'.PickupTime::TodayEvening()->formatRange("H:i","-").'">'.PickupTime::TodayEvening()->formatRange("ga"," - ").'</option>';?>
                                            </select>
                                       
                                   </div>
                                <p class="help-block">&nbsp</p>
                            </div>
                            <div class="col col-sm-6 col-md-3 input-group-vertical">
                                <p>Horario de Entrega*</p>
                                <div class="input-group datepicker date" id="dropoff_datepicker">
                                    <input type='hidden' id="dropoff_datetime" name="dropoff_datetime" />
                                            <input type='text' id="dropoffdate" name="dropoffdate" class="form-control" />
                                            <span class="input-group-addon">
                                                <span class="fa fa-calendar"></span>
                                            </span>
                                </div>   
                                <p class="help-block">*only working days</p>
                            </div>
                            <div class="col col-sm-6 col-md-3 input-group-vertical">
                                <p class="hidden-xxs">&nbsp;</p>
                                    <div class='input-group'>
                                         <span class="input-group-addon">
                                            <span class="fa fa-clock-o"></span>
                                         </span>
                                        <select id="dropofftime" name="dropofftime" class="selectpicker form-control" data-live-search="true" required>
                                                <?php echo '<option value="'.PickupTime::TodayMorning()->formatRange("H:i","-").'">'.PickupTime::TodayMorning()->formatRange("ga"," - ").'</option>';?>
                                                <?php echo '<option value="'.PickupTime::TodayEvening()->formatRange("H:i","-").'">'.PickupTime::TodayEvening()->formatRange("ga"," - ").'</option>';?>
                                            </select> 
                                       
                                   </div>
                                <p class="help-block">&nbsp</p>
                            </div>
                              <div class="col col-sm-12 input-group-vertical ">
                                <p>Peticiones Especiales</p>
                                <textarea rows="4" maxlength="3000" placeholder="Escribe aquí si tienes indicaciones especiales" name="comment" class="form-control"></textarea>
                            </div>
                            <div class="col col-sm-12 input-group-vertical ">
                                <label id="lblTerms">
                                    <input type="checkbox" name="terms" value="True" required/>
                                        <span>
                                            Acepto los 
                                            <a href="user_terms.php" target="_blank">términos y condiciones de uso.</a>
                                        </span>
                                </label>
                                <div id="message-fail" class="alert alert-danger" style="display:none">
                                </div>
                            </div>
                
                            
                        </div>

                        <div class="row item checkout-block">
                            <div class="input-group-vertical">
                                <p>Elige tu medio de pago</p>
                            </div>
                            <div class="input-group-horizontal">
                                <div class="payelement">
                                    <div class="inputfield"><input class="payment_method" type="radio" name="payment_method" value="webpay" checked></div>  
                                    <div class="logofield"><img class="webpay-logo" src="img/logo-webpay.png" height="100"></div>
                                </div>
                                <div class="payelement">
                                    <div class="inputfield"><input class="payment_method" type="radio" name="payment_method" value="oneclick"></div> 
                                    <div class="logofield" style="padding-top:5px;"><img class="webpay-logo" src="img/oneclick.png" height="80"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row item checkout_footer pay_tab">
                            <button type="submit" class="pay_btn hvr-glow">Pagar</button>
                        </div>
                        <div class="row item checkout-block oneclick_tab">
                            <div class="input-group-vertical">
                                <p>Elige La tarjeta de pago</p>
                            </div>
                            <div class="input-group-horizontal">
                                <div class="tc_input_row">
                                    <select name="city_area_id" class="form-control" required>
                                        <option value="-1">Seleccione la tarjeta de pago</option>
                                        <?php 
                                            $providers = OneClick::GETPROVIDERS();
                                            foreach ($providers as $provider) {
                                                echo '<option value="'.$provider['TBK_USER'].'" >('.strtoupper($provider['CREDIT_CARD_TYPE']).')  XXXX XXXX XXXX '.$provider['LAST4NUMBER'].'</option>';
                                            }
                                        ?>                               
                                    </select>
                                </div>
                                <div class="tc_add_row">
                                    <button type="button" class="add_tc_btn" id="add_tc_action">+ Agregar tarjeta</button>
                                </div>
                            </div>
                        </div>
                        <div class="row item checkout_footer oneclick_tab">
                            <button type="submit" class="pay_btn hvr-glow">Pagar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
<script>
</script>

 <?php 
 if(!isset($SCRIPTS_FOOTER)){
  $SCRIPTS_FOOTER = "";
}
 $SCRIPTS_FOOTER.=
    '<script type="text/javascript" src="js/washitems.js"></script>

    <script>
        $(document).ready(function() {
            appOrder.showPrice("0","cargando","cargando","cargando"); 

            appOrder.initDatePicker();
            appOrder.sanitizeNumberInput();
            appOrder.initToolTips();
            appOrder.bindPrice();
            
            $("#checkbox_washing").change(function() {
                if($(this).is(":checked")){
                    $("#washing_details_container").show();
                    $("#order_line_weight").show();
                }
                else{
                    $("#washing_details_container").hide();
                    $("#order_line_weight").hide();
                }
            });

            $("#checkbox_ironing").change(function() {
                if($(this).is(":checked")){
                    $("#order_line_ironing").show();
                }
                else{
                    $("#order_line_ironing").hide();
                }
            });

            $("#checkout_form input[name=payment_method]").on("click", function() {
                if($("input[name=payment_method]:checked", "#checkout_form").val() == "oneclick"){
                    $(".pay_tab").hide();
                    $(".oneclick_tab").show();
                } 
                else{
                    $(".pay_tab").show();
                    $(".oneclick_tab").hide();
                }
            });
            $("#add_tc_action").on("click", function(){
                location.href="php/transbank/ep_webpay.php?action=ONECLICK_INSCRIPTION";
                // $.ajax({
                //     url: "",
                //     method: "POST",
                //     data: {},
                //     success: function(res){
                //         result = JSON.parse(res);
                //         window.open(result.url);
                //     }
                // })
            });
            

            var washingControl = new WashingWashItemsControl("#modal_possible_items", true);
            washingControl.OnWashItemChoosed = function(){
                    $("#modal_selected_items_placeholder").html(this.GetHtmlForChosenItems());
                    $("#weight").val(this.washProduct.getSanitizedWeight());

                    appOrder.recalculatePrice();
            };
            var ironingControl = new IroningWashItemsControl("#ironing_placeholder","#checkbox_ironing");
            ironingControl.onWashItemAmountChanged = function(){
                    appOrder.totalIroningItems = this.totalItems();
                    appOrder.recalculatePrice();
            };

            var drycleaningControl = new DryCleainigWashItemsControl("#dry-cleaning-possible-items-placeholder", true);
            drycleaningControl.onWashItemAmountChanged = function(){
                appOrder.dryCleaningItemLines = drycleaningControl.washProduct.itemLines;
                appOrder.recalculatePrice();
            };

            $(\'input[name="laundry_option"]\').change(function() {

                    var selectedLaundry = $(this).val();
                    if(selectedLaundry == "washing_and_ironing"){
                        $("#washing_ironing_container").show();
                        $("#dry_cleaning_container").hide();
                    }
                    else{
                        $("#washing_ironing_container").hide();
                        $("#dry_cleaning_container").show();
                    }                    
                    
                    appOrder.recalculatePrice();
            });

            $(".btn-state-washing-items").click(function(event){
                    event.preventDefault();
                
                    $("#modal_possible_items").modal("show");
            });


            $("#checkout_form").submit(function( event ) {
                var selectedLaundry = $(\'input[name="laundry_option"]:checked\').val();
                var isLaundrybyItem = selectedLaundry === "dry_cleaning" || selectedLaundry === "special_cleaning"; 

                if(isLaundrybyItem && (!washingControl.HasAnyItem() && !drycleaningControl.HasAnyItem())){
                    event.preventDefault();
                    $("#message-fail").show().html("<strong>¡Atención!</strong> Debe seleccionar las prendas que desea enviar.");
                }
                if(appOrder.priceWithDiscount > 0 && appOrder.priceWithDiscount < 350){
                    event.preventDefault();
                    $("#message-fail").show().html("<strong>¡Atención!</strong> El precio mínimo es $350.");
                }

                //Ironing
                var ironing_items_post_hidden = $("#ironing_items_post_hidden");
                ironing_items_post_hidden.html("");

                if(ironingControl.totalItems() > 0){
                     $.each(ironingControl.washProduct.itemLines, function( key, washItemLine ) {
                         var inputHtml =  \'<input type="checkbox" style="display:none" name="ironing_items_post[]" value="\'+washItemLine.count+\',\'+washItemLine.item.Name+\'" checked>\';
                         
                         var hiddenInputItem = $.parseHTML(inputHtml);
                        ironing_items_post_hidden.append(hiddenInputItem);
                    });
                }

                //Dry cleaning
                var dry_cleaning_items_post_hidden = $("#dry_cleaning_items_post_hidden");
                dry_cleaning_items_post_hidden.html("");

                if(selectedLaundry === "dry_cleaning" && drycleaningControl.HasAnyItem()){
                    $.each(drycleaningControl.washProduct.itemLines, function( key, washItemLine ) {
                        if(washItemLine.count > 0){
                            var inputHtml =  \'<input type="checkbox" style="display:none" name="dry_cleaning_items_post[]" value="\'+washItemLine.item.Id+\',\'+washItemLine.count+\'" checked>\';
                            
                            var hiddenInputItem = $.parseHTML(inputHtml);
                            dry_cleaning_items_post_hidden.append(hiddenInputItem);
                        }
                    });
                }

            });
        });
    </script>
    
    
    <!-- Google Code for Lavado Conversion Page -->
    <script type="text/javascript">
        /* <![CDATA[ */
        var google_conversion_id = 874782056;
        var google_conversion_language = "en";
        var google_conversion_format = "3";
        var google_conversion_color = "ffffff";
        var google_conversion_label = "HmpeCM_Gj2oQ6LqQoQM";
        var google_remarketing_only = false;
        /* ]]> */
    </script>
    <script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
    </script>
    <noscript>
        <div style="display:inline;">
        <img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/874782056/?label=HmpeCM_Gj2oQ6LqQoQM&amp;guid=ON&amp;script=0"/>
        </div>
    </noscript>
    ';

include_once(dirname(__FILE__)."/templates/footer.general.php");
 ?>