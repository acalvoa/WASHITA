 <?php 
include_once(dirname(__FILE__)."/templates/header.general.php");
require_once(dirname(__FILE__)."/php/transbank/OrdersGenerator.php");
$order = new OrderGenerator();
$data_order = $order->ORDER_INFO($_POST["TBK_ORDEN_COMPRA"]);
?>
   <section>
        <div class="container">
            <div class="section-heading section-order">
             <h1>El Pago ha tenido un error</h1>
             <div class="divider"></div>

             <div class="result-text">
                <p>Tu Orden <?php echo $data_order["TBK_TRANSACTION"]['WASHITA_ORDER'] ?> no se ha podido procesar por problemas al verificar el pago.</p>
                <p>Por favor intentalo nuevamente.<p>  
             </div>
            </div>
        </div>
    </section>
 
 <?php 
include_once(dirname(__FILE__)."/templates/footer.general.php");
 ?>