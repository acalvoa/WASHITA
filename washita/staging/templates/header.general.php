<?php 

if(!isset($LINKS)){
    $LINKS = "";
}
$LINKS.= '
    <link rel="stylesheet" href="css/general.css">
    <link rel="stylesheet" href="css/datetimepicker.css">
    ';
    
include_once(dirname(__FILE__)."/header.php");
?>

<body>
    <header>
        <?php 
            include_once(dirname(__FILE__)."/navbar.php");
        ?>
    </header>
        