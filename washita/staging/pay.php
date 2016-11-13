<?php

require_once(dirname(__FILE__)."/_config.php");
require_once(dirname(__FILE__).'/php/Order.class.php');
include_once(dirname(__FILE__)."/php/kpf/flowAPI.php");
include_once(dirname(__FILE__)."/php/kpf/config.php");

global $PaymentService;

$orderNumber = $_GET["orderNumber"];
$order = Order::GetOrderByNumber($orderNumber);
     
     
$descritption = "Washita.cl Orden N°".$order->OrderNumber.". Pago.";

if($PaymentService == "flow"){
    $flowAPI = new flowAPI();
    $flow_pack = $flowAPI->new_order($order->OrderNumber, $order->ActualPriceWithDiscount, $descritption, $order->Email);

    // redirect to flow via javascript
    echo '
    <html>
        <body>
            La reorientacion de servicio Flow.<br/>Por favor espera.<br/>
            <form method="post" name="frm" action="'.$flow_url_pago.'">
            <input type="hidden" name="parameters" value="'.$flow_pack.'" />
            <button type="submit">Pagar en Flow manualmente</button> </form>
            <script language="JavaScript">document.frm.submit();</script>
        </body>
    </html>
    ';
}
else if($PaymentService == "webpay"){
    // TODO: implement webpay
    require_once(dirname(__FILE__)."/php/transbank/Webpay.php");
    // $webpay = new Webpay();
    // $webpay->START_TRANS_WS($order->ActualPriceWithDiscount,$order->OrderNumber,$order->Email,$descritption);
    include_once(dirname(__FILE__)."/templates/header.general.php");
?>
<div class="container">
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
        <button type="submit" class="pay_btn hvr-glow">CONFIRMAR PEDIDO</button>
    </div>
    <div class="row item checkout-block oneclick_tab">
        <div class="input-group-vertical">
            <p>Elige La tarjeta de pago</p>
        </div>
        <div class="input-group-horizontal">
            <div class="tc_input_row">
                <select name="TBK_USER" class="form-control" required>
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
        <button type="submit" class="pay_btn hvr-glow">CONFIRMAR PEDIDO</button>
    </div>
</div>
<script>
        $(document).ready(function() {
            $("#checkout_form input[name=payment_method]").on("click", function() {
                if($("input[name=payment_method]:checked", "#checkout_form").val() == "oneclick"){
                    $(".pay_tab").hide();
                    $(".oneclick_tab").show();
                    $("#checkout_form").attr("action", "'.$GLOBALS['TBK_AUTHORIZE_ONECLICK'].'");
                } 
                else{
                    $(".pay_tab").show();
                    $(".oneclick_tab").hide();
                    $("#checkout_form").attr("action", "'.$GLOBALS['TBK_INIT_TRANS_LINK'].'");
                }
            });
            $("#add_tc_action").on("click", function(){
                location.href="php/transbank/ep_webpay.php?action=ONECLICK_INSCRIPTION";
            });
        });
<?php
    include_once(dirname(__FILE__)."/templates/footer.general.php");
}
?>

