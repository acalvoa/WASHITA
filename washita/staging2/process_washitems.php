<?php
require_once(dirname(__FILE__)."/php/WashItem.class.php");

$washItems = WashItem::GetAll();

$json = json_encode($washItems);

// foreach ($washItems as $item) {
//     // Result to JSON
// }

 header('Content-type: application/json');
 echo json_encode($json);

