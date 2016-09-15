
<?php
$LINKS = '
<link rel="stylesheet" href="css/washita.css">
';

include_once(dirname(__FILE__)."/_config.php");
include_once(dirname(__FILE__)."/templates/header.php");
?>

<body>

    <header>
        <?php 
            include_once(dirname(__FILE__)."/templates/navbar.php");
        ?>
        
        <div class="container subscription_container">
            <div class="row">
                <div class="col col-sm-6">
                        <img src="img/freeze/Slides/hand-freeze.png" alt="" class="img-responsive hidden-xxs">
                </div>  
                <div class="col col-sm-6 text-center" id="subscription" >
                            <p class="tp-caption large_white_light ">
                                Monthly subscriptions
                            </p>
                             <div class="section-heading section-heading-subscription">
                                        <div class="divider-wide"></div>
                                            <p>New additional text</p>
                             </div>                                    
                                    <div style="display: inline-block">
                                        <div class="col col-md-4 col-xs-12">
                                            <a class="about-item" href="usersettings.php?subscription=1">
                                                <div class="precio hidden-xxs hidden-xs hidden-sm"> 
                                                    <p>Up to <strong><?php echo $Subscription_1_Kilos ?> Kg</strong></p>
                                                    <img src="img/freeze/uno.png" alt="">
                                                    <h2>$<?php echo $PriceSubscription_1 ?></h2>
                                                </div>
                                                <div class="precio visible-xxs visible-xs visible-sm">
                                                    <img src="img/freeze/uno.png" class="pull-left gap-right">
                                                    <span>
                                                        <p>Up to <strong><?php echo $Subscription_1_Kilos ?>  Kg</strong></p>
                                                        <h2>$<?php echo $PriceSubscription_1 ?></h2>
                                                    </span>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col col-md-4 col-xs-12">
                                            <a class="about-item" href="usersettings.php?subscription=2">
                                                <div class="precio hidden-xxs hidden-xs hidden-sm"> 
                                                    <p>Up to <strong><?php echo $Subscription_2_Kilos ?>  Kg</strong></p>
                                                    <img src="img/freeze/uno.png" alt="">
                                                    <h2>$<?php echo $PriceSubscription_2 ?></h2>
                                                </div>
                                                <div class="precio visible-xxs visible-xs visible-sm">
                                                    <img src="img/freeze/uno.png" class="pull-left gap-right">
                                                    <span>
                                                        <p>Up to <strong><?php echo $Subscription_2_Kilos ?>  Kg</strong></p>
                                                        <h2>$<?php echo $PriceSubscription_2 ?></h2>
                                                    </span>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-md-4 col-xs-12">
                                            <a class="about-item" href="usersettings.php?subscription=3">
                                                <div class="precio hidden-xxs hidden-xs hidden-sm"> 
                                                    <p>Up to <strong><?php echo $Subscription_3_Kilos ?>  Kg</strong></p>
                                                    <img src="img/freeze/uno.png" alt="">
                                                    <h2>$<?php echo $PriceSubscription_3 ?></h2>
                                                </div>
                                                <div class="precio visible-xxs visible-xs visible-sm">
                                                    <img src="img/freeze/uno.png" class="pull-left gap-right">
                                                    <span>
                                                        <p>Up to <strong><?php echo $Subscription_3_Kilos ?>  Kg</strong></p>
                                                        <h2>$<?php echo $PriceSubscription_3 ?></h2>
                                                    </span>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                     <div class="section-heading">
                                                <div class="divider-wide"></div>
                                    </div>  
                                    
                                   
                        </div>
        </div>

<br/>
<br/>
    </header>


<?php
 $SCRIPTS_FOOTER='
 <script>
        $(document).ready(function() {
            appMaster.smoothScroll();
            appMaster.setHeightForMainPageImage();
            appMaster.revsliderAuto();
            appMaster.placeHold();
            appMaster.bindEmailSupport();
            appMaster.scrollToHashInUrl();
        });
    </script>';

include_once(dirname(__FILE__)."/templates/footer.general.php");
?>
