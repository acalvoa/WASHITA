<?php
require_once(dirname(__FILE__)."/_config.php");
require_once(dirname(__FILE__)."/php/_helpers.php");
require_once(dirname(__FILE__).'/php/Order.class.php');
require_once(dirname(__FILE__)."/php/Result.class.php");
require_once(dirname(__FILE__).'/php/OrderFeedback.class.php');
require_once(dirname(__FILE__).'/php/controls/StarRating.control.php');

// GET 
$feedbackCode = $_GET["feedbackCode"];

$orderFeedback = new OrderFeedback();
//Save details
if($_POST){
    $rating_overall = intval(GetPostNoLongerThan('rating_overall',2));
    $rating_easiness = intval(GetPostNoLongerThan('rating_easiness',2));
    $rating_ironing = intval(GetPostNoLongerThan('rating_ironing',2));
    $rating_washing = intval(GetPostNoLongerThan('rating_washing',2));
    $rating_pickup = intval(GetPostNoLongerThan('rating_pickup',2));
    $rating_recommend = intval(GetPostNoLongerThan('rating_recommend',2));
    
    $ratings = array($rating_overall, $rating_easiness, $rating_ironing, $rating_washing, $rating_pickup, $rating_recommend);
    foreach ($ratings as $rate) {
       if(!($rate >= 1 && $rate <= 4)){
            echo 'Error. Rating should be between 1 and 4 stars';
            exit();
        }
    }
    
    
    $orderNumber = GetPostNoLongerThan('orderNumber',30);
    $details = GetPostNoLongerThan('details',2000);


     $orderFeedback = OrderFeedback::GetOrderFeedbackByOrderNumber($orderNumber);
     $result = null;
     if($orderFeedback && $orderFeedback->FeedbackCode == $feedbackCode){
        $orderFeedback->SetRating($rating_overall,$rating_easiness,$rating_ironing,$rating_washing,$rating_recommend,$rating_pickup);
        $orderFeedback->Text=$details;
        $result = OrderFeedback::SaveInDb($orderFeedback);
        if($result->success){
            RedirectToMessagePage("Feedback","<p>The feedback is sent. Thank you!</p>");
        }
     }
      
     if(!$result || !$result->success){
         RedirectToErrorPage($orderNumber,"Internal error order_feedback save rating.");
         exit();
     }
     exit();
}


$rating = $_GET["rating"];
$order = Order::GetOrderNumberByFeedbackCode($feedbackCode);
$id = null;
       
if($_GET && isset($order)){
     if(!($rating >= 1 && $rating <= 4)){
        echo 'Error. Rating should be between 1 and 4 stars';
        exit();
    }
    Order::SetFeedbackIsRequested($order->OrderNumber);
    
    $orderFeedback = OrderFeedback::GetOrderFeedbackByOrderNumber($order->OrderNumber);
    if(!$orderFeedback){
        $orderFeedback = new OrderFeedback();
        $orderFeedback->OrderNumber = $order->OrderNumber;  
        $orderFeedback->FeedbackCode = $feedbackCode;
        $orderFeedback->SetRating($rating,$rating,$rating,$rating,$rating,$rating);
    }
    else{
        $orderFeedback->RatingOverall = $rating;
    }
    
    //Save rating
    $result = OrderFeedback::SaveInDb($orderFeedback);
    if(!$result->success){
        RedirectToErrorPage($orderNumber,"Internal error order_feedback create rating.");
    }
}

  

include_once(dirname(__FILE__)."/templates/header.general.php"); 
?>
<section>
    <div class="container">
         <div class="section-heading section-order">
                <h1>¿Lo hicimos bien?</h1>
                <div class="divider"></div>
                    <?php
                    if($order && $orderFeedback){
                        echo '
                        <form class="form-horizontal" method="post">
                            <div class="form-group">
                                <label for="rating_overall" class="col-sm-4 control-label">
                                    ¿Qué te pareció nuestro servicio?
                                </label>
                                <div class="col-sm-8 text-left star-rating-large">
                                    '.RatingControl('rating_overall', $orderFeedback->RatingOverall).'
                                </div>
                            </div>

                            <p>nos gustaría también saber más detalles,</p>
                            <br/>
                             <div class="form-group">
                                <label for="rating_easiness" class="col-sm-4 control-label">
                                    ¿Qué tan bien planchada quedo la ropa? 
                                </label>
                                <div class="col-sm-8 text-left">
                                    '.RatingControl('rating_easiness', $orderFeedback->RatingEasiness).'
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="rating_ironing" class="col-sm-4 control-label">
                                      ¿Qué tan fácil fue hacer el pedido?
                                </label>
                                <div class="col-sm-8 text-left">
                                    '.RatingControl('rating_ironing',  $orderFeedback->RatingIroning).'
                                </div>
                            </div>';
                            if($order->HasWashing()){
                                echo '
                                 <div class="form-group">
                                    <label for="rating_washing" class="col-sm-4 control-label">
                                       ¿Qué tan bien lavada quedo la ropa?  
                                    </label>
                                    <div class="col-sm-8 text-left">
                                        '.RatingControl('rating_washing', $orderFeedback->RatingWashing).'
                                    </div>
                                </div>';
                            }    
                            
                        echo 
                        '
                            <div class="form-group">
                                <label for="rating_pickup" class="col-sm-4 control-label">
                                     ¿Cómo estuvo la coordinación para recoger y retornar tu ropa?   
                                </label>
                                <div class="col-sm-8 text-left">
                                    '.RatingControl('rating_pickup', $orderFeedback->RatingPickup).'
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="rating_recommend" class="col-sm-4 control-label">
                                     ¿Recomendarías WASHita a tus amigos? 
                                </label>
                                <div class="col-sm-8 text-left">
                                    '.RatingControl('rating_recommend', $orderFeedback->RatingRecommend).'
                                </div>
                            </div>
                            <br/>
                            <div class="form-group">
                                <label for="details" class="col-sm-4 control-label">
                                    ¿Qué cambiarias o mejorarias? 
                                </label>
                                <div class="col-sm-8">
                                <textarea class="form-control" id="details" name="details" maxlength="2000" rows="4" 
                                    placeholder="Máximo 2000 carácteres"></textarea>
                                </div>
                            </div>
                            <input type="hidden" name="orderNumber" value="'.$order->OrderNumber.'" />

                            <div class="form-group text-right col-sm-12">
                                <button type="submit" class="btn btn-default">Enviar</button>
                            </div>
                            </form>
                        ';
                    }
                    else{
                        echo '<p>Feedback code is not valid or the feedback is already counted.</p>';
                    }
                    ?>
                        
                        
        </div>
    </div>
</section> 
 <?php
 
  
   
 
 include_once(dirname(__FILE__)."/templates/footer.general.php");

 ?>