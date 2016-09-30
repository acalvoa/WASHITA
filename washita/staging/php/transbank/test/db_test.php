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

// $QUERY = $mysql->INSERT($insert, "palo"); 
$QUERY = $mysql->M_INSERT($in, $ta);
// $QUERY = $mysql->UPDATE($insert,$insert2,"palo"); 
// $QUERY = $mysql->M_UPDATE($in,$in2, $ta);
// $QUERY = $mysql->DELETE($insert, "palo");
// $QUERY = $mysql->M_DELETE($in, $ta);
print_r($QUERY);
?>