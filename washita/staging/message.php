 <?php 
 include_once(dirname(__FILE__)."/templates/header.general.php");
?>
   <section>
        <div class="container">
            <div class="section-heading section-order">
             <h1><?php echo $_GET['header'] ?></h1>
             <div class="divider"></div>

             <div class="result-text">
                <?php echo $_GET["text"] ?>
             </div>
            </div>
        </div>
    </section>
 
 <?php 
    include_once(dirname(__FILE__)."/templates/footer.general.php");
 ?>