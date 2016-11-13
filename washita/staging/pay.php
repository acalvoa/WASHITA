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
    $webpay = new Webpay();
    $webpay->START_TRANS_WS($order->ActualPriceWithDiscount,$order->OrderNumber,$order->Email,$descritption);
}
?>

