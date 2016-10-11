<?php

require_once(dirname(__FILE__)."/_config.php");
require_once(dirname(__FILE__).'/php/Order.class.php');
include_once(dirname(__FILE__)."/php/kpf/flowAPI.php");
include_once(dirname(__FILE__)."/php/kpf/config.php");


$orderNumber = $_GET["orderNumber"];
$order = Order::GetOrderByNumber($orderNumber);
     
     
$flowAPI = new flowAPI();
$descritption = "Washita.cl, Order N".$order->OrderNumber.". Additional payment.";
$flow_pack = $flowAPI->new_order($order->OrderNumber, $order->AdditionalPriceWithDiscount, $descritption, $order->Email);
   
?>

<html>
<body>
    La reorientacion de servicio Flow.<br/>Por favor espera.<br/>
    <form method="post" name="frm" action="<?php echo $flow_url_pago ?>">
    <input type="hidden" name="parameters" value="<?php echo $flow_pack ?>" />
    <button type="submit">Pagar en Flow manualmente</button> </form>
    <script language="JavaScript">document.frm.submit();</script>
 </body>
 </html>
 