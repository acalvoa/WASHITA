<?php
require_once(dirname(__FILE__)."/../../_config.php");
require_once(dirname(__FILE__)."/../_helpers.php");
require_once(dirname(__FILE__)."/../Order.class.php");
include_once(dirname(__FILE__)."/../OrderWashItemLine.class.php");
include_once(dirname(__FILE__)."/../OrderCustomItemLine.class.php");
include_once(dirname(__FILE__)."/../WashItem.class.php");
include_once(dirname(__FILE__)."/../WashType.enum.php");

// UNCOMMENT ONLY FOR A TEST and open page  /staging/php/email/confirmation_template.php
// 
// Find in the database correct order number

//   $order_number = '21119';
//   $order = Order::GetOrderByNumber($order_number);
//   $washItemLines = OrderWashItemLine::GetCurrentItemsForOrder($order_number);
//   $ironingItemLines = OrderCustomItemLine::GetCurrentItemsForOrder(WashType::OnlyIroning, $order_number, false);


//   echo GetEmailBody($order, $washItemLines, $ironingItemLines);      

function GetEmailBody($order, $washItemLines, $ironingItemLines){

$cityAndArea = $order->GetCityAndArea();

$body = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>Washita</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>
<body style="margin: 0; padding: 0; background:#fff; font-family:lucida sans unicode,lucida grande,sans-serif;">
 <table align="center" border="0" cellpadding="0" cellspacing="0" style="max-width:600px;border-collapse: collapse; background:white; border-style:solid;border-color: #336799;margin:20px auto">
 <tr style="background:#336799">
  <td style="padding:10px 20px;">';
 $body .= '<img src="'.$GLOBALS['site_root'].'/img/freeze/logo.png'.'" />';
 $body .= '<span style="float:right;margin:20px 10px;font-size:20px;color:white">Confirmación de Pedido</span>
  </td>
 </tr>
 <tr>
  <td style="padding:10px 20px; text-align: center;">

    <p>Hola '.$order->Name.',
        <br/>
        Hemos recibido tu pedido y ya ha sido agendado el retiro de tu ropa sucia en
        '.($cityAndArea != null? $cityAndArea->GetFullName():"").', '.$order->Address.'
    </p>';
  $body .= '
  <br/>
  <p style="color:rgb(102,102,102);">Pasaremos a buscar tu ropa el <b>'.$order->PickupTime->asText().'</b></p>';
  $body .= '<p style="color:rgb(102,102,102);">Te la devolveremos limpia el <b>'.$order->DropOff->asText().'</b></p>';
  $body .= '</td>
 </tr>
 <tr>
  <td style="padding:10px 20px;">';
    // ORDER Start
    $body .= '<table border="0" style="font-size:12px;margin-left:50px;margin-top: -12px;width: 100%;"><tr><td>';
    $body .= '<p style="font-size:26px;line-height: 10px;"><strong>Orden '.$order->OrderNumber.'</strong></p>';
    if(count($washItemLines)>0){
        $body .= "<p>".$order->WashingTypeText().":</p>";        
        $body .= "<div style='margin-left: 10px;'>";
        foreach ($washItemLines as $itemLine) {
            $body .= "<p>".$itemLine->Count."x ".$itemLine->WashItem->Name."</p>";
        }
        $body .= "</div>";
    }

    if(count($ironingItemLines)>0){
        $body .= "<p>Planchado:</p>";        
        $body .= "<div style='margin-left: 10px;'>";
        foreach ($ironingItemLines as $ironingItemLine) {
            $body .= "<p>".$ironingItemLine->Count."x ".$ironingItemLine->Name."</p>";
        }
        $body .= "</div>";
    }



    if(!empty($order->Comment)){
        $body .= "<p>Peticiones Especiales: ".$order->Comment.".</p>";        
    }

    if($order->IsWeightRequired()){
        $body .= "<p>Peso Estimado: ".NumberFormatWithTens($order->Weight)." Kg.</p>";
    }
    $body .= "<p>Servicio: ".$order->WashingTypeText().".</p>";
 

    $body .= '<p style="font-size:20px;line-height: 25px;text-align:right;margin-right: 50px;margin-top:-3px">
                Precio Estimado: '.MoneyFormat($order->PriceWithDiscount).'</p>';
    $body .= '</td></tr></table>';
    // ORDER End
    

    $body .='<br/><br/>
    <p style="font-size:22px;color:rgb(102,102,102);text-align:center;">¡Pronto tendrás tu ropa limpia!</p>';
    
    $body .= 'Cualquier duda o cambio en tu pedido, por favor, escríbenos a 
                <a href="mailto:service@washita.cl" target="_top">service@washita.cl</a> 
                a la brevedad. ¡Pronto estaremos recogiendo tu ropa!
        </td>
    </tr>
    <tr>
        <td style="padding:10px 20px;text-align: center;color:grey">
        <hr style="border-style: dashed;color: #66cdcc;"/>
        
        <p style="font-size:14px">¿Preguntas? ¡Contáctanos!</p>
        <a href="tel:+56(22) 405 3056">+56(22) 405 3056</a> | 
        <a href="mailto:service@washita.cl" target="_top">service@washita.cl</a>
        
        <hr style="border-style: dashed;color: #66cdcc;"/>
        <span style="font-size:12px; float:right">© 2016 Washita.cl<span>
        </td>
    </tr>
    </table>
    </body>

</html>';

return $body;
}

?>