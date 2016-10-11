
<?php
$LINKS = '
<link rel="stylesheet" href="css/washita.css">
';
include_once(dirname(__FILE__)."/templates/header.php");
?>

<body>
    <div class="pre-loader">
        <div class="load-con">
            <img src="img/freeze/logo.png" class="animated fadeInDown" alt="">
            <div class="spinner">
              <div class="bounce1"></div>
              <div class="bounce2"></div>
              <div class="bounce3"></div>
            </div>
        </div>
    </div>

    <header>
        <?php 
            include_once(dirname(__FILE__)."/templates/navbar.php");
        ?>
        <!--RevSlider-->
        <div class="tp-banner-container">
            <div class="tp-banner" >
                <ul>
                    <li data-transition="fade" >
                        <!-- MAIN IMAGE -->
                        <img src="img/transparent.png"  alt="slidebg1"  data-bgfit="cover" data-bgposition="left top" data-bgrepeat="no-repeat">
                        <!-- LAYERS -->
                        <!-- LAYER NR. 1 -->
                        <div class="visible-xs">
                            <div class="tp-caption lfl fadeout"
                                data-x="left"
                                data-y="center"
                                data-hoffset="300"
                                data-voffset="0"
                                data-speed="500"
                                data-start="700"
                                data-easing="Power4.easeOut">
                                <img style="width:440px" src="img/freeze/iphone-freeze-sm.png" alt="">
                            </div>

                            <div class="tp-caption large_white_light sfr" data-x="600" data-y="center" data-hoffset="0" data-voffset="-80" data-speed="500" data-start="1200" data-easing="Power4.easeOut">
                                    Ropa
                            </div>
                            <div class="tp-caption large_white_bold sft" data-x="760" data-y="center" data-hoffset="0" data-voffset="-80" data-speed="500" data-start="1400" data-easing="Power4.easeOut">
                                limpia
                            </div>
                            <div class="tp-caption large_white_light2 sfb" data-x="600" data-y="center" data-hoffset="0" data-voffset="0" data-speed="1000" data-start="1500" data-easing="Power4.easeOut">
                                fácil, rápido y económico
                            </div>
                           
                            <div class="tp-caption sfb" data-x="center" data-y="center" data-hoffset="0" data-voffset="450" data-speed="500" data-start="1200" data-easing="Power4.easeOut">
                                <div class="row">
                                    <div class="col col-xs-12 main-buttons">
                                        <a href="#about" class="btn btn-primary inverse btn-lg">Cómo funciona</a>
                                    </div>
                                    <div class="col col-xs-12 process-button main-buttons">
                                        <a href="process.php" class="btn btn-default btn-lg" style="width:175px; margin-top:10px">LAVAR AHORA</a>
                                    </div>
                                </div>
                          </div>

                         
                      
                            
                      </div>
                        <div class="hidden-xs">
                            <div class="tp-caption lfl fadeout"
                                data-x="left"
                                data-y="top"
                                data-hoffset="-40"
                                data-voffset="100"
                                data-speed="500"
                                data-start="700"
                                data-easing="Power4.easeOut">
                                <img id="hand-freeze" src="img/freeze/iphone-freeze.png" alt="">
                            </div>

                            <div class="tp-caption large_white_light sfr" data-x="550" data-y="center" data-hoffset="0" data-voffset="-80" data-speed="500" data-start="1200" data-easing="Power4.easeOut">
                                    Ropa
                            </div>
                            <div class="tp-caption large_white_bold sft" data-x="710" data-y="center" data-hoffset="0" data-voffset="-80" data-speed="500" data-start="1400" data-easing="Power4.easeOut">
                                limpia
                            </div>
                            <div class="tp-caption large_white_light2 sfb" data-x="550" data-y="center" data-hoffset="0" data-voffset="0" data-speed="1000" data-start="1500" data-easing="Power4.easeOut">
                                fácil, rápido y económico
                            </div>

                            <div class="tp-caption sfb" data-x="550" data-y="center" data-hoffset="0" data-voffset="85" data-speed="1000" data-start="1700" data-easing="Power4.easeOut">
                                <a href="#about" class="btn btn-primary inverse btn-lg">Cómo funciona</a>
                                <a href="process.php" class="btn btn-default btn-lg">LAVAR AHORA</a>                             
                            </div>
                            
                            <div class="main-caption2 tp-caption small_white_light sfb" data-x="550" data-y="center" data-hoffset="0" data-voffset="150" data-speed="1000" data-start="1700" data-easing="Power4.easeOut">
                                        
                                    <div class="divider"></div>
                                    ¡PRONTO! conoce también a <a href="#support"><img style="width: 93x; height: 50px; vertical-align: text-bottom" src="img/Logos/logowashonWHT.png"></a>
                                 </div>
                              <!--  <div class="tp-caption sfb" data-x="center" data-y="center" data-hoffset="350" data-voffset="250" data-speed="1000" data-start="1700" data-easing="Power4.easeOut">
                                    <img style="width: 180px; height: 96px;" src="img/Logos/logowashon.png">
                            </div>-->

                        </div>
                        
                      </li>
                </ul>

            </div>
        </div>


    </header>


    <div class="wrapper">
        <section id="about">
            <div class="container">

                <div class="section-heading scrollpoint sp-effect3">
                    <h1>Fácil y rápido</h1>
                    <div class="divider"></div>
                    <p>3 simples pasos</p>
                </div>

                <div class="row">
                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="about-item scrollpoint sp-effect2">
                            <i class="fa fa-mobile fa-5x"></i>
                            <h3>Haz tu pedido</h3>
                            <p>Ingresa a <a href="process.php">LAVAR AHORA</a>, indica la cantidad de ropa y realiza tu pedido vía web.</p>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-xs-12" >
                        <div class="about-item scrollpoint sp-effect5">
                            <i class="fa fa-truck fa-5x"></i>
                            <h3>Recogemos tu ropa</h3>
                            <p>Elige en qué horario te acomoda e iremos al lugar que nos indiques a recoger tu ropa.</p>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-xs-12" >
                        <div class="about-item scrollpoint sp-effect5">
                            <i class="fa fa-clock-o fa-5x"></i>
                            <h3>Ropa limpia en tus manos</h3>
                            <p>En poco tiempo tendrás tu ropa <font color='#66cdcc'>LAVADA</font>, <font color='#66cdcc'>PLANCHADA</font> y <font color='#66cdcc'>DOBLADA </font>lista para usarla.</p>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <section id="features">
            <div class="container">
                <div class="section-heading scrollpoint sp-effect3">
                    <h1>Ropa Limpia</h1>
                    <div class="divider"></div>
                    <p>Olvídate de perder tiempo lavando o yendo a dejar y buscar tu ropa.
                    </p>
                    <p>¡<b><font color='#66cdcc'>DESCANSA</font></b>!
                    ¡Nosotros nos hacemos cargo de tu ropa sucia!</p>

                </div>
                <div class="row">
                    <div class="col-md-4 scrollpoint sp-effect1 feature-column">
                        <div class="media media-left feature">
                            <a class="pull-right">
                                <i class="fa fa-calendar fa-2x"></i>
                            </a>
                            <div class="media-body">
                                <h3 class="media-heading">¿Cuándo recogen la ropa sucia?</h3>
                                Todos los días, en el recorrido <br/>de la mañana o en el de la tarde.
                            </div>
                        </div>
                        <div class="media media-left feature">
                            <a class="pull-right">
                                <i class="fa fa-clock-o fa-2x"></i>
                            </a>
                            <div class="media-body">
                                <h3 class="media-heading">¿A qué hora son los recorridos?</h3>
                                En la mañana entre las 8am y 10am <br/> y en la tarde entre las 4pm y 6pm.
                            </div>
                        </div>
                        <div class="media media-left feature">
                            <a class="pull-right">
                                <i class="fa fa-map-marker fa-2x"></i>
                            </a>
                            <div class="media-body">
                                <h3 class="media-heading">¿Está disponible en mi comuna?</h3>
                                Por el momento estamos en las siguientes comunas <a href="#screens">ver aquí</a>.
                            </div>
                        </div>
                        <div class="media media-left feature">
                            <a class="pull-right">
                                <i class="fa fa-truck fa-2x"></i>
                            </a>
                            <div class="media-body">
                                <h3 class="media-heading">¿Si no estoy cuando pasen a recoger?</h3>
                                ¡No te preocupes! Puedes dejarla en conserjería y nosotros la recogeremos. De lo contrario,
                                puedes reagendar o pedir una devolución.
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 feature-column" >
                        <img src="img/freeze/iphone-freeze.png" class="img-responsive img-feature scrollpoint sp-effect5"  alt="">
                    </div>
                    <div class="col-md-4 scrollpoint sp-effect2 feature-column">

                        <div class="media feature">
                            <a class="pull-left">
                                <i class="fa fa-balance-scale fa-2x"></i>
                            </a>
                            <div class="media-body">
                                <h3 class="media-heading">¿Cómo calcular el peso de mi ropa?</h3>
                                ¡No es necesario que sea exacto! Sólo ingresa un valor aproximado y al recoger
                                lo pesaremos bien. 
                                Si es menor te devolveremos la diferencia y si es mayor
                                te solicitaremos el pago.
                            </div>
                        </div>
                        <div class="media feature">
                            <a class="pull-left">
                                <i class="fa fa-leaf fa-2x"></i>
                            </a>
                            <div class="media-body">
                                <h3 class="media-heading">¿Cuándo llegará mi ropa limpia?</h3>
                                Tu ropa llegará en menos de 48 horas. <br/>
                                Los retiros en horario PM se procesarán a contar del siguiente día hábil.
                            </div>
                        </div>
                        <div class="media feature">
                            <a class="pull-left">
                                <i class="fa fa-question fa-2x"></i>
                            </a>
                            <div class="media-body">
                                <h3 class="media-heading">¿Tienes más preguntas?</h3>
                                Escríbenos a <a href="#support">contacto</a> o al e-mail hola@washita.cl
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>



        <section id="screens">
            <div class="container">

                <div class="section-heading scrollpoint sp-effect3">
                    <h1>¿Dónde está WASHita? </h1>
                    <div class="divider"></div>
                    <p>Si aún no aparecemos en tu comuna, escríbenos a <a href="#support">contacto</a> </br> y trataremos de habilitar la zona lo antes posible.</p>
                </div>

                <div class="filter scrollpoint sp-effect3">

                   <a href="javascript:void(0)" class="button button-google-maps"
                        data-slick-object-class=".slick-maps-one"
                        data-maps-src="https://www.google.com/maps/d/u/0/embed?mid=zHI8hr9UG0SA.k96z5lwqncDo">Las Condes</a>
                   <a href="javascript:void(0)" class="button button-google-maps"
                        data-slick-object-class=".slick-maps-two"
                        data-maps-src="https://www.google.com/maps/d/u/0/embed?mid=zHI8hr9UG0SA.kKJldWujN20U">Providencia</a>
                    <a href="javascript:void(0)" class="button button-google-maps"
                        data-slick-object-class=".slick-maps-three"
                        data-maps-src="https://mapsengine.google.com/map/embed?mid=zHI8hr9UG0SA.koyM8lEDqYkg">Reñaca</a>                    
                    <a href="javascript:void(0)" class="button button-google-maps"
                        data-slick-object-class=".slick-maps-four"
                        data-maps-src="https://www.google.com/maps/d/embed?mid=zHI8hr9UG0SA.kpsc4gS5_9cY">Plan Viña del Mar</a>
                    <a href="javascript:void(0)" class="button button-google-maps"
                        data-slick-object-class=".slick-maps-five"
                        data-maps-src="https://mapsengine.google.com/map/embed?mid=zHI8hr9UG0SA.kfkuf6qVmXxI">Concón</a>
                </div>
                
                <div class="slider filtering scrollpoint sp-effect5" >
                    <div class="slick-maps-one">
                        <!--
                        <div class="google-maps">
                       <iframe src="https://www.google.com/maps/d/u/0/embed?mid=zHI8hr9UG0SA.k96z5lwqncDo" ></iframe>   
                       </div>
                       -->
                    </div>
                    <div class="slick-maps-two"></div>
                    <div class="slick-maps-three"></div>
                    <div class="slick-maps-four"></div>
                    <div class="slick-maps-five"></div>
                </div>

                
            </div>
        </section>


        <section id="getApp">
            <div class="container">
                <div class="section-heading inverse scrollpoint sp-effect3">
                    <h1>Precios</h1>
                    <div class="divider"></div>
                    <p>Olvídate de la <b>ropa sucia</b> y del <b>planchado</b></p>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <a href="process.php?laundry_option=washing_and_ironing"  class="about-item">
                            <div class="scrollpoint sp-effect2">
                                <div class="precio">  
                                <h3>Lavada y</br>doblada</h3>
                                <img src="img/freeze/uno.png" alt="">
                                <h2>$1.400/Kg</h2>
                                <p>Sobre 6Kg sólo $1.200/Kg</p>
                                <p><i>Lavado por carga. No incluye desmanchado.</i></p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12" >
                        <a href="process.php?laundry_option=only_ironing" class="about-item">
                            <div class="scrollpoint sp-effect1">
                                <div class="precio">
                                <h3>Sólo <br>planchado</h3>
                                <img src="img/freeze/cinco.png" alt="">
                                <h2>$3.000/Kg</h2>
                                <p>Todas las camisas que quieras.</p>
                                <p>&nbsp;</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <!--
                    <div class="col-md-4 col-sm-6 col-xs-12" >
                        <div class="about-item scrollpoint sp-effect3">
                            <div class="precio">
                            <h3>Suscripción Mensual</h3>
                            <i class="fa fa-home fa-6x"></i>
                            <h4>$1.100 por Kilo</h4>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore.</p>
                            </div>
                        </div>
                    </div>
                    -->
                </div>

            </div>
        </section>



        <section id="support" class="doublediagonal">
            <div class="container">
                <div class="section-heading scrollpoint sp-effect3">
                    <h1>Contacto</h1>
                    <div class="divider"></div>
                    <p>¿Cómo te podemos ayudar?</p>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                                <div class="col-md-8 col-sm-8 scrollpoint sp-effect1">
                                    <div id="support_alert_placeholder"></div>
                                    <form id="supportForm" action="contact.php" method="post">
                                        <div class="form-group">
                                            <input type="text" id="name" name="name" maxlength="256" class="form-control" placeholder="Tú nombre">
                                        </div>
                                        <div class="form-group">
                                            <input type="email" id="email" name="email" maxlength="120" class="form-control" placeholder="Tú email">
                                        </div>
                                        <div class="form-group">
                                            <textarea id="message" name="message" maxlength="1000" cols="30" rows="10" class="form-control" placeholder="Mensaje"></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-lg">Enviar</button>
                                    </form>
                                    <br/>

                                </div>

                                <div class="col-md-4 col-sm-4 contact-details scrollpoint sp-effect2">

                                    <div class="media">
                                        <a class="pull-left">
                                            <i class="fa fa-envelope fa-2x"></i>
                                        </a>
                                        <div class="media-body">
                                            <h4 class="media-heading">
                                                <a href="mailto:hola@washita.cl">hola@washita.cl</a>
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="media">
                                        <a class="pull-left">
                                            <i class="fa fa-phone fa-2x"></i>
                                        </a>
                                        <!--<div class="media-body">
                                            <h4 class="media-heading">+56 (22) 405-3056</h4>
                                        </div>-->
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
<?php
 $SCRIPTS_FOOTER='
 <script>
        $(document).ready(function() {
            appMaster.preLoader();
            appMaster.smoothScroll();
            appMaster.reviewsCarousel();
            appMaster.screensCarousel();
            appMaster.animateScript();
            appMaster.setHeightForMainPageImage();
            appMaster.revsliderAuto();
            appMaster.placeHold();
            appMaster.bindEmailSupport();
            appMaster.scrollToHashInUrl();
        });
    </script>
    
    
    <script src="js/jquery.matchHeight.min.js" type="text/javascript"></script>
    <script>
        $(function() {
            $(".feature-column").matchHeight();
        });
    </script>


    ';



include_once(dirname(__FILE__)."/templates/footer.general.php");
?>
