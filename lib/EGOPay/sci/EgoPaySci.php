<?php
/**
 * EgoPay Sci Class
 * @version 1.4
 * @author EgoPay
 * @copyright 2014
 */
class EgoPaySci
{
    /**
     * EgoPay SCI url
     */
    const EGOPAY_PAYMENT_URL = "https://www.egopay.com/payments/pay";

    /**
     * EgoPay Store ID
     * @var string
     */
    protected $_storeId;

    /**
     * EgoPay Store password
     * @var string
     */
    protected $_storePassword;

    /**
     * Set these urls if you don't want use the ones you have in the website
     * User gets redirected after success payment
     * @var string
     */
    protected $_successUrl;

    /**
     * User gets redirected then he goes back without paying
     * @var string
     */
    protected $_failUrl;

    /**
     * Callback script is accessed by this url
     * @var string
     */
    protected $_callbackUrl;

    /**
     * The url of payment form action
     * @var string
     */
    protected $_paymentUrl = self::EGOPAY_PAYMENT_URL;

    /**
    * Constructor
    * @param array $aParams - parameters that initiate API object
    * The available parameters are:
    * Required:
    *   store_id - id of the store
    *   store_password - unique generated password for the store
    * Optional:
    *   success_url - success callback url
    *   fail_url - failed callback url
    */
    public function __construct(array $aParams)
    {
        if(!isset($aParams['store_id'], $aParams['store_password'])) {
            throw new EgoPayException("Missign required params (store_id or store_password)");
        }

        $this->setStoreId($aParams['store_id']);
        $this->setStorePassword($aParams['store_password']);

        if (isset($aParams['success_url'])) {
            $this->setSuccessUrl($aParams['success_url']);
        }
        if (isset($aParams['fail_url'])) {
            $this->setFailUrl($aParams['fail_url']);
        }
        if (isset($aParams['callback_url'])) {
            $this->setCallbackUrl($aParams['callback_url']);
        }
        if (isset($aParams['payment_url'])) {
            $this->setPaymentUrl($aParams['payment_url']);
        }
    }

    /**
     * @param string $url
     */
    public function setSuccessUrl($url)
    {
        $this->_successUrl = $url;
    }

    /**
     * @param string $url
     */
    public function setFailUrl($url)
    {
        $this->_failUrl = $url;
    }

    /**
     * @param string $url
     */
    public function setCallbackUrl($url)
    {
        $this->_callbackUrl = $url;
    }

    /**
     * @param string $id
     */
    public function setStoreId($id)
    {
        $this->_storeId = $id;
    }

    /**
     * @param string $password
     */
    public function setStorePassword($password)
    {
        $this->_storePassword = $password;
    }

    /**
     * @param string $url
     */
    public function setPaymentUrl($url)
    {
        $this->_paymentUrl = $url;
    }

    /**
     * @param array $aData - data that will be sent
     * @return string
     */
    public function getConfirmationUrl($aData)
    {
        $sHash = $this->createHash($aData);
        return $this->_paymentUrl . '/?hash=' . urlencode($sHash);
    }

    /**
     * @param array $aData
     */
    public function sendRequest($aData)
    {
    	$sUrl = $this->getConfirmationUrl($aData);
    	header('Location: '.$sUrl);
    }

    /**
     * Creates encoded data hash
     * @param array $aData
     * @return string
     */
    public function createHash($aData)
    {
        if(!isset($aData['amount'], $aData['currency'])) {
            throw new EgoPayException("Missign required params (amount or currency)");
        }

        if (!empty($this->_successUrl)) {
            $aData['success_url'] = $this->_successUrl;
        }
        if (!empty($this->_failUrl)) {
            $aData['fail_url'] = $this->_failUrl;
        }
        if (!empty($this->_callbackUrl)) {
            $aData['callback_url'] = $this->_callbackUrl;
        }

        return $this->_storeId . $this->encode($aData);
    }

    /**
     * Required for encoding
     * @param string $string
     * @return string
     */
    protected function safe_b64encode($string) {
        $data = base64_encode($string);
        $data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
        return $data;
    }

    /**
     * Encodes given value
     * @param array $value
     * @return string
     */
    protected function encode($data){
        if (!$data) {
            return false;
        }
        $serialized = serialize($data);
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->_storePassword, $serialized, MCRYPT_MODE_ECB, $iv);
        return trim($this->safe_b64encode($crypttext));
    }
}

class EgoPayFormSCI extends EgoPaySci {
    /**
     * @var string
     */
    protected $image_url = "https://www.egopay.com/cdn/frontend/images/ego_88x31_o2.png";
    /**
     *
     * @var string
     */
    protected $button_name = "buy";

    /**
     * Constructor
     * @param array $aParams - parametersthat initiate API object
     * The available parameters are:
     * Required:
     *   store_id - id of the store
     *   store_password - unique generated password for the store
     * Optional:
     *   success_url - success callback url
     *   fail_url - failed callback url
     *   callback_url - failed callback url
     */
    function __construct($aParams)
    {
        $this->_paymentUrl = "https://www.egopay.com/payments/pay/form";

        parent::__construct($aParams);

        if (isset($aParams['image_url'])) {
            $this->setImageUrl($aParams['image_url']);
        }
    }

    /**
     * @param string $sUrl
     */
    public function setImageUrl($sUrl)
    {
        $this->image_url = $sUrl;
    }

    /**
     * Creates confirmation url
     * @param array $aData - data that will be sent
     * @return string - confirmation url
     */
    public function getConfirmationUrl($aData)
    {
        $sQuery = $this->formatQuery($aData);
        return $this->_paymentUrl . '/?' . $sQuery;
    }

    /**
     * @param array $aData
     */
    public function sendRequest($aData)
    {
        $sUrl = $this->getConfirmationUrl($aData);
        header('Location: ' . $sUrl);
    }

    /**
     * Creates http query
     * @param array $aData
     * @return string
     */
    public function formatQuery($aData)
    {
        $aData = $this->prepareData($aData, true);
        return urldecode(http_build_query($aData));
    }

    /**
     * @param array $aData
     * @return string
     */
    public function createVerify($aData)
    {
        $aData = $this->filterForVerify($aData);
        ksort($aData);
        $string = $this->store_password . '|' . implode('|', $aData);
        return hash('sha256', $string);
    }

    /**
     * @param array $aData
     * @return string
     */
    protected function filterForVerify($aData)
    {
        $aRequired = array('amount', 'currency', 'cf_1', 'cf_2', 'cf_3', 'cf_4', 'cf_5', 'cf_6', 'cf_7', 'cf_8');
        $aFiltered = array();
        foreach ($aRequired as $key) {
            if (array_key_exists($key, $aData)) {
                $aFiltered[$key] = $aData[$key];
            }
        }
        return $aFiltered;
    }

    /**
     * @param array $aData
     * @return array
     */
    public function prepareData($aData)
    {
        if (!empty($this->_successUrl))
            $aData['success_url'] = $this->_successUrl;
        if (!empty($this->_failUrl))
            $aData['fail_url'] = $this->_failUrl;
        if (!empty($this->_callbackUrl))
            $aData['callback_url'] = $this->_callbackUrl;

        $aData['store_id'] = $this->store_id;
        unset ($aData['store_password']);

        $aData['verify'] = $this->createVerify($aData);
        return $aData;
    }

    /**
     * @param array $aData
     * @return string
     */
    public function getFormHtml($aData)
    {
        $aData = $this->prepareData($aData);

        $html = sprintf('<form method="post" action="%s">', self::EGOPAY_PAYMENT_URL) . " \n";
        foreach ($aData as $sKey => $sValue) {
            $html .= sprintf('<input class="egopay-%1$s" type="hidden" name="%1$s" value="%2$s"/>',
                    $sKey, $sValue) . " \n";
        }
        $html .= sprintf('<input class="egopay-%1$s" type="image" name="%1$s" src="%2$s" alt="%3$s EgoPay" />',
                    $this->button_name, $this->image_url, ucfirst($this->button_name)) . " \n";
        $html .= '</form>' . " \n";
        return $html;
    }
}

class EgoPaySubscription extends EgoPayFormSCI {
    /**
     * @var string
     */
    protected $image_url = "https://www.egopay.com/cdn/frontend/images/ego_88x31_o2.png";

    /**
     * @var string
     */
    protected $button_name = "subscribe";

    /**
     * @param array $aData
     * @return array
     */
    protected function filterForVerify($aData)
    {
        return $aData;
    }

    /**
     * @param array $aData
     * @return array
     * @throws EgoPayException
     */
    public function prepareData($aData)
    {
        $aRequired = array('amount', 'currency', 'item_name', 'period_unit', 'period_length', 'period_iterations');

        foreach ($aRequired as $required) {
            if (!array_key_exists($required, $aData) || !$aData[$required]) {
                throw new EgoPayException("This param is required - '$required'");
            }
        }

        if (!empty($this->_successUrl))
            $aData['success_url'] = $this->_successUrl;
        if (!empty($this->_failUrl))
            $aData['fail_url'] = $this->_failUrl;
        if (!empty($this->_callbackUrl))
            $aData['callback_url'] = $this->_callbackUrl;

        $aData['store_id'] = $this->store_id;
        unset ($aData['store_password']);

        $aData['verify'] = $this->createVerify($aData);
        return $aData;
    }
}

class EgoPaySciCallback
{
    /**
     * Current SCI version
     */
    const VERSION = '1.4';

    /**
     * SCI payment URL
     */
    const EGOPAY_REQUEST_URL = "https://www.egopay.com/payments/request";

    /**
     * EgoPay Store ID
     * @var string
     */
    protected $_storeId;

    /**
     * EgoPay Store password
     * @var string
     */
    protected $_storePassword;

    /**
     * EgoPay Store checksum key
     * @var string
     */
    protected $_checksumKey = null;

    /**
     * @var bool
     */
    protected $_verifyPeer = true;

    /**
     * Developer referral link
     * @var string
     */
    protected $_refCode = '';

    /**
     * After specified amount of seconds, the request is treated as expired
     * @var int
     */
    protected $_timeOut = 15;

    /**
     * EgoPay url for payment retrieval
     * @var string
     */
    protected $_requestUrl = self::EGOPAY_REQUEST_URL;

   /**
    * Constructor
    * @param array $aParams - parameters that initiate API object
    * The available parameters are:
    * Required:
    *   store_id - id of the store
    *   store_password - unique generated password for the store
    *   r - referral code (last part of affiliate link)
    * Optional:
    *   checksum_key - key identified in your store info
    */
    public function __construct(array $aParams)
    {
        $aRequired = array('store_id','store_password');

        foreach($aRequired as $required)
            if(!array_key_exists($required, $aParams) || !$aParams[$required])
                throw new EgoPayException("This param is required - '$required'");

        if (isset($aParams['r']) && $aParams['r']) {
            if (strpos($aParams['r'], '?') !== false) {
                throw new EgoPayException("Invalid affiliate code");
            }
            $this->_refCode = $aParams['r'];
        }

        $this->setStoreId($aParams['store_id']);
        $this->setStorePassword($aParams['store_password']);

        if (isset($aParams['request_url'])) {
            $this->setRequestUrl($aParams['request_url']);
        }
        if (isset($aParams['checksum_key'])) {
            $this->setChecksumKey($aParams['checksum_key']);
        }
        if (isset($aParams['verify_peer'])) {
            $this->setVerifyPeer($aParams['verify_peer']);
        }
    }

    /**
     * Set EgoPay Store checksum key
     * @param string $key
     */
    public function setChecksumKey($key)
    {
        $this->_checksumKey = $key;
    }

    /**
     * Set EgoPay url for payment retrieval
     * @param string $url
     */
    public function setRequestUrl($url)
    {
        $this->_requestUrl = $url;
    }

    /**
     * @param string $id
     */
    public function setStoreId($id)
    {
        $this->_storeId = $id;
    }

    /**
     * @param string $password
     */
    public function setStorePassword($password)
    {
        $this->_storePassword = $password;
    }

    /**
     * @param bool $verify
     */
    public function setVerifyPeer($verify)
    {
        $this->_verifyPeer = (bool) $verify;
    }

    /**
     * @param array $params - POST data
     * @return array
     * @throws EgoPayException
     */
    protected function prepareRequest(array $params)
    {
        if(!isset($params['product_id']))
            throw new EgoPayException("This param is required - 'product_id'");

        $post = array('product_id' => $params['product_id'],
            'store_id' => $this->_storeId,
            'security_password' => $this->_storePassword,
            'v' => self::VERSION,
            'r' => $this->_refCode,
         );
        return $post;
    }

     /**
     * Sends response to the EgoPay server with data that was sent from EgoPay
     * server
     * @param array $aParams
     * @return array response
     */
    public function getResponse($aParams)
    {
        if (!function_exists('curl_init')) {
            throw new EgoPayException('Curl library not installed');
        }

        $post = $this->prepareRequest($aParams);

    	$ch = curl_init();

    	curl_setopt($ch, CURLOPT_URL, $this->_requestUrl);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_6; en-us) AppleWebKit/525.27.1 (KHTML, like Gecko) Version/3.2.1 Safari/525.27.1");
    	curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($ch, CURLOPT_TIMEOUT, $this->_timeOut);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_verifyPeer);

    	$response_body = curl_exec($ch);
        $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $response_error = curl_errno($ch);
        curl_close($ch);

        return $this->processResponse($response_body, $response_code, $response_error);
    }

    /**
     *
     * @param string $response_body - urlencoded key/value pairs
     * @param int $response_code - HTTP code
     * @param int $response_error - cURL error number
     * @return array - payment information
     * @throws EgoPayException
     */
    protected function processResponse($response_body, $response_code, $response_error)
    {
        if ($response_error || $response_code != 200) {
            throw new EgoPayException('Invalid request to EgoPay. Response code: ' . $response_code);
        }
        $response = array();
        parse_str($response_body, $response);

        $checksum = $response['checksum'];
        unset($response['checksum']);
        if (!empty($this->_checksumKey) && $this->checksum($response) != $checksum) {
            throw new EgoPayException('The response has been tampered with');
        }
        return $response;
    }

    /**
     *
     * @param array $data - received payment information (without checksum field)
     * @return string checksum
     */
    protected function checksum($data)
    {
        return hash('sha256', $this->_checksumKey.'|'.implode('|', $data));
    }
}

/**
 * EgoPay Api Exception class
 */
class EgoPayException extends Exception {

}