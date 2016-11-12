 <?php 
include_once(dirname(__FILE__)."/templates/header.general.php");

?>
   <section>
        <div class="container">
            <div class="section-heading section-order">
             <h1>Error al procesar la transacción</h1>
             <div class="divider"></div>

             <div class="result-text">
                <p>Por los extraños misterios de la Web, nos fue imposible procesar el pedido. Lamentamos el inconveniente.</p>  
                <p>Intentalo nuevamente y si no ponte en contacto con nosotros para ayudarte.</p>
                <?php 
                if(isset($_GET['order_number']) && !empty($_GET['order_number'])){
                    echo '<p>Tu Número de Orden: '.$_GET['order_number'].'.</p>';
                }
                ?>
                
                <p><?php echo $_GET["text"] ?></p>

             </div>
            </div>
        </div>
    </section>
 
 <?php 
 include_once(dirname(__FILE__)."/templates/footer.general.php");

 ?>