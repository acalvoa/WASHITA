<?php
require_once(dirname(__FILE__)."/../../_config.php");
require_once(dirname(__FILE__)."/../_helpers.php");
require_once(dirname(__FILE__)."/../Order.class.php");


// UNCOMMENT ONLY FOR A TEST and open page  /staging/php/email/restore_password_template.php
// echo GetRestorePasswordBody('a-a@a.com', '1234567890');

function GetRestorePasswordBody($email,$temp_code){
    $body = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Washita</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    </head>
    <body style="margin: 0; padding: 0; background:#fff; font-family:lucida sans unicode,lucida grande,sans-serif;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse; background:white; border-style:solid;border-color: #336799;margin:20px auto">
    <tr style="background:#336799">
    <td style="padding:10px 20px;">';
    $body .= '<img src="'.$GLOBALS['site_root'].'/img/freeze/logo.png'.'" />';
    $body .= '<span style="float:right;margin:20px 10px;font-size:20px;color:white">
                    Recuperar contraseña
            </span>
    </td>
    </tr>
    
    <tr>
    <td style="padding:10px 20px;">'.PHP_EOL;
        $body .= '<p style="margin-bottom:0">
                 Haz solicitado recuperar tu contraseña.
                 <br>&nbsp;
                 <br>&nbsp;
                </p>';
        $link = GetTempLinkForPasswordChange($email,$temp_code);
        $body .= '<p>
                    Si no recuerdas tu contraseña, puedes recuperarla en el siguiente link.
                    <br>
                    <a href="'.$link.'">'.$link.'</a>.
                    <br>
                    Este link tiene una vigencia de 1 día.
                </p>
                <br>
                <br>';  
        
        
        $body .= 'Cualquier pregunta, cambio en tu pedido o duda por favor envíanos un correo a service@washita.cl a la brevedad. ¡Pronto estaremos recogiendo tu ropa!
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