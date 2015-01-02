<?php
/**
 * Class EgoPayApiAgentInterface
 */
interface EgoPayApiAgentInterface
{    
    /**
     * Returns the attached API's wallet balance. If there is specified currency, 
     * then the requesting currency balance is returned.
     * @param string $sCurrency optional, (USD or EUR)
     * @return object
     */
    public function getBalance($sCurrency = null);
    /**
     * Get transaction details
     * @param string $iTransactionId transaction reference number
     * @return object
     */
    public function getFindTransaction($sTransactionId);
    /**
     * Transfer money to the EgoPay user Account.
     * @param string $sPayeeEmail payee e-mail address
     * @param float $fAmount payment amount
     * @param string $sCurrency payment currency (USD or EUR)
     * @param string $sDetails payment details
     * @return object
     */
    public function getTransfer($sPayeeEmail, $fAmount, $sCurrency, $sDetails);
    /**
     * Get wallet history
     * @param array $aParams - optional params:
     *      sDateFrom - Y-m-d H:i:s
     *      sDateTo - Y-m-d H:i:s
     *      sCurrency - USD or EUR
     *      iTypeId - payment type id ( for full list of types look at api documentation)
     *      sReferenceNumber - payment reference number
     *      sAccount - user email
     *      iCurrentPage - current page number for pagination
     *      iTotalPages - total pages
     * @return object 
     */
    public function getHistory($aParams = array());
    /**
     * @param array $aParams - optional params:
     *      iCurrentPage - current page number for pagination
     *      iRowsPerPage - rows per page
     * @return object
     */
    public function getSoldSubscriptions($aParams = array());
    /**
     * @param array $aParams - optional params:
     *      iCurrentPage - current page number for pagination
     *      iRowsPerPage - rows per page
     * @return object
     */
    public function getPurchasedSubscriptions($aParams = array());
    /**
     * @param array $aParams - optional params:
     *      sReferenceNumber - Subscription reference number
     *      iCurrentPage - current page number for pagination
     *      iRowsPerPage - rows per page
     * @return object
     */
    public function getSubscriptionTransactions($aParams = array());
    /**
     * @param array $aParams - optional params:
     *      sReferenceNumber - Subscription reference number
     * @return object
     */
    public function getCancelSubscription($aParams = array());
    /**
     * Universal function for accessing any operation.
     * For more information see: https://www.egopay.com/developers/api
     * Since v1.3
     * @param string $sOperation - operation name (e.g. 'balance', 'findTransaction', ...)
     * @param array $aParameters - array of parameters for certain operation
     * @return array|mixed|object
     */
    public function get($sOperation, $aParameters = array());
}