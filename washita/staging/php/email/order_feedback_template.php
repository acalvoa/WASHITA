<?php
require_once(dirname(__FILE__)."/../../_config.php");
require_once(dirname(__FILE__)."/../_helpers.php");
require_once(dirname(__FILE__)."/../Order.class.php");
require_once(dirname(__FILE__)."/../OrderRequiredFeedback.class.php");


// UNCOMMENT ONLY FOR A TEST and open page  /staging/php/email/order_feedback_template.php
//  $orderRequiredFeedback = new OrderRequiredFeedback();
//  $orderRequiredFeedback->FeedbackCode = "asdasd123123saea";

//  $order = Order::GetOrderByNumber('21064');

//  echo GetFeedbackRequestBody($orderRequiredFeedback, $order);

function GetFeedbackRequestBody(OrderRequiredFeedback $orderRequiredFeedback, Order $order){
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
                    Tu pedido ya fue entregado
            </span>
    </td>
    </tr>
    
    <tr>
    <td style="padding:10px 20px;">';
        $body .= '<p style="margin-bottom:0">
                ¡'.$order->Name.', gracias por escoger washita!
                  <br/> 
                  tu ropa ya ha sido entregada
                </p>';
        $body .= "<p>
                 Tu pedido fue entregado hoy <b>".strftime("%d-%b-%Y", $order->PickupTime->to->getTimestamp())."</b>
                 en ".$order->GetFullAddress()."<br/>
                 Número de Orden: ".$order->OrderNumber."<br/>
                 Servicio: ".$order->WashingTypeText()."<br/>
                 Peso: ".NumberFormatWithTens($order->Weight)." Kg <br/>
                 Monto Cancelado: ".MoneyFormat($order->PriceWithDiscount)."<br/>
                </p>";

        $baseUrl = $GLOBALS['site_root'].'/order_feedback.php?feedbackCode='.$orderRequiredFeedback->FeedbackCode;
        $body .= '<p>
                    <br/><b style="color: #30669B; font-size: 20px">Califica nuestro servicio</b>
                </p>
                    <br/> 
                  <div>  
                    <a href="'.$baseUrl.'&rating=1" style="  
                                                    padding: 10px 25px;
                                                    margin: 5px;
                                                    font-size: 18px;
                                                    color: #fff;
                                                    border-radius: 10px;
                                                    background-color: #0E3E6C;
                                                    border-width: 1px;
                                                    text-decoration: initial;
                                                    display: inline-block;
                        ">
                            Malo
                        </a>
                        <a href="'.$baseUrl.'&rating=2" style="  
                                                    padding: 10px 25px;
                                                    margin: 5px;
                                                    font-size: 18px;
                                                    color: #fff;
                                                    border-radius: 10px;
                                                    background-color: #19538A;
                                                    border-width: 1px;
                                                    text-decoration: initial;
                                                    display: inline-block;
                        ">
                            Regular
                        </a>
                        <a href="'.$baseUrl.'&rating=3" style="  
                                                    padding: 10px 25px;
                                                    margin: 5px;
                                                    font-size: 18px;
                                                    color: #fff;
                                                    border-radius: 10px;
                                                    background-color: #4E7EAB;
                                                    border-width: 1px;
                                                    text-decoration: initial;
                                                    display: inline-block;
                        ">
                            Bueno
                        </a>
                        <a href="'.$baseUrl.'&rating=4" style="  
                                                    padding: 10px 25px;
                                                    margin: 5px;
                                                    font-size: 18px;
                                                    color: #fff;
                                                    border-radius: 10px;
                                                    background-color: #7BA3CA;
                                                    border-width: 1px;
                                                    text-decoration: initial;
                                                    display: inline-block;
                        ">
                            ¡Excelente!
                        </a> 
                    </div>
                    <br/>
                    <br/>
                    <br/>
                    <br/>
                ';
        
   
        
        $body .= 'Ayúdanos a mejorar nuestro servicio contándonos cómo podríamos haberlo hecho mejor.</br>
                 Si necesitas una boleta o factura, escríbenos a service@washita.cl 
            </td>
        </tr>
        <tr>
            <td style="padding:10px 20px;text-align: center;color:grey">
            <hr style="border-style: dashed;color: #66cdcc;"/>
            
            <p style="font-size:14px">¿Preguntas? ¡Contáctanos!</p>
            <a style="color:#336799" href="tel:+56(22) 405 3056">+56(22) 405 3056</a> | 
            <a style="color:#336799" href="mailto:service@washita.cl" target="_top">service@washita.cl</a>
            
            <hr style="border-style: dashed;color: #66cdcc;"/>
            <span style="font-size:12px; float:center">
            Copyright © 2016 Washita.cl Todos los derechos reservados.
            <br/> Este e-mail ha sido enviado por WASHita.cl<span>
            </td>
        </tr>
        </table>
        </body>

    </html>';

    return $body;
}

?>