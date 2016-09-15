     <footer>
            <div class="container">
                <a href="user_terms.php">
                    <img src="img/freeze/logo.png" alt="" class="logo">
                    <br/>Términos y Condiciones de Uso
                </a>
                <div class="social">
                    <a href="#"><i class="fa fa-twitter fa-lg"></i></a>
                   
                    <a href="https://www.facebook.com/WASHita-595017300654133/" target="_blank"><i class="fa fa-facebook fa-lg"></i></a>
                </div>
                <div class="rights">
                    <p>Copyright &copy; 2016</p>
                    <p>WASHita <font color="#66cdcc">-EdLabs</font></p>
                </div>
            </div>
        </footer>
        
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/slick.min.js"></script>
    <script src="js/placeholdem.min.js"></script>
    <script src="js/rs-plugin/js/jquery.themepunch.plugins.min.js"></script>
    <script src="js/rs-plugin/js/jquery.themepunch.revolution.min.js"></script>
    <script src="js/waypoints.min.js"></script>
    <script src="js/moment.js"></script>
    <script src="js/datetimepicker.js"></script>
    <script src="js/datetimepicker.pair.js"></script>
    <script src="js/date.js"></script>
    <script src="js/scripts.js"></script>
    <script>
        $(document).ready(function() {
            appMaster.scrollMenu();
        });
    </script>
    <?php
    if(isset($SCRIPTS_FOOTER)){
        echo $SCRIPTS_FOOTER;
    }

    ?>
    
</body>
</html>