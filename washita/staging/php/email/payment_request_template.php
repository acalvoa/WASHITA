<?php
require_once(dirname(__FILE__)."/../../_config.php");
require_once(dirname(__FILE__)."/../_helpers.php");
require_once(dirname(__FILE__)."/../Order.class.php");
require_once(dirname(__FILE__)."/../OrderWashItemLine.class.php");
require_once(dirname(__FILE__)."/../OrderCustomItemLine.class.php");
require_once(dirname(__FILE__)."/../WashType.enum.php");


// UNCOMMENT ONLY FOR A TEST and open page  /staging/php/email/payment_request_template.php
// $order = Order::GetOrderByNumber('21120');
// echo GetPaymentRequestBody($order);
function GetPaymentRequestBody(Order $order){
    global $PaymentService;
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
    $body .= '<span style="float:right;margin:20px 10px;font-size:20px;color:white">
                    Pagar Pedido 
            </span>
    </td>
    </tr>
    
    <tr>
    <td style="padding:10px 20px;">'.PHP_EOL;
        $body .= '<p style="margin-bottom:0">
                ¡Hola '.$order->Name.',  hemos revisado tu pedido y el valor a pagar es '.MoneyFormat($order->ActualPriceWithDiscount).'.
                </p>';
        // ORDER Start
        $body .= '<table border="0" style="font-size:12px;margin-left:20px"><tr><td>';
        $body .= "<p>Número de Orden: ".$order->OrderNumber.".</p>";
        if($order->WashType == WashType::WashingAndIroning){
            $body .= "<p>Peso: ".$order->ActualWeight." Kg.</p>";
            $actualWashItemLines = OrderWashItemLine::GetActualItemsForOrder($order->OrderNumber);
            if(count($actualWashItemLines) > 0){
                $body .= "<p>Prendas a lavar: ".OrderWashItemLine::LinesToString($actualWashItemLines)."</p>";
            }
            $actualIroningItemLines = OrderCustomItemLine::GetCurrentItemsForOrder(WashType::OnlyIroning, $order->OrderNumber, true);
            if(count($actualIroningItemLines) > 0){
                $body .= "<p>Prendas a planchar: ".OrderCustomItemLine::LinesToString($actualIroningItemLines)."</p>";
            }
        }
        else if($order->WashType == WashType::OnlyIroning){
            $body .= "<p>Peso: ".$order->ActualWeight." Kg.</p>";
        }
        else if($order->WashType == WashType::DryCleaning){
            $actualWashItemLines = OrderWashItemLine::GetActualItemsForOrder($order->OrderNumber);
            $body .= "<p>Prendas a lavaseco: ".OrderWashItemLine::LinesToString($actualWashItemLines)."</p>";
        }

        $body .= '</td></tr></table>';
        // ORDER End
        if($PaymentService == 'webpay'){
            $body .= '<p>
                    Para procesar este pedido, por favor completar el pago de '.MoneyFormat($order->ActualPriceWithDiscount).' haciendo click en el siguiente botón.
                </p>
                <a href="'.$GLOBALS['site_root'].'/pay.php?orderNumber='.$order->OrderNumber.'" >
                        <div style="height:50px;"><img src="'.$GLOBALS['site_root'].'/img/webpayplus.jpg" height="50" style="float:left; margin-right: 20px;"/><div style="
                                                padding: 10px 40px;
                                                font-size: 18px;
                                                color: #EBF3E9;
                                                border-radius: 10px;
                                                background-color: #228b22;
                                                border-width: 1px;
                                                text-decoration: initial;
                                                margin-top: 5px;
                                                float: left;
                    ">Pagar con Webpay</div></div>
                    </a> <br>
                    <a href="'.$GLOBALS['site_root'].'/pay.php?orderNumber='.$order->OrderNumber.'" >
                        <div style="height:50px;"><img src="'.$GLOBALS['site_root'].'/img/oneclick.png" height="50" style="float:left; margin-right: 20px;"/><div style="
                                                padding: 10px 40px;
                                                font-size: 18px;
                                                color: #EBF3E9;
                                                border-radius: 10px;
                                                background-color: #228b22;
                                                border-width: 1px;
                                                text-decoration: initial;
                                                margin-top: 5px;
                                                float: left;
                    ">Pagar con OneClick</div></div>
                    </a> 
                     <br/>
                    <br/>
                    <br/>
                ';
        }
        else {
            $body .= '<p>
                    Para procesar este pedido, por favor completar el pago de '.MoneyFormat($order->ActualPriceWithDiscount).' haciendo click en el siguiente botón.
                </p>
                <a href="'.$GLOBALS['site_root'].'/pay.php?orderNumber='.$order->OrderNumber.'" style="
                                                padding: 10px 40px;
                                                font-size: 18px;
                                                color: #EBF3E9;
                                                border-radius: 10px;
                                                background-color: #228b22;
                                                border-width: 1px;
                                                text-decoration: initial;
                                                display: inline-block;
                    ">
                        Pagar
                    </a> 
                     <br/>
                    <br/>
                    <br/>
                ';
        }
        
        
        
        $body .= 'Cualquier pregunta, cambio en tu pedido o duda, por favor envíanos un correo a service@washita.cl a la brevedad. ¡Pronto estaremos recogiendo tu ropa!
            </td>
        </tr>
        <tr>
            <td style="padding:10px 20px;text-align: center;color:grey">
            <hr style="border-style: dashed;color: #66cdcc;"/>
            
            <p style="font-size:14px">¿Preguntas? ¡Contáctanos!</p>
             
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