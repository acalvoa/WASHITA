<?php
require_once("../MySQLDB.php");
$mysql = new MySQLDB("localhost","root","shadowfax", "washita");
$insert['gato'] = "megan";
$insert['perro'] = "sofia";
$insert2['gato'] = "lol";
$insert2['perro'] = "perrito";

$in = array($insert,$insert,$insert,$insert);
$in2 = array($insert2,$insert2,$insert2,$insert2);
$ta = array("palo","palo","palo","palo");

// CAMA DE PRUEBAS
$de = '{"NAME":"Angelo Calvo Alfaro","ADDRESS":"Colombia 2055, San Ramon","EMAIL":"angelo.calvoa@gmail.com","PHONE":"954081153","WEIGHT":"1.00","IS_IRONING":null,"DISCOUNT_COUPON":"","PRICE_WITH_DISCOUNT":"1400","PRICE_WITHOUT_DISCOUNT":"1400","CREATE_DATE":"2016-10-02 16:34:39","PICKUP_FROM":"2016-10-03 08:00:00","PICKUP_TILL":"2016-10-03 08:00:00","IS_ONLY_IRONING":null,"DROPOFF_FROM":"2016-10-04 08:00:00","DROPOFF_TILL":"2016-10-04 08:00:00","ACTUAL_WEIGHT":null,"ADDITIONAL_PRICE_WITHOUT_DISCOUNT":null,"ADDITIONAL_PRICE_WITH_DISCOUNT":null,"IS_FEEDBACK_REQUESTED":0,"FEEDBACK_CODE":null,"CITY_AREA_ID":4,"WASH_TYPE":0,"COMMENT":"TEST WEBAY","ACTUAL_PRICE_WITH_DISCOUNT":null}';
$insert3 = json_decode($de);
$QUERY = $mysql->INSERT($insert3, "orders"); 
// $QUERY = $mysql->M_INSERT($in, $ta);
// $QUERY = $mysql->UPDATE($insert,$insert2,"palo"); 
// $QUERY = $mysql->M_UPDATE($in,$in2, $ta);
// $QUERY = $mysql->DELETE($insert, "palo");
// $QUERY = $mysql->M_DELETE($in, $ta);
print_r($QUERY);
?>