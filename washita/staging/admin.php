<?php


require_once(dirname(__FILE__)."/_config.php");
require_once(dirname(__FILE__)."/php/_helpers.php");
require_once(dirname(__FILE__)."/php/City.class.php");
require_once(dirname(__FILE__)."/php/AdminLogin.class.php");

session_start();
require_once(dirname(__FILE__)."/php/AdminLoginService.class.php");


$isSignedOut = false;
    if(isset($_POST["signout"])){
        AdminLoginService::Signout(); 
        $isSignedOut = true;
    }

   if((!$_POST && AdminLoginService::CurrentLogin() == null)
        || $isSignedOut){
        echo 
        '<html>
         <head>
            <title>WASHITA ADMIN</title>
         </head>
         <body>
            <br/>
            <form method="post">
            Password: <input type="password" name="password"></input>
            <input type="submit" autofocus></input>
            </form>
         </body>
         </html>';
         die();
    }


include_once(dirname(__FILE__)."/templates/header.admin.php");

if(isset($_POST["password"])){
    AdminLoginService::Login($_POST["password"]);
}
AdminLoginService::ThrowIfNotLogined();

$adminLogin = AdminLoginService::CurrentLogin();
AdminLoginService::Required($adminLogin->CanViewOrders());

if(isset($_POST["cityId"])){
    $adminLogin->SetCurrentCityById($_POST["cityId"]);
    AdminLoginService::Store($adminLogin);
    RedirectToPageByJs("admin_orders.php");
    die;
}


?>
    <h1 class="text-center">
        ADMIN PANEL
    </h1>

<?php 
global $AdminOrdersCityIdVina,$AdminOrdersCityIdSantiago;
$vina = City::GetCityById($AdminOrdersCityIdVina);
$santiago = City::GetCityById($AdminOrdersCityIdSantiago);

//Signout button
    echo '
    <form method="post">
         <a href="javascript:;" onclick="parentNode.submit();"  
            class="btn btn-default"style="float:right;margin-top:-40px;margin-right:20px;" >
            Signout
            </a>
            <input type="hidden" name="signout" value="true"/>
    </form>
    ';


if($adminLogin->CanChangeCity() || $adminLogin->CurrentCity()->Id == $vina->Id){
    echo '
    <form method="post">
         <a href="javascript:;" onclick="parentNode.submit();"  
            class="btn btn-default" style="float:left;margin:30px;" role="button">
            '.$vina->Name.'
            </a>
            <input type="hidden" name="cityId" value="'.$vina->Id.'"/>
    </form>
    ';
}
    
if($adminLogin->CanChangeCity() || $adminLogin->CurrentCity()->Id == $santiago->Id){
    echo '
     <form method="post">
         <a href="javascript:;" onclick="parentNode.submit();"  
            class="btn btn-default" style="float:left;margin:30px;" role="button">
            '.$santiago->Name.'
            </a>
            <input type="hidden" name="cityId" value="'.$santiago->Id.'"/>
    </form>
    ';
}
?>
        <br/>
        <br/>
        <br/>
<?php         
if($adminLogin->CanEditWashItems()){
    echo '
         <a href="admin_wash_items.php" class="btn btn-default" 
                style="float:left;clear: both;margin:30px;" role="button">
                Wash items
            </a>
    ';
}

if($adminLogin->CanViewInfluencers()){
    echo '
         <a href="admin_influencers.php" class="btn btn-default" 
                style="float:left;clear: both;margin:30px;" role="button">
                Influencers
            </a>
                ';
}
?>
        
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        
        


<?php
include_once(dirname(__FILE__)."/templates/footer.admin.php");

?>

