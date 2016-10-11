<?php
include_once(dirname(__FILE__)."/../../_config.php");

/*******************************************************************************
* config                                                                      	*
* Página de configuración del comercio                                          *
* Version: 1.2                                                                 	*
* Date:    2015-05-28                                                         	*
* Author:  flow.cl                                                     			*
********************************************************************************/


/**
 * Ingrese aquí la URL de su página de éxito
 * Enter here the URL of your page successful
 * Ejemplo: http://www.comercio.cl/kpf/exito.php
 * 
 * @var string
 */
//$flow_url_exito = 'http://www.washita.cl/beta/kpf/exito.php';
$flow_url_exito = $site_root.'/php/kpf/exito.php'; 

/**
 * Ingrese aquí la URL de su página de fracaso
 * Enter here the URL of your page failure
 * Ejemplo: http://www.comercio.cl/kpf/fracaso.php
 * 
 * @var string
 */
//$flow_url_fracaso = 'http://www.washita.cl/beta/kpf/fracaso.php';
$flow_url_fracaso = $site_root.'/php/kpf/fracaso.php'; 

/**
 * Ingrese aquí la URL de su página de confirmación
 * Enter here the URL of your confirmation page
 * Ejemplo: http://www.comercio.cl/kpf/confirmacion.php
 * 
 * @var string
 */
//$flow_url_confirmacion = 'http://www.washita.cl/beta/kpf/confirma.php';
$flow_url_confirmacion = $site_root.'/php/kpf/confirma.php'; 

/**
 * Ingrese aquí la página de pago de Flow
 * Enter here the payment page Flow
 * Ejemplo:
 * Test / Sitio de pruebas = http://flow.tuxpan.com/app/kpf/pago.php
 * Production / Sitio de produccion = https://www.flow.cl/app/kpf/pago.php
 * 
 * @var string
 */
 
if(!isset($flow_url_pago)){ 
    $flow_url_pago = 'http://flow.tuxpan.com/app/kpf/pago.php';
}


# Commerce specific config

/**
 * Ingrese aquí la ruta (path) en su sitio donde están las llaves
 * 
 * @var string
 */
 
if(!isset($flow_keys)){ 
    $flow_keys = __DIR__.'/keys';
}

/**
 * Ingrese aquí la ruta (path) en su sitio donde estarán los archivos de logs
 * 
 * @var string
 */
$flow_logPath = __DIR__.'/logs';

/**
 * Ingrese aquí el email con el que está registrado en Flow
 * 
 * @var string
 */
$flow_comercio = 'eduardo.labarca@gmail.com';

/**
 * Ingrese aquí el medio de pago
 * Valores posibles:
 * Solo Webpay = 1 
 * Solo Servipag = 2
 * Todos los medios de pago = 9
 * 
 * @var string
 */
$flow_medioPago = '1';

/**
 * Ingrese aquí el modo de acceso a Webpay
 * Valores posibles:
 * Mostrar pasarela Flow = f 
 * Ingresar directamente a Webpay = d
 * 
 * @var string
 */
$flow_tipo_integracion = 'd';
?>
