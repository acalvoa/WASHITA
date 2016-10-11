<?php
require_once(dirname(__FILE__)."/../_config.php");
require_once(dirname(__FILE__)."/_helpers.php");

require_once(dirname(__FILE__)."/email/SmtpOrderTo.class.php");
require_once(dirname(__FILE__)."/email/PHPMailerAutoload.php");
require_once(dirname(__FILE__)."/email/confirmation_template.php");
require_once(dirname(__FILE__)."/email/additional_payment_template.php");
require_once(dirname(__FILE__)."/email/restore_password_template.php");
require_once(dirname(__FILE__)."/email/order_feedback_template.php");
require_once(dirname(__FILE__)."/email/payment_request_template.php");
require_once(dirname(__FILE__)."/email/zero_payment_confirmation.php");




require_once(dirname(__FILE__)."/Order.class.php");
require_once(dirname(__FILE__)."/CityAndArea.class.php");

include_once(dirname(__FILE__)."/OrderRequiredFeedback.class.php");
include_once(dirname(__FILE__)."/OrderWashItemLine.class.php");
include_once(dirname(__FILE__)."/OrderCustomItemLine.class.php");
include_once(dirname(__FILE__)."/WashType.enum.php");

class MailService{


    public function SendNotification($p_orderNumber){
        
        try {
            $order = Order::GetOrderByNumber($p_orderNumber);
            $washItemLines = OrderWashItemLine::GetCurrentItemsForOrder($p_orderNumber);
            $ironingItemLines = OrderCustomItemLine::GetCurrentItemsForOrder(WashType::OnlyIroning, $p_orderNumber, false);
            
            // MAIL
        
            //Subject
            $subject = 'WASHita Ropa Limpia. Orden N° '.$p_orderNumber;
            // Email body
            $body = GetEmailBody($order,$washItemLines,$ironingItemLines);

            $mail = new PHPMailer();
            //$mail->SMTPDebug = 3;                               // Enable verbose debug output
            
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = $GLOBALS["smtpHost"];  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = $GLOBALS["smtpName"];                 // SMTP username
            $mail->Password = $GLOBALS["smtpPassword"];                           // SMTP password
            $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 465;                                    // TCP port to connect to
            $mail->CharSet = 'UTF-8';
            $mail->From = $GLOBALS["smtpName"];
            $mail->FromName = 'Washita.cl';

            $mail->addAddress($order->Email); 

            $cityAndArea = $order->GetCityAndArea();
            $smtpOrderTo = SmtpOrderTo::GetByCityId($cityAndArea->CityId);
            if(!empty($smtpOrderTo->To)){
                $mail->addBCC($smtpOrderTo->To);
            }
            if(!empty($smtpOrderTo->Bcc)){
                $mail->addBCC($smtpOrderTo->Bcc);
            }
            
            $mail->isHTML(true);                                  // Set email format to HTML

            $mail->Subject = $subject;
            $mail->Body    = $body;
            //$mail->AltBody = $message;
            
            
            
            // Send email
            if(!$mail->send()) {
                //$errors.= $mail->ErrorInfo; //for debug only
                RedirectToErrorPage($order_number, "Sorry. There is internal error in email sending.");
            }
            else{
                //echo('Email is sent. '); //TEST
            }
            
        } catch (Exception $e) {
            RedirectToErrorPage($p_orderNumber,"Internal error Mail_sending");
        }
    }


    public function PaymentRequest($p_orderNumber){
        
        try {
            $order = Order::GetOrderByNumber($p_orderNumber);
            
            // MAIL
        
            //Subject
            $subject = 'WASHita Ropa Limpia. Orden N° '.$p_orderNumber;
            // Email body
            $body = GetPaymentRequestBody($order);

            $mail = new PHPMailer();
            //$mail->SMTPDebug = 3;                               // Enable verbose debug output
            
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = $GLOBALS["smtpHost"];  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = $GLOBALS["smtpName"];                 // SMTP username
            $mail->Password = $GLOBALS["smtpPassword"];                           // SMTP password
            $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 465;                                    // TCP port to connect to
            $mail->CharSet = 'UTF-8';
            $mail->From = $GLOBALS["smtpName"];
            $mail->FromName = 'Washita.cl';

            $mail->addAddress($order->Email); 

            $cityAndArea = $order->GetCityAndArea();
            $smtpOrderTo = SmtpOrderTo::GetByCityId($cityAndArea->CityId);
            if(!empty($smtpOrderTo->To)){
                $mail->addBCC($smtpOrderTo->To);
            }
            if(!empty($smtpOrderTo->Bcc)){
                $mail->addBCC($smtpOrderTo->Bcc);
            }
            
            $mail->isHTML(true);                                  // Set email format to HTML

            $mail->Subject = $subject;
            $mail->Body    = $body;
            
            // Send email
            if(!$mail->send()) {
                //$errors.= $mail->ErrorInfo; //for debug only
                RedirectToErrorPage($order_number, "Sorry. There is internal error in email sending.");
            }
            else{
                //echo('Email is sent. '); //TEST
            }
            
        } catch (Exception $e) {
            RedirectToErrorPage($p_orderNumber,"Internal error Mail_sending");
        }
    }
 

    public function ZeroPaymentConfirmation($p_orderNumber){
        
        try {
            $order = Order::GetOrderByNumber($p_orderNumber);
            
            // MAIL
        
            //Subject
            $subject = 'WASHita Ropa Limpia. Orden N° '.$p_orderNumber;
            // Email body
            $body = GetZeroPaymentConfirmationBody($order);

            $mail = new PHPMailer();
            //$mail->SMTPDebug = 3;                               // Enable verbose debug output
            
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = $GLOBALS["smtpHost"];  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = $GLOBALS["smtpName"];                 // SMTP username
            $mail->Password = $GLOBALS["smtpPassword"];                           // SMTP password
            $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 465;                                    // TCP port to connect to
            $mail->CharSet = 'UTF-8';
            $mail->From = $GLOBALS["smtpName"];
            $mail->FromName = 'Washita.cl';

            $mail->addAddress($order->Email); 

            $cityAndArea = $order->GetCityAndArea();
            $smtpOrderTo = SmtpOrderTo::GetByCityId($cityAndArea->CityId);
            if(!empty($smtpOrderTo->To)){
                $mail->addBCC($smtpOrderTo->To);
            }
            if(!empty($smtpOrderTo->Bcc)){
                $mail->addBCC($smtpOrderTo->Bcc);
            }
            
            $mail->isHTML(true);                                  // Set email format to HTML

            $mail->Subject = $subject;
            $mail->Body    = $body;
            
            // Send email
            if(!$mail->send()) {
                //$errors.= $mail->ErrorInfo; //for debug only
                RedirectToErrorPage($order_number, "Sorry. There is internal error in email sending.");
            }
            else{
                //echo('Email is sent. '); //TEST
            }
            
        } catch (Exception $e) {
            RedirectToErrorPage($p_orderNumber,"Internal error Mail_sending");
        }
    }
 


    public function NotifyAdmin($subject, $body){
        // MAIL
	    //$mail = CreateEmailSender($clientEmail, $subject, $body);
        $mail = new PHPMailer();
        //$mail->SMTPDebug = 3;                               // Enable verbose debug output
        
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = $GLOBALS["smtpHost"];  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = $GLOBALS["smtpName"];                 // SMTP username
        $mail->Password = $GLOBALS["smtpPassword"];                           // SMTP password
        $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 465;                                    // TCP port to connect to
        $mail->CharSet = 'UTF-8';

        $mail->From = $GLOBALS["smtpName"];
        $mail->FromName = 'Washita.cl';
        $mail->addAddress($GLOBALS["smtpAdmin"]);               // Name is optional
        if(!empty($GLOBALS["smtpAdminCc"])){
            $mail->addBCC($GLOBALS["smtpAdminCc"]);
        }
        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = $subject;
        $mail->Body    = $body;
        //$mail->AltBody = $message;
        
        $mail->send();
    }
    
    public function NotifyClientAboutAdditionalPayment(Order $order){
        // MAIL
        $mail = new PHPMailer();
        //$mail->SMTPDebug = 3;                               // Enable verbose debug output
        
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = $GLOBALS["smtpHost"];  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = $GLOBALS["smtpName"];                 // SMTP username
        $mail->Password = $GLOBALS["smtpPassword"];                           // SMTP password
        $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 465;                                    // TCP port to connect to
        $mail->CharSet = 'UTF-8';

        $mail->From = $GLOBALS["smtpName"];
        $mail->FromName = 'Washita.cl';
        
        $mail->addAddress($order->Email);               // Name is optional

        $cityAndArea = $order->GetCityAndArea();
        $smtpOrderTo = SmtpOrderTo::GetByCityId($cityAndArea->CityId);
        if(!empty($smtpOrderTo->To)){
            $mail->addBCC($smtpOrderTo->To);
        }
        if(!empty($smtpOrderTo->Bcc)){
            $mail->addBCC($smtpOrderTo->Bcc);
        }

        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = "Solicitud de pago adicional - N° Orden".$order->OrderNumber;
        $mail->Body    = GetAdditionalPaymentBody($order);
        
        return $mail->send();
    }
    
    public function SendRestorePasswordLink($email, $temp_code){
        // MAIL
        $mail = new PHPMailer();
        //$mail->SMTPDebug = 3;                               // Enable verbose debug output
        
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = $GLOBALS["smtpHost"];  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = $GLOBALS["smtpName"];                 // SMTP username
        $mail->Password = $GLOBALS["smtpPassword"];                           // SMTP password
        $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 465;                                    // TCP port to connect to
        $mail->CharSet = 'UTF-8';

        $mail->From = $GLOBALS["smtpName"];
        $mail->FromName = 'Washita.cl';
        
        $mail->addAddress($email);               // Name is optional
        if(!empty($GLOBALS["smtpAdmin"])){
            $mail->addBCC($GLOBALS["smtpAdmin"]);
        }
        if(!empty($GLOBALS["smtpAdminCc"])){
            $mail->addBCC($GLOBALS["smtpAdminCc"]);
        }

        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = "Recuperar contraseña - Washita.cl";
        $mail->Body    = GetRestorePasswordBody($email,$temp_code);
        
        return $mail->send();
    }
    
    public function SendFeedbackRequest(OrderRequiredFeedback $orderRequiredFeedback){
        
        // MAIL
        $mail = new PHPMailer();
        //$mail->SMTPDebug = 3;                               // Enable verbose debug output
        
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = $GLOBALS["smtpHost"];  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = $GLOBALS["smtpName"];                 // SMTP username
        $mail->Password = $GLOBALS["smtpPassword"];                           // SMTP password
        $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 465;                                    // TCP port to connect to
        $mail->CharSet = 'UTF-8';

        $mail->From = $GLOBALS["smtpName"];
        $mail->FromName = 'Washita.cl';
        
        $mail->addAddress($orderRequiredFeedback->Email);               // Name is optional

        $cityAndArea = CityAndArea::GetCityAndAreaByCityAreaId($orderRequiredFeedback->CityAreaId);

        
        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = "Tu opinión sobre Washita.cl";

        $order = Order::GetOrderByNumber($orderRequiredFeedback->OrderNumber);
        $mail->Body  = GetFeedbackRequestBody($orderRequiredFeedback, $order);
        $result = $mail->send();
        return $result;
    }

    

    

}


    

    
?>