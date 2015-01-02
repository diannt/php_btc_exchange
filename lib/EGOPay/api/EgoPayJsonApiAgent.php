<?php
/**
 * EgoPay API JSON Class
 * For more information please visit https://www.egopay.com/developers/api
 */
require_once 'EgoPayApiAgentInterface.php';
/**
 * Class EgoPayJsonApiAgent
 */
class EgoPayJsonApiAgent implements EgoPayApiAgentInterface
{
    /**
     * Current API lib version
     */
    const VERSION = '1.4';

    /**
     * EgoPay API request url
     */
    const EGOPAY_API_PAYMENT_URL = "https://www.egopay.com/api/json/";

    /**
     * User agent
     */
    const USER_AGENT = "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_6; en-us) AppleWebKit/525.27.1 (KHTML, like Gecko) Version/3.2.1 Safari/525.27.1";

    /**
     * EgoPay API Auth object
     * @var EgoPayAuth
     */
    private $_oAuth;

    /**
     * Curl request must verify SSL peer or not
     * @var bool
     */
    private $_verifyPeer = true;

    /**
     * @var string - the URL for API requests
     */
    private $_requestUrl = self::EGOPAY_API_PAYMENT_URL;

    /**
     * EgoPay API lib constructor
     * @param EgoPayAuth $a Auth credentials
     */
    public function __construct(EgoPayAuth $a)
    {
        $this->_oAuth = $a;
    }

    /**
     * Curl request must verify SSL peer or not
     * @param bool $verify
     */
    public function setVerifyPeer($verify)
    {
        $this->_verifyPeer = (bool)$verify;
    }

    /**
     * Set the URL for API requests
     * @param string $url
     */
    public function setRequestUrl($url)
    {
        $this->_requestUrl = rtrim($url, '/') . '/';
    }

    /**
     * Returns the attached API's wallet balance. If there is specified currency, then the requesting currency balance
     * is returned.
     * For more information see: https://www.egopay.com/developers/api
     * Since v1.0
     * @param string $sCurrency valid values:Empty,USD,EUR
     * @return mixed|null|object
     * @throws EgoPayApiException
     */
    public function getBalance($sCurrency = null)
    {
        $sResponse = $this->_getResponse('balance');
        $oBalance = $this->_parseResponse($sResponse);
        if ($sCurrency !== null) {
            if (isset($oBalance->{$sCurrency})) {
                return $oBalance->{$sCurrency};
            } else {
                return null;
            }
        }
        return $oBalance;
    }
    /**
     * Returns attached API's wallet operations list.
     * For more information see: https://www.egopay.com/developers/api
     * Since v1.0
     * @param $iTransactionId
     * @return array|mixed|object
     * @throws EgoPayApiException
     */
    public function getFindTransaction($iTransactionId)
    {
        $sResponse = $this->_getResponse('findTransaction',array(
            'transactionId' => $iTransactionId
        ));
        return $this->_parseResponse($sResponse);
    }
    /**
     * Transfer money to the EgoPay user Account.
     * For more information see: https://www.egopay.com/developers/api
     * Since v1.0
     * @param string $sPayeeEmail
     * @param float $fAmount
     * @param string $sCurrency
     * @param string $sDetails
     * @return array|mixed|object
     * @throws EgoPayApiException
     */
    public function getTransfer($sPayeeEmail, $fAmount, $sCurrency, $sDetails)
    {
        $sResponse = $this->_getResponse('transfer',array(
            'payeeEmail'    => $sPayeeEmail,
            'amount'        => $fAmount,
            'currency'      => $sCurrency,
            'details'       => $sDetails
        ));
        return $this->_parseResponse($sResponse);
    }
    /**
     * Returns the attached API's wallet operations list.
     * For more information see: https://www.egopay.com/developers/api
     * Since v1.0
     * @param array $aParams
     * @return array|mixed|object
     * @throws EgoPayApiException
     */
    public function getHistory($aParams = array())
    {
        $sResponse = $this->_getResponse('history', $aParams);
        return $this->_parseResponse($sResponse);
    }
    /**
     * Returns the attached API's user sold subscriptions
     * For more information see: https://www.egopay.com/developers/api
     * Since v1.3
     * @param array $aParams
     * @return array|mixed
     * @throws EgoPayApiException
     */
    public function getSoldSubscriptions($aParams = array())
    {
        $sResponse = $this->_getResponse('soldSubscriptions', $aParams);
        return $this->_parseResponse($sResponse);
    }
    /**
     * Returns the attached API's user purchased subscriptions
     * For more information see: https://www.egopay.com/developers/api
     * Since v1.3
     * @param array $aParams
     * @return array|mixed
     * @throws EgoPayApiException
     */
    public function getPurchasedSubscriptions($aParams = array())
    {
        $sResponse = $this->_getResponse('purchasedSubscriptions', $aParams);
        return $this->_parseResponse($sResponse);
    }
    /**
     * Returns provided subscription transactions list
     * For more information see: https://www.egopay.com/developers/api
     * Since v1.3
     * @param array $aParams
     * @return array|mixed
     * @throws EgoPayApiException
     */
    public function getSubscriptionTransactions($aParams = array())
    {
        $sResponse = $this->_getResponse('subscriptionTransactions', $aParams);
        return $this->_parseResponse($sResponse);
    }
    /**
     * Cancels provided subscription. Returns canceled subscription information on success.
     * For more information see: https://www.egopay.com/developers/api
     * Since v1.3
     * @param array $aParams
     * @return array|mixed
     * @throws EgoPayApiException
     */
    public function getCancelSubscription($aParams = array())
    {
        $sResponse = $this->_getResponse('cancelSubscription', $aParams);
        return $this->_parseResponse($sResponse);
    }

    /**
     * Universal function for accessing any operation.
     * For more information see: https://www.egopay.com/developers/api
     * Since v1.3
     * @param string $sOperation - operation name (e.g. 'balance', 'findTransaction', ...)
     * @param array $aParameters - array of parameters for certain operation
     * @return array|mixed|object
     * @throws EgoPayApiException
     */
    public function get($sOperation, $aParameters = array())
    {
        $sResponse = $this->_getResponse($sOperation, $aParameters);
        return $this->_parseResponse($sResponse);
    }

    /**
     * Gets a prefixed unique identifier based on the current time in microseconds.
     * For more information see http://php.net/manual/en/function.uniqid.php
     * Since v1.0
     * @return string
     */
    private function _generateId()
    {
        return uniqid();
    }

    /**
     * Builds query that is accepted by EgoPay
     * Since v1.4
     * @param $aData
     * @return array
     */
    private function _preparePostData($aData)
    {
        $aData = array_merge(array(
            'id'            => $this->_generateId(),
            'version'       => self::VERSION,
            'account_name'  => $this->_oAuth->getAccountName(),
            'api_id'        => $this->_oAuth->getApiId(),
            'ts'            => time(),
        ), array_filter($aData));

        ksort($aData);
        $aData['h'] = hash('sha256', $this->_oAuth->getApiPass() . '|' . implode('|', $aData));
        return $aData;
    }

    /**
     * Retrieves Response that is sent from EgoPay
     * Since v1.0
     * @param $sAction
     * @param array $aData
     * @return bool|mixed
     * @throws EgoPayApiException
     */
    private function _getResponse($sAction, $aData = array())
    {
        if (!function_exists('curl_init')) {
            throw new EgoPayApiException("Curl library not installed");
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->_requestUrl . $sAction);
        curl_setopt($ch, CURLOPT_USERAGENT, self::USER_AGENT);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_preparePostData($aData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_verifyPeer);

        $response_body = curl_exec($ch);
        $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $response_error = curl_errno($ch);
        $curl_message = curl_error($ch);

        curl_close($ch);

        if ($response_error) {
            throw new EgoPayApiException('Curl error: '.$curl_message);
        } elseif ($response_code != 200) {
            $this->_checkError($response_body);
        }
        return $response_body;
    }

    /**
     * Parses response from EgoPay
     * Since v1.0
     * @param $sResponse
     * @return mixed|null
     */
    private function _parseResponse($response_body)
    {
        return json_decode($response_body);
    }

    /**
     * Checks if there was errors
     * Since v1.4
     * @param $oResponse
     * @throws EgoPayApiException
     */
    private function _checkError($response_body)
    {
        if (empty($response_body)) {
            throw new EgoPayApiException('Empty response', 0);
        }
        $response = $this->_parseResponse($response_body);
        if (is_null($response)) {
            throw new EgoPayApiException('Invalid response format', 0);
        }
        if (isset($response->status) && $response->status == 'ERROR') {
            throw new EgoPayApiException($response->error_message, (int) $response->error_code);
        }
    }
}