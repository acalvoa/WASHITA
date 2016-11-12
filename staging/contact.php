<?php
include_once(dirname(__FILE__)."/_config.php");
include_once(dirname(__FILE__)."/php/MailService.class.php");
include_once(dirname(__FILE__)."/php/_helpers.php");
require_once(dirname(__FILE__)."/php/Result.class.php");


$result = new Result();
$result->success = false;

if($_POST) //Post Data received from order list page.
{
	$name =  GetPostNoLongerThan('name', 256); 
    $email = GetPostNoLongerThan('email', 124); 
    $message = GetPostNoLongerThan('message', 1000); 
    
	// Check request data
	if (empty($name) || $name == "Tú nombre")
	{
        $result->message.= "¡Por favor ingrese su nombre!";
	}
    if (empty($message) || $message == "Mensaje")
	{
		$result->message.= "¡Por favor ingrese un mensaje!";
	}
    if (!IsEmail($email))
	{
        $result->message.= "¡Ingrese un Email válido!";;
	}
    
    if(empty($result->message))
    {
        try {
            $mailService = new MailService();
            
            $body = "<h2>Solicitud de contacto - Washita</h2>";
            $body.= "<p>Nombre: ".$name."</p>";
            $body.= "<p>Email: ".$email."</p>";
            $body.= "<p>Mensaje: ".$message."</p>";
            $mailService->NotifyAdmin("Washita - solicitud de contacto, ".$name, $body);
            
            $result->success = true;
            //RedirectToMessagePage("Support","<p>Email enviado. Nos pondremos en contacto a la brevedad.</p>");
            
        } catch (Exception $e) {
            //RedirectToErrorPage(null,"Internal error Contact page");
        }
    }
 }
 
header('Content-type: application/json');
echo json_encode($result);
?>
