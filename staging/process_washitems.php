<?php
require_once(dirname(__FILE__)."/php/WashItem.class.php");
require_once(dirname(__FILE__)."/php/WashType.enum.php");

$washTypeGet = GetGet('laundry_option');
$washType = WashType::ConvertFromPost($washTypeGet);

$washItems = WashItem::GetAllByType($washType);

$json = json_encode($washItems);

// foreach ($washItems as $item) {
//     // Result to JSON
// }

 header('Content-type: application/json');
 echo json_encode($json);

