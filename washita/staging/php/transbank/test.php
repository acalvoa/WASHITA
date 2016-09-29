<?php
require_once("MySQLDB.php");
$mysql = new MySQLDB("localhost","root","shadowfax", "washita");
$insert['gato'] = "megan";
$insert['perro'] = "sofia";

$in = array($insert,$insert,$insert,$insert);
$ta = array("palo","palo","palo","palo");
$QUERY = $mysql->M_INSERT($in, $ta); 
print_r($QUERY);
?>