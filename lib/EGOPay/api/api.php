<?php
/**
 * EgoPay API SOAP Class
 * For more information please visit https://www.egopay.com/developers/api
 */
//if (!function_exists('__autoload')) {
//    function __autoload($class_name) {
//        include_once($class_name . ".php");
//    }
//}

/**
 * Class EgoPayAuth
 */
class EgoPayAuth
{
    protected $sAccountName;
    protected $sApiId;
    protected $sApiPass;
    
    function __construct($sAccountName, $sApiId, $sApiPass)
    {
        $this->sAccountName = $sAccountName;
        $this->sApiId = $sApiId;
        $this->sApiPass = $sApiPass;
    } 
    
    public function getAccountName()
    {
        return $this->sAccountName;
    }
    
    public function getApiId()
    {
        return $this->sApiId;
    }
    
    public function getApiPass()
    {   
        return $this->sApiPass;
    }
}

/**
 * Class EgoPayApiException
 */
class EgoPayApiException extends Exception {
    
}

/**
 * Class TransactionDetails
 */
class TransactionDetails {
    public  $sId,
            $sDate,
            $fAmount,
            $fFee,
            $sEmail,
            $sType,
            $sDetails,
            $sStatus
        ;
}