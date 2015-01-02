<?php

class PerfectMoney
{
    const BASE_URL = 'https://perfectmoney.is';

    private $accountID;
    private $passPhrase;

    private $error = '';

    public function setAccountID($accountID)
    {
        $this->accountID = $accountID;
    }

    public function getAccountID()
    {
        return $this->accountID;
    }

    public function setPassPhrase($passPhrase)
    {
        $this->passPhrase = $passPhrase;
    }

    public function getPassPhrase()
    {
        return $this->passPhrase;
    }

    public function getError()
    {
        return $this->error;
    }

    /**
        Money transfer
        return: array
        example:
        {
            'PAYMENT_ID': '123',
            'Payer_Account': 'U1911111',
            'PAYMENT_AMOUNT': '0.01',
            'PAYMENT_BATCH_NUM': '1166150',
            'Payee_Account': 'U11232323'
        }
    */
    public function transfer($payerAccount, $payeeAccount, $amount, $memo, $test = true)
    {
        $paramArray['AccountID'] = $this->accountID;
        $paramArray['PassPhrase'] = $this->passPhrase;
        $paramArray['Payer_Account'] = $payerAccount;
        $paramArray['Payee_Account'] = $payeeAccount;
        $paramArray['Amount'] = $amount;
        $paramArray['Memo'] = $memo;
        //$paramArray['PAY_IN'] = 1; // what is this???

        $params = http_build_query($paramArray);

        if ($test === true)
            $url = self::BASE_URL . '/acct/verify.asp?' . $params;
        else
            $url = self::BASE_URL . '/acct/confirm.asp?' . $params;

        return $this->parseAnswer($url);
    }

    /**
        Get account balance
        return: array of account balances
        example:
        {
            'E16123123': '0.00',
            'G15123123': '0.00',
            'U11231233': '190.00'
        }
    */
    public function balance()
    {
        $paramArray['AccountID'] = $this->accountID;
        $paramArray['PassPhrase'] = $this->passPhrase;

        $params = http_build_query($paramArray);
        $url = self::BASE_URL . '/acct/balance.asp?' . $params;

        return $this->parseAnswer($url);
    }

    private function parseAnswer($url)
    {
        $answer = file_get_contents($url);
        if ($answer === false)
        {
            $this->error = 'Error opening url';
            return null;
        }

        if(!preg_match_all("/<input name='(.*)' type='hidden' value='(.*)'>/", $answer, $result, PREG_SET_ORDER))
        {
            $this->error = 'Invalid output';
            return null;
        }

        $dict = array();
        foreach($result as $item)
        {
            $key = $item[1];
            $dict[$key] = $item[2];
        }

        if (array_key_exists('ERROR', $dict))
        {
            $this->error = $dict['ERROR'];
            return null;
        }

        $this->error = '';
        return $dict;
    }
}