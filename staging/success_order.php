 <?php 
include_once(dirname(__FILE__)."/templates/header.general.php");

?>
   <section>
        <div class="container">
            <div class="section-heading section-order">
              <h1>Hemos recibido su pedido Orden N°<?php echo $_GET["order_number"] ?>.</h1>
             <div class="divider"></div>

             <div class="result-text">
                <p>El horario de retiro de tu ropa será el día (DD-MM-AA) y hora: <b><?php echo $_GET["pickuptime"] ?></b>, ante cualquier problema avísanos por email o whatsapp.<p>
                <p>&nbsp;</p>
                <p>Cuando llegue tu pedido a nuestra central, te enviaremos el valor a pagar.<p>  
                <p>&nbsp;</p>
                <p>¡Pronto tu ropa estará limpia y lista para usar!<p>
             </div>
            </div>
        </div>
    </section>
 
 <?php 
include_once(dirname(__FILE__)."/templates/footer.general.php");
 ?>