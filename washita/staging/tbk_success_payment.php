 <?php 
include_once(dirname(__FILE__)."/templates/header.general.php");
require_once(dirname(__FILE__)."/php/transbank/OrdersGenerator.php");
print_r($_POST);
$USER = WashitaUser::CurrentUser();
$order = new OrderGenerator($USER->Id);
if(isset($_POST["TBK_ORDEN_COMPRA"])){
    $data_order = $order->ORDER_INFO($_POST["TBK_ORDEN_COMPRA"]);
}
else
{
    $data_order = $order->ORDER_INFO_TOKEN($_POST["token_ws"]);
}

?>
    <section>
        <div class="container">
            <div class="section-heading section-order">
             <h1>Pago realizado con éxito</h1>
             <div class="divider"></div>

             <div class="result-text">
                <p>El horario de retiro de tu ropa será el día (DD-MM-AA) y hora: <b><?php echo $data_order["TBK_PREORDER"]['PICKUP_FROM']; ?></b>, ante cualquier problema avísanos por email o whatsapp.<p>
                <p>Tu número de Orden es <?php echo $data_order["TBK_TRANSACTION"]['WASHITA_ORDER'] ?>.</p>
                <p>En los próximos minutos recibirás un email de confirmación con el detalle de tu pedido.<p>  
                <p>¡Pronto tu ropa estará limpia y lista para usar!<p>
             </div>
            </div>
        </div>
    </section>
 <?php 
include_once(dirname(__FILE__)."/templates/footer.general.php");
 ?>