<?php
/**
 * EgoPay API SOAP Class
 * For more information please visit https://www.egopay.com/developers/api
 */
require_once 'EgoPayApiAgentInterface.php';
/**
 * Class EgoPaySoapApiAgent
 */
class EgoPaySoapApiAgent implements EgoPayApiAgentInterface
{
    /**
     * Current API lib version
     */
    const VERSION = '1.4';
    /**
     * EgoPay API SOAP WSDL url
     */
    const EGOPAY_API_PAYMENT_WSDL_URL = "https://www.egopay.com/api/soap?wsdl";
    /**
     * EgoPay API SOAP url
     */
    const EGOPAY_API_PAYMENT_HEADER_URL = "https://www.egopay.com/soap/";
    /**
     * EgoPay API Auth object
     * @var EgoPayAuth
     */
    private $_oAuth;
    /**
     * SoapClient
     * @var SoapClient
     */
    private $_oClient;

    /**
     * The url of WSDL interface
     * @var string
     */
    private $_wsdlUrl = self::EGOPAY_API_PAYMENT_WSDL_URL;

    /**
     * The URL of SOAP header
     * @var string
     */
    private $_headerUrl = self::EGOPAY_API_PAYMENT_HEADER_URL;


    /**
     * EgoPay API lib constructor
     * @param EgoPayAuth $a Auth credentials
     */
    public function __construct(EgoPayAuth $a)
    {
        $this->_oAuth = $a;
    }

    /**
     * Sets urls of SOAP server
     * @param string $wsdl_url - The url of WSDL interface
     * @param string $header_url - The URL of SOAP header
     */
    public function setUrls($wsdl_url, $header_url)
    {
        $this->_wsdlUrl = $wsdl_url;
        $this->_headerUrl = $header_url;
    }

    /**
     * Returns the attached API's wallet balance. If there is specified currency, then the requesting currency balance
     * is returned.
     * For more information see: https://www.egopay.com/developers/api
     * Since v1.0
     * @param null $sCurrency
     * @return null|object
     */
    public function getBalance($sCurrency = null)
    {
        $this->_setupCredentials();
           
        $oBalance = $this->_oClient->balance();
        $this->_checkError($oBalance);
        
        if ($sCurrency !== null)
            if (isset($oBalance->{$sCurrency}))
                return $oBalance->{$sCurrency};
            else
                return null;
        
        return $oBalance;
    }
    /**
     * Returns attached API's wallet operations list.
     * For more information see: https://www.egopay.com/developers/api
     * Since v1.0
     * @param $iTransactionId
     * @return object
     */
    public function getFindTransaction($iTransactionId)
    {
        $aData = array(
            'transactionId' => $iTransactionId
        );
        $this->_setupCredentials($aData);
        $oTransaction = $this->_oClient->findTransaction($aData);
        $this->_checkError($oTransaction);
        return $oTransaction;
    }
    /**
     * Transfer money to the EgoPay user Account.
     * For more information see: https://www.egopay.com/developers/api
     * Since v1.0
     * @param string $sPayeeEmail
     * @param float $fAmount
     * @param string $sCurrency
     * @param string $sDetails
     * @return object
     */
    public function getTransfer($sPayeeEmail, $fAmount, $sCurrency, $sDetails)
    {
        $aData = array(
            'payeeEmail'    => $sPayeeEmail,
            'amount'        => $fAmount,
            'currency'      => $sCurrency,
            'details'       => $sDetails            
        );
        $this->_setupCredentials($aData);
        $oTransfer = $this->_oClient->transfer($aData);
        $this->_checkError($oTransfer);
        return $oTransfer;
    }
    /**
     * Returns the attached API's wallet operations list.
     * For more information see: https://www.egopay.com/developers/api
     * Since v1.0
     * @param array $aParams
     * @return object
     */
    public function getHistory($aParams = array())
    {        
        $this->_setupCredentials($aParams);
        $oHistory = $this->_oClient->history($aParams);
        $this->_checkError($oHistory);
        return $oHistory;
    }
    /**
     * Returns the attached API's user sold subscriptions
     * For more information see: https://www.egopay.com/developers/api
     * Since v1.3
     * @param array $aParams
     * @return mixed
     */
    public function getSoldSubscriptions($aParams = array())
    {
        $this->_setupCredentials($aParams);
        $oSoldSubscriptions = $this->_oClient->soldSubscriptions($aParams);
        $this->_checkError($oSoldSubscriptions);
        return $oSoldSubscriptions;
    }

    /**
     * Returns the attached API's user purchased subscriptions
     * For more information see: https://www.egopay.com/developers/api
     * Since v1.3
     * @param array $aParams
     * @return mixed
     */
    public function getPurchasedSubscriptions($aParams = array())
    {
        $this->_setupCredentials($aParams);
        $oPurchasedSubscriptions = $this->_oClient->purchasedSubscriptions($aParams);
        $this->_checkError($oPurchasedSubscriptions);
        return $oPurchasedSubscriptions;
    }

    /**
     * Returns provided subscription transactions list
     * For more information see: https://www.egopay.com/developers/api
     * Since v1.3
     * @param array $aParams
     * @return mixed
     */
    public function getSubscriptionTransactions($aParams = array())
    {
        $this->_setupCredentials($aParams);
        $oSubscriptionTransactions = $this->_oClient->subscriptionTransactions($aParams);
        $this->_checkError($oSubscriptionTransactions);
        return $oSubscriptionTransactions;
    }
    /**
     * Cancels provided subscription. Returns canceled subscription information on success.
     * For more information see: https://www.egopay.com/developers/api
     * Since v1.3
     * @param array $aParams
     * @return mixed
     */
    public function getCancelSubscription($aParams = array())
    {
        $this->_setupCredentials($aParams);
        $oCancelSubscription = $this->_oClient->cancelSubscription($aParams);
        $this->_checkError($oCancelSubscription);
        return $oCancelSubscription;
    }
    
    /**
     * Universal function for accessing any operation.
     * For more information see: https://www.egopay.com/developers/api
     * Since v1.3
     * @param string $sOperation - operation name (e.g. 'balance', 'findTransaction', ...)
     * @param array $aParameters - array of parameters for certain operation
     * @return array|mixed|object
     */
    public function get($sOperation, $aParameters = array())
    {
        $this->_setupCredentials($aParameters);
        $oResult = call_user_func_array(array($this->_oClient, $sOperation), $aParameters);       
        $this->_checkError($oResult);
        return $oResult;
    }
    
    /**
     * Gets a prefixed unique identifier based on the current time in microseconds.
     * For more information see http://php.net/manual/en/function.uniqid.php
     * Since v1.0
     * @return string
     */
    private static function _generateId()
    {
        return uniqid();
    }
    /**
     * Setups EgoPay Auth credentials
     * @param array $aData
     */
    private function _setupCredentials($aData=array())
    {                
        $this->_oClient = new SoapClient($this->_wsdlUrl);
        $oHeader = new SoapHeader(
            $this->_headerUrl,
            "authenticate",
            new SoapVar($this->_buildAuthenticationQuery($aData), SOAP_ENC_OBJECT)
        );
        $this->_oClient->__setSoapHeaders(array($oHeader));      
    }
    /**
     * Builds Soap request to EgoPay
     * @param $aData
     * @return array
     */
    private function _buildAuthenticationQuery($aData)
    {
        $aHeader = array(
            'id'            => self::_generateId(),
            'version'       => self::VERSION,
            'account_name'  => $this->_oAuth->getAccountName(),
            'api_id'        => $this->_oAuth->getApiId(),
            'ts'            => time(),
        );
        $aData = array_merge($aHeader, array_filter($aData));
        ksort($aData);
        $aHeader['h'] = hash('sha256',$this->_oAuth->getApiPass() . '|' . implode('|', $aData));        
        return $aHeader;
    }
    /**
     * Checks if there was errors
     * Since v1.0
     * @param $response
     * @throws EgoPayApiException
     */
    private function _checkError($response)
    {
        if ($response === null)
            throw new EgoPayApiException('Invalid Response Format', (int) 0);
        if (isset($response->status) && $response->status == 'ERROR')
            throw new EgoPayApiException($response->error_message, (int) $response->error_code);
    }        
}