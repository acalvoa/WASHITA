<nav class="navbar navbar-default navbar-fixed-top" role="navigation"> 
            <div class="container">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                        <span class="fa fa-bars fa-lg"></span>
                    </button>
                    <a class="navbar-brand" href="index.php">
                        <img src="img/freeze/logo.png" alt="" class="logo">
                    </a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="index.php#about">Cómo funciona</a>
                        </li>
                        <li><a  href="index.php#screens">Dónde</a>
                        </li>
                        <li><a href="index.php#getApp">Precios</a>
                        </li>
                        <li><a href="process.php">LAVAR AHORA</a>
                        </li>

                        <li><a href="index.php#support">Contacto</a></li>
                        
                        <?php                      
                        // Login part
                        
                        if(isset($_SESSION["UserShortName"]) && !empty($_SESSION["UserShortName"])){

                                echo '
                                    <li class="visible-xs visible-sm"><a href="usersettings.php">Settings</a></li>
                                    <li class="visible-xs visible-sm"><a href="usersettings_exit.php">Sign out</a></li>                                    
                                  ';
                        }
                        else{
                            //echo '<li><a href="login.php">Sign-in</a></li>';
                            
                        }                       
                        ?>
                    </ul>
                    <?php
                    if(isset($_SESSION["UserShortName"]) && !empty($_SESSION["UserShortName"])){
                        echo '
                            <div class="btn-group hidden-xs hidden-sm user-name">
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        '.$_SESSION["UserShortName"].' <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a href="usersettings.php">Settings</a></li>
                                        <li role="separator" class="divider"></li>
                                        <li><a href="usersettings_exit.php">Sign out</a></li>
                                    </ul>
                            </div>
                      ';
                    }
                    ?>
                </div>
                <!-- /.navbar-collapse -->
            </div>
            <!-- /.container-->
        </nav>