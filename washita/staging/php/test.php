<?php

// ini_set("log_errors", 1);
// ini_set("error_log", "php-error.log");

//echo $_SERVER['DOCUMENT_ROOT'];

require_once(dirname(__FILE__)."/../_config.php");



include_once(dirname(__FILE__)."/_helpers.php");
// require_once(dirname(__FILE__)."/Price.class.php");
// include_once(dirname(__FILE__)."/MailService.class.php");
// require_once(dirname(__FILE__)."/PickupTime.class.php");
// require_once(dirname(__FILE__)."/hybridauth/WashitaUser.php");
// require_once(dirname(__FILE__)."/NonworkingDays.class.php");

LogHttpHeaders();
error_log("test");



// $date = DateTime::createFromFormat("d-m-Y", "01-11-2016");
// var_dump($date);
//$nonWorkingDates = NonworkingDays::GetNonWokingDaysForFuturePeriod(93);
                                // var_dump($nonWorkingDates);

                                // var_dump(DateTimeImmutable::createFromFormat("Y-m-d", "2016-10-27"));

// $address="a-artur@yandex.ru,aa@2.com";
// if(!empty($address)){
//     foreach(explode(",",$address) as $bcc){
//         echo $bcc;
//     }
// }
// $order = Order::GetOrderByNumber("21039");
// echo $order->GetFullAddress();

// $mailService = new MailService();
//$mailService->SendNotification('21127');

// $order = new OrderRequiredFeedback();
//                 $order->OrderNumber = '21127';
//                 $order->FeedbackCode = 'test';
//                 $order->Email = '2dot718281828@gmail.com';
//                 $order->Name = 'Arturio';
//                 $order->CityAreaId = 2;
//$r = $mailService->SendFeedbackRequest($order);

// var_dump($r);

// if (defined("CRYPT_BLOWFISH") && CRYPT_BLOWFISH) {
//     echo "CRYPT_BLOWFISH is enabled!";
// }else {
// echo "CRYPT_BLOWFISH is not available";
// }   
// exit();
// echo date("Y-m-d H:i:s");




//  $pickupFrom = CreateDateTimeImmutableFromMutable(new DateTime('2016-01-01'));
//  $pickupTill = CreateDateTimeImmutableFromMutable(new DateTime('2016-01-01'));
//  $order->PickupTime = PickupTime::CreatePickupTime($pickupFrom,$pickupTill);

// $pickupFrom = CreateDateTimeImmutableFromMutable(new DateTime("2016-01-01 10:00"));
// $pickupTill = CreateDateTimeImmutableFromMutable(new DateTime("2016-01-01 11:00"));
// $pickupDate = PickupTime::CreatePickupTime($pickupFrom,$pickupTill);
// echo $pickupDate->asText();

//phpinfo();
?>