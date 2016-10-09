<?php
/**
*	@autor: Angelo Calvo A. <angelo.calvoa@gmail.com>
*	@autor: Angelo Calvo A. - Github(acalvoa)
*	@version: 1.0
*	
*	This class implementation has been created for use the OneCLick SOAP Call API provided by transbank.
*/
require_once(dirname(__FILE__).'/vendor/xmlseclibs.php');
require_once(dirname(__FILE__).'/vendor/soap-wsse.php');
require_once(dirname(__FILE__)."/../../_config.php");
/**
* DEFINES THE CONSTANTS
*/
define('PRIVATE_KEY', $GLOBALS['TBK_PRIVATE_KEY_WS']);
define('CERT_FILE', $GLOBALS['TBK_CERT_FILE_WS']);
/**
*	This class is the main class of the OneClick Process.
*/
class OneClickSoap extends SoapClient{
	/** @var */
	function __doRequest($request, $location, $saction, $version) {
		$doc = new DOMDocument('1.0');
		$doc->loadXML($request);
		$objWSSE = new WSSESoap($doc);
		$objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1,array('type' =>
		'private'));
		$objKey->loadKey(PRIVATE_KEY, TRUE);
		$options = array("insertBefore" => TRUE);
		$objWSSE->signSoapDoc($objKey, $options);
		$objWSSE->addIssuerSerial(CERT_FILE);
		$objKey = new XMLSecurityKey(XMLSecurityKey::AES256_CBC);
		$objKey->generateSessionKey();
		$retVal = parent::__doRequest($objWSSE->saveXML(), $location, $saction,
		$version);
		$doc = new DOMDocument();
		$doc->loadXML($retVal);
		return $doc->saveXML();
	}
}
?>