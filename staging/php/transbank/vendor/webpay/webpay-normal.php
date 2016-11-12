<?php

/**
 * @brief      Ecommerce Plugin for chilean Webpay
 * @category   Plugins/SDK
 * @author     Allware Ltda. (http://www.allware.cl)
 * @copyright  2015 Transbank S.A. (http://www.tranbank.cl)
 * @date       Jan 2015
 * @license    GNU LGPL
 * @version    1.0
 * @link       http://transbankdevelopers.cl/
 *
 * This software was created for easy integration of ecommerce
 * portals with Transbank Webpay solution.
 *
 * Required:
 *  - PHP v5.6
 *  - PHP SOAP library
 *  - Ecommerce vX.X
 *
 * Changelog:
 *  - v1.0 Original release
 *
 * See documentation and how to install at link site
 *
 */

class transactionResultOutput {

    var $accountingDate; //string
    var $buyOrder; //string
    var $cardDetail; //cardDetail
    var $detailOutput; //wsTransactionDetailOutput
    var $sessionId; //string
    var $transactionDate; //dateTime
    var $urlRedirection; //string
    var $VCI; //string

}

class cardDetail {

    var $cardNumber; //string
    var $cardExpirationDate; //string

}

class wsTransactionDetailOutput {

    var $authorizationCode; //string
    var $paymentTypeCode; //string
    var $responseCode; //int

}

class wsTransactionDetail {

    var $sharesAmount; //decimal
    var $sharesNumber; //int
    var $amount; //decimal
    var $commerceCode; //string
    var $buyOrder; //string

}

class acknowledgeTransaction {

    var $tokenInput; //string

}

class acknowledgeTransactionResponse {
    
}

class initTransaction {

    var $wsInitTransactionInput; //wsInitTransactionInput

}

class wsInitTransactionInput {

    var $wSTransactionType; //wsTransactionType
    var $commerceId; //string
    var $buyOrder; //string
    var $sessionId; //string
    var $returnURL; //anyURI
    var $finalURL; //anyURI
    var $transactionDetails; //wsTransactionDetail
    var $wPMDetail; //wpmDetailInput

}

class wpmDetailInput {

    var $serviceId; //string
    var $cardHolderId; //string
    var $cardHolderName; //string
    var $cardHolderLastName1; //string
    var $cardHolderLastName2; //string
    var $cardHolderMail; //string
    var $cellPhoneNumber; //string
    var $expirationDate; //dateTime
    var $commerceMail; //string
    var $ufFlag; //boolean

}

class initTransactionResponse {

    var $return; //wsInitTransactionOutput

}

class wsInitTransactionOutput {

    var $token; //string
    var $url; //string

}

/**
 * TRANSACCIÓN DE AUTORIZACIÓN NORMAL:
 * Una transacción de autorización normal (o transacción normal),corresponde a una solicitud de 
 * autorización financiera de un pago con tarjetas de crédito o débito, en donde quién realiza el pago
 * ingresa al sitio del comercio, selecciona productos o servicio, y el ingreso asociado a los datos de la
 * tarjeta de crédito o débito lo realiza en forma segura en Webpay
 * 
 *  Respuestas WebPay: 
 * 
 *  TSY: Autenticación exitosa
 *  TSN: autenticación fallida.
 *  TO : Tiempo máximo excedido para autenticación.
 *  ABO: Autenticación abortada por tarjetahabiente.
 *  U3 : Error interno en la autenticación.
 *  Puede ser vacío si la transacción no se autentico.
 *
 *  Códigos Resultado
 * 
 *  0  Transacción aprobada.
 *  -1 Rechazo de transacción.
 *  -2 Transacción debe reintentarse.
 *  -3 Error en transacción.
 *  -4 Rechazo de transacción.
 *  -5 Rechazo por error de tasa.
 *  -6 Excede cupo máximo mensual.
 *  -7 Excede límite diario por transacción.
 *  -8 Rubro no autorizado.
 */
class WebPayNormal {

    var $soapClient;
    var $config;

    /** Configuración de URL según Ambiente */
    private static $WSDL_URL_NORMAL = array(
        "INTEGRACION"   => "https://webpay3gint.transbank.cl/WSWebpayTransaction/cxf/WSWebpayService?wsdl",
        "CERTIFICACION" => "https://webpay3gint.transbank.cl/WSWebpayTransaction/cxf/WSWebpayService?wsdl",
        "PRODUCCION"    => "https://webpay3g.transbank.cl/WSWebpayTransaction/cxf/WSWebpayService?wsdl",
    );

    /** Descripción de codigos de resultado */
    private static $RESULT_CODES = array(
        "0"  => "Transacción aprobada",
        "-1" => "Rechazo de transacción",
        "-2" => "Transacción debe reintentarse",
        "-3" => "Error en transacción",
        "-4" => "Rechazo de transacción",
        "-5" => "Rechazo por error de tasa",
        "-6" => "Excede cupo máximo mensual",
        "-7" => "Excede límite diario por transacción",
        "-8" => "Rubro no autorizado",
    );
    
    private static $classmap = array('getTransactionResult' => 'getTransactionResult', 'getTransactionResultResponse' => 'getTransactionResultResponse', 'transactionResultOutput' => 'transactionResultOutput', 'cardDetail' => 'cardDetail', 'wsTransactionDetailOutput' => 'wsTransactionDetailOutput', 'wsTransactionDetail' => 'wsTransactionDetail', 'acknowledgeTransaction' => 'acknowledgeTransaction', 'acknowledgeTransactionResponse' => 'acknowledgeTransactionResponse', 'initTransaction' => 'initTransaction', 'wsInitTransactionInput' => 'wsInitTransactionInput', 'wpmDetailInput' => 'wpmDetailInput', 'initTransactionResponse' => 'initTransactionResponse', 'wsInitTransactionOutput' => 'wsInitTransactionOutput');

    function __construct($config) {

        $this->config = $config;
        $privateKey = $this->config->getParam("PRIVATE_KEY");
        $publicCert = $this->config->getParam("PUBLIC_CERT");

        $modo = $this->config->getModo();
        $url = WebPayNormal::$WSDL_URL_NORMAL[$modo];

        $this->soapClient = new WSSecuritySoapClient($url, $privateKey, $publicCert, array(
            "classmap" => self::$classmap,
            "trace" => true,
            "exceptions" => true
        ));
    }

    /**
     * Obtiene resultado desde Webpay
     * */
    function _getTransactionResult($getTransactionResult) {

        $getTransactionResultResponse = $this->soapClient->getTransactionResult($getTransactionResult);
        return $getTransactionResultResponse;
    }

    /**
     * Notifica a Webpay Transacción OK
     * */
    function _acknowledgeTransaction($acknowledgeTransaction) {

        $acknowledgeTransactionResponse = $this->soapClient->acknowledgeTransaction($acknowledgeTransaction);
        return $acknowledgeTransactionResponse;
    }

    /**
     * Inicia transacción con Webpay
     * */
    function _initTransaction($initTransaction) {

        $initTransactionResponse = $this->soapClient->initTransaction($initTransaction);
        return $initTransactionResponse;
    }

    /**
     * Descripción según codigo de resultado Webpay (Ver Codigo Resultados) 
     * */
    function _getReason($code) {
        return WebPayNormal::$RESULT_CODES[$code];
    }

    /**
     * Permite inicializar una transacción en Webpay. 
     * Como respuesta a la invocación se genera un token
     * que representa en forma única una transacción.
     * */
    public function initTransaction($amount, $sessionId, $buyOrder) {
        
        try {

            error_reporting(0);
            $error = array();

            $wsInitTransactionInput = new wsInitTransactionInput();

            $wsInitTransactionInput->wSTransactionType = TR_NORMAL_WS;
            $wsInitTransactionInput->sessionId = $sessionId;
            $wsInitTransactionInput->buyOrder = $buyOrder;
            $wsInitTransactionInput->returnURL = $this->config->getParam("URL_RETURN");
            $wsInitTransactionInput->finalURL = $this->config->getParam("URL_FINAL");

            $wsTransactionDetail = new wsTransactionDetail();
            $wsTransactionDetail->commerceCode = $this->config->getParam("COMMERCE_CODE");
            $wsTransactionDetail->buyOrder = $buyOrder;
            $wsTransactionDetail->amount = $amount;

            $wsInitTransactionInput->transactionDetails = $wsTransactionDetail;

            $initTransactionResponse = $this->_initTransaction(
                    array("wsInitTransactionInput" => $wsInitTransactionInput)
            );

            /** Validación de firma del requerimiento de respuesta enviado por Webpay */
            $xmlResponse = $this->soapClient->__getLastResponse();
            $soapValidation = new SoapValidation($xmlResponse, $this->config->getParam("WEBPAY_CERT"));
            $validationResult = $soapValidation->getValidationResult();

            /** Valida conexion a Webpay. Caso correcto retorna URL y Token */
            if ($validationResult === TRUE) {

                $wsInitTransactionOutput = $initTransactionResponse->return;

                /** Redirecciona a getTransactionResult */
                return array(
                    "url"      => $wsInitTransactionOutput->url,
                    "token_ws" => $wsInitTransactionOutput->token
                );
            } else {

                $error["error"]  = "Error validando conexi&oacute;n a Webpay (Verificar que la informaci&oacute;n del certificado sea correcta)";
                $error["detail"] = "No se pudo completar la conexi&oacute;n con Webpay";
            }
            
        } catch (Exception $e) {

            $error["error"]  = "Error conectando a Webpay (Verificar que la informaci&oacute;n del certificado sea correcta)";
            
            $replaceArray = array('<!--' => '', '-->' => '');
            $error["detail"] = str_replace(array_keys($replaceArray), array_values($replaceArray), $e->getMessage());
        }

        return $error;
    }

    /**
     * Permite obtener el resultado de la transacción una vez que 
     * Webpay ha resuelto su autorización financiera.
     * 
     * Respuesta VCI:
     * 
     * TSY: Autenticación exitosa
     * TSN: autenticación fallida.
     * TO : Tiempo máximo excedido para autenticación
     * ABO: Autenticación abortada por tarjetahabiente
     * U3 : Error interno en la autenticación
     * Puede ser vacío si la transacción no se autentico
     * 
     * */
    public function getTransactionResult($token_ws) {

        $getTransactionResult = new getTransactionResult();
        
        $getTransactionResult->tokenInput = $token_ws;
        $getTransactionResultResponse = $this->_getTransactionResult($getTransactionResult);

        /** Validación de firma del requerimiento de respuesta enviado por Webpay */
        $xmlResponse = $this->soapClient->__getLastResponse();
        $soapValidation = new SoapValidation($xmlResponse, $this->config->getParam("WEBPAY_CERT"));
        $validationResult = $soapValidation->getValidationResult();

        if ($validationResult === TRUE) {
            $result = $getTransactionResultResponse->return;

            /** Indica a Webpay que se ha recibido conforme el resultado de la transacción */
            if ($this->acknowledgeTransaction($token_ws)) {

                /** Validación de transacción aprobada */
                $resultCode = $result->detailOutput->responseCode;
                if (($result->VCI == "TSY" || $result->VCI == "") && $resultCode == 0) {
                    return $result;
                } else {
                    $result->detailOutput->responseDescription = $this->_getReason($resultCode);
                    return $result;
                }
            } else {
                return array("error" => "Error eviando acknowledgeTransaction a Webpay");
            }
        }
        return array("error" => "Error validando transacción en Webpay");
    }

    /**
     * Indica  a Webpay que se ha recibido conforme el resultado de la transacción
     * */
    public function acknowledgeTransaction($token) {

        $acknowledgeTransaction = new acknowledgeTransaction();
        $acknowledgeTransaction->tokenInput = $token;
        $acknowledgeTransactionResponse = $this->_acknowledgeTransaction($acknowledgeTransaction);
        
        $xmlResponse = $this->soapClient->__getLastResponse();
        $soapValidation = new SoapValidation($xmlResponse, $this->config->getParam("WEBPAY_CERT"));
        $validationResult = $soapValidation->getValidationResult();
        return $validationResult === TRUE;
    }

}