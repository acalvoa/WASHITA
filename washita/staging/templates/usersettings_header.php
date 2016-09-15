<?php
require_once(dirname(__FILE__)."/../_config.php");
require_once(dirname(__FILE__)."/../php/_helpers.php");

    if(!isset($_SESSION["UserId"])){
        RedirectToLoginPage();
    }
    
     include_once(dirname(__FILE__)."/header.general.php");
     
     function IfCurrentPagePrintActiveClass($url){
         if(DoesCurrentUrlContains($url)){
             return 'class="active"';
         }
     }
 ?>
 
 <!--<div id="wrapper">-->
 <div class="container">

        <!-- Sidebar -->

         <!-- 
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav">
                <li class="sidebar-brand">
                    <a href="#">
                        User settings
                    </a>
                </li>
                <li>
                    <a href="usersettings_profile.php"
                         <?php echo IfCurrentPagePrintActiveClass("usersettings_profile.php") ?>   
                    >Profile</a>
                </li>
                <li>
                    <a href="usersettings_subscription.php"
                         <?php echo IfCurrentPagePrintActiveClass("usersettings_subscription.php") ?>   
                    >Subscription</a>
                </li>
                <li role="separator" class="divider"></li>
                <li>
                    <a href="usersettings_exit.php">Sign out</a>
                </li>
            </ul>
        </div>
        -->
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div > <!--id="page-content-wrapper"-->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        
