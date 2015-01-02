<?php

class OKPay
{
    const BASE_URL = 'https://api.okpay.com/OkPayAPI?wsdl';

    private $error = '';

    public function Error()
    {
        return $this->error;
    }

    public function verify_notification($post)
    {
        // Read the post from OKPAY and add 'ok_verify'
        $request = 'ok_verify=true';

        foreach ($post as $key => $value)
        {
            $value = urlencode(stripslashes($value));
            $request .= "&$key=$value";
        }

        $fsocket = false;
        $result = false;

        // Try to connect via SSL due sucurity reason
        if ($fp = @fsockopen('ssl://www.okpay.com', 443, $errno, $errstr, 30)) {
            $fsocket = true;
        } elseif ($fp = @fsockopen('www.okpay.com', 80, $errno, $errstr, 30)) {
            $fsocket = true;
        }

        if ($fsocket == true)
        {
            $header = 'POST /ipn-verify.html HTTP/1.0' . "\r\n" .
                'Host: www.okpay.com'."\r\n" .
                'Content-Type: application/x-www-form-urlencoded' . "\r\n" .
                'Content-Length: ' . strlen($request) . "\r\n" .
                'Connection: close' . "\r\n\r\n";

            @fputs($fp, $header . $request);
            $string = '';
            while (!@feof($fp))
            {
                $res = @fgets($fp, 1024);
                $string .= $res;
                // Find verification result in response
                if ($res == 'VERIFIED' || $res == 'INVALID' || $res == 'TEST')
                {
                    $result = $res;
                    break;
                }
            }
            @fclose($fp);
        }

        return $result;
    }

    public function send_money($walletID, $password, $currency, $receiver, $amount, $comment, $isReceiverPayFees, $invoice)
    {
        $this->error = '';

        try
        {
            $client = new SoapClient(self::BASE_URL);
            $params = array(
                'WalletID' => $walletID,
                'SecurityToken' => self::create_security_token($password),
                'Receiver' => $receiver,
                'Currency' => $currency,
                'Amount' => $amount,
                'Comment' => $comment,
                'IsReceiverPaysFees' => $isReceiverPayFees,
                'Invoice' => $invoice,
            );

            $webService = $client->Send_Money($params);
            $wsResult = $webService->Send_MoneyResult;
            return $wsResult;
        }
        catch (Exception $e)
        {
            $this->error = $e;
            return null;
        }
    }

    public function balance($walletID, $password)
    {
        $this->error = '';

        try
        {
            $client = new SoapClient(self::BASE_URL);
            $params = array(
                'WalletID' => $walletID,
                'SecurityToken' => self::create_security_token($password),
            );

            $webService = $client->Wallet_Get_Balance($params);
            $wsResult = $webService->Wallet_Get_BalanceResult;
            return $wsResult;
        }
        catch (Exception $e)
        {
            $this->error = $e;
            return null;
        }
    }

    public function currency_balance($walletID, $password, $currency)
    {
        $this->error = '';

        try
        {
            $client = new SoapClient(self::BASE_URL);
            $params = array(
                'WalletID' => $walletID,
                'SecurityToken' => self::create_security_token($password),
                'Currency' => $currency,
            );

            $webService = $client->Wallet_Get_Currency_Balance($params);
            $wsResult = $webService->Wallet_Get_Currency_BalanceResult;
            return $wsResult->Amount;
        }
        catch (Exception $e)
        {
            $this->error = $e;
            return null;
        }
    }

    private function create_security_token($password)
    {
        $datePart = gmdate("Ymd:H");
        $authString = $password.":".$datePart;
        $secToken = hash('sha256', $authString);
        $secToken = strtoupper($secToken);
        return $secToken;
    }

}