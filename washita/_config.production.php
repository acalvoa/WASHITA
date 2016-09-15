<?php

$site_root = 'http://www.washita.cl';

$DBServer = 'localhost';
$DBName   = 'washita';


$smtpOrderVina = "orders@washita.cl";
$smtpOrderBccVina = 'eduardo@washita.cl'; // additional copy
$smtpOrderSantiago = "sebastian@washita.cl";
$smtpOrderBccSantiago = 'eduardo@washita.cl,francisca@washita.cl'; // additional copy

$smtpAdmin = 'francisca@washita.cl'; // where to send email
$smtpAdminCc = 'eduardo@washita.cl,jorge@washita.cl'; // where to send email

$flow_url_pago = 'https://www.flow.cl/app/kpf/pago.php';
$flow_keys = __DIR__.'/php/kpf/keys.production';
?>