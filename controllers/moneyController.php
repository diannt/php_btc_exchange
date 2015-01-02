<?php

define('URL_SUCCESS_PAYMENT', '/usr/mypage?err=0');
define('URL_WRONG_USER_ACTION', '/usr/mypage?err=1');
define('URL_WRONG_MONEY_VALUE', '/usr/mypage?err=2');
define('URL_SERVER_ERROR', '/usr/mypage?err=3');
define('URL_NOTIFICATION_SEND', '/usr/mypage?err=5');
define('URL_ERROR', '/usr/mypage?err=6&message=');
define('URL_WRONG_DATA_INPUT', '/usr/mypage?err=7');

class money extends MainController
{
    const UNUSED_COIN_ADDRESS = 'You cannot generate a new address, without using the old';
    const LIMITS = 'Please check limits for this currency';

    /* Paxum */
    static public function PAX_transaction()
    {
        header('Location: /');
    }


    /* Yandex-money */
    static public function YM_transaction_submit()
    {
        $usr = usr::getCurrentUser(1);
        if($usr == null)
        {
            header('Location: /');
            exit;
        }

        if($usr->getId() != 2)
        {
            header('Location: /');
            exit;
        }

        if(!isset($_GET['code']))
        {
            $to = self::getVar('wallet');
            $UID = self::getVar('UID');

            Session::setSessionVariable('currentUIDTransaction', $UID);

            $scope = "account-info " .
                "operation-history " .
                "operation-details " .
                "payment.to-account(\"" . $to . "\",\"account\") ";
            $authUri = YandexMoney::authorizeUri(YM_OUT_ACCESSTOKEN, YM_OUT_REDIRECTPAGE, $scope);
            header('Location: ' . $authUri);
            exit;
        }

        $UID = Session::getSessionVariable('currentUIDTransaction');
        $code = $_GET['code'];

        $ym = new YandexMoney(YM_OUT_ACCESSTOKEN);

        $receiveTokenResp = $ym->receiveOAuthTokenOut($code, YM_OUT_REDIRECTPAGE , YM_OUT_SECRETTOKEN);
        if(!$receiveTokenResp->isSuccess())
        {
            header('Location: /');
            exit;
        }
        $token = $receiveTokenResp->getAccessToken();

        $purseList = Purse::findBy(array(
            'UID'   =>  $UID,
            'CurId' =>  1,
        ));
        if(empty($purseList))
        {
            header('Location: /admin/ym');
            exit;
        }
        $purse = $purseList[0];

        $prs = new Purse();
        $prs->findById($purse['id']);
        $prs->update(array('out_id' => $token));

        $at = new AtYm();
        $at->findBy(array('UID' => $UID));
        $at->delete();

        header('Location: /admin/ym');
        exit;
    }

    static public function YM_transaction_o()
    {
        $value = $_GET['value'];

        $usr = usr::getCurrentUser(1);
        if($usr == null)
        {
            header('Location: /');
            exit;
        }

        $purseList = Purse::findBy(array(
            'UID'   =>  $usr->getId(),
            'CurId' =>  4,
        ));
        if(empty($purseList))
        {
            header('Location: /usr/mypage?err=1');
            exit;
        }
        $purse = $purseList[0];
        $to = $_GET['yar_path'];

        if($purse['Value'] < $value)
        {
            header('Location: /usr/mypage?err=1');
            exit;
        }

        if($purse['out_id'] == '')
        {
            $await = new AtYm();
            $await->setUID($usr->getId());
            $await->setWallet($to);

            $await->insert();

            header('Location: /usr/mypage?err=4');
            exit;
        }

        $prs = new Purse();

        $newValue = $purse['Value']; //- $value;
        $prs->setId($purse['id']);
        $prs->update(array('value' => $newValue));

        $ym = new YandexMoney(YM_OUT_ACCESSTOKEN);
        $resp = $ym->requestPaymentP2P($purse['out_id'], $to, $value);
        if(!$resp->isSuccess())
        {
            header('Location: /usr/mypage?err=2');
            exit;
        }

        $requestId = $resp->getRequestId();
        $resp = $ym->processPaymentByWallet($purse['out_id'], $requestId);
        if(!$resp->isSuccess())
        {
            header('Location: /usr/mypage?err=3');
            exit;
        }

        header('Location: /usr/mypage?err=0');
        exit;
    }


    static public function YM_transaction()
    {
        if(isset($_GET['error']) && ($_GET['error'] == 'access_denied'))
        {
            header('Location: /usr/mypage?err=1');
            exit;
        }

        $ym = new YandexMoney(YM_ACCESSTOKEN);

        $usr = usr::getCurrentUser(1);
        if($usr == null)
        {
            header('Location: /');
            exit;
        }

        $purseList = Purse::findBy(array(
            'UID'   =>  $usr->getId(),
            'CurId' =>  4, // for Yandex
        ));
        if(empty($purseList))
        {
            header('Location: /usr/mypage?err=1');
            exit;
        }
        $purse = $purseList[0];

        if(!isset($_GET['code']))
        {
            if(empty($purse['Additional_ID']))
            {
                $scope = "account-info " .
                    "operation-history " .
                    "operation-details " .
                    "payment.to-account(\"410012112526562\",\"account\") ";
                $authUri = YandexMoney::authorizeUri(YM_ACCESSTOKEN, YM_REDIRECTPAGE, $scope);
                header('Location: ' . $authUri);
                exit;
            }

            if(!isset($_GET['value']) || !is_numeric($_GET['value']))
            {
                header('Location: /usr/mypage?err=2');
                exit;
            }
            $value = $_GET['value'];
            $token = $purse['Additional_ID'];
        }
        else
        {
            //Save new code
            $code = $_GET['code'];
            $receiveTokenResp = $ym->receiveOAuthToken($code, YM_REDIRECTPAGE, YM_SECRETTOKEN);
            if(!$receiveTokenResp->isSuccess())
            {
                header('Location: /usr/mypage?err=1');
                exit;
            }
            $token = $receiveTokenResp->getAccessToken();
            $prsUpd = new Purse();
            $prsUpd->findById($purse['id']);
            $prsUpd->update(array('Additional_ID' => $token));

            header('Location: /usr/mypage?err=4');
            exit;
        }

        /* ------------ */

        $resp = $ym->requestPaymentP2P($token, "410012112526562", $value);
        if(!$resp->isSuccess())
        {
            header('Location: /usr/mypage?err=2');
            exit;
        }

        $requestId = $resp->getRequestId();
        $resp = $ym->processPaymentByWallet($token, $requestId);
        if(!$resp->isSuccess())
        {
            header('Location: /usr/mypage?err=3');
            exit;
        }

        $prsUpd = new Purse();
        $prsUpd->findById($purse['id']);
        $prsUpd->update(array('Value' => $purse['Value'] + $value));

        header('Location: /usr/mypage?err=0');
    }

    /* Bitcoin */
    static public function BTC_transaction()
    {
        $usr = usr::getCurrentUser(1);
        if($usr == null)
        {
            header('Location: /');
            exit;
        }

        $at_btc = new AtBtc();
        $exist = $at_btc->findBy(array('UID' => $usr->getId(), 'type' => 0, 'done' => 0));
        if ($exist == true) // user already has unused address to input
        {
            $currentAddress = Core::validate($_POST['currentAddress']);
            if ($currentAddress == $at_btc->getAddress()) {
                Core::printErrorJson(URL_ERROR . self::UNUSED_COIN_ADDRESS);
            } else {
                Core::printSuccessJson($at_btc->getAddress());
            }
            return;
        }

        $wallets = self::BTC_MinAndMaxWallets();
        if ($wallets['min'] == null)
        {
            Core::printErrorJson(URL_SERVER_ERROR);
            exit;
        }
        $ourAccount = $wallets['min']['account'];

        $bitcoin = new jsonRPCClient('http://' . BTC_RPC_USER . ':' . BTC_RPC_PASSWORD . '@' . BTC_RPC_HOST . ':' . BTC_RPC_PORT . '/');
        try {
            $address = $bitcoin->getnewaddress($ourAccount);
        } catch (Exception $e) {
            Core::printErrorJson(URL_SERVER_ERROR);
            return;
        }

        $at_btc->setUID($usr->getId());
        $at_btc->setAddress($address);
        $at_btc->setType(0);
        $at_btc->setDone(0);
        $at_btc->insert();

        Core::printSuccessJson($address);
    }

    static public function BTC_processing()
    {
        $transaction_hash = Core::validate($_POST['transaction_hash']);
        $address = Core::validate($_POST['address']);
        $value_in_btc = Core::validate($_POST['amount']);
        $confirmations = Core::validate($_POST['confirmations']);

        if ($_GET['test'] == true) {
            exit;
        }

        $at_btc = new AtBtc();
        $exist = $at_btc->findBy(array('address' => $address, 'type' => 0, 'done' => 0));
        if ($exist == false) {
            return;
        }

        if($confirmations >= BTC_CONFIRMATIONS)
        {
            $currency = new Currency();
            $currency->findBy(array('Name' => 'BTC'));

            $limits = self::transactionLimits($currency->getId(), 'BTC', 0);

            if (($value_in_btc < $limits['min']) ||
                ($limits['max'] != null && $value_in_btc > $limits['max']))
            {
                return;
            }

            $bitcoin = new jsonRPCClient('http://' . BTC_RPC_USER . ':' . BTC_RPC_PASSWORD . '@' . BTC_RPC_HOST . ":" . BTC_RPC_PORT . '/');
            try {
                $account = $bitcoin->getaccount($address);
            } catch (Exception $e) {
                return;
            }

            $wallets = WalletsBtc::findBy(array('account' => $account));
            if (empty($wallets)) {
                return;
            }

            $feeVolume = $value_in_btc * $limits['fee'];
            $feeVolume = Core::round_up($feeVolume, 8);

            $purses = Purse::findBy(array('UID' => $at_btc->getUID(), 'CurId' => $currency->getId()));
            $purse = new Purse();
            $purse->findById($purses[0]['id']);
            $purse->addValue($value_in_btc - $feeVolume);
            $purse->save();

            $wallet = new WalletsBtc();
            $wallet->findById($wallets[0]['id']);
            $profit = $feeVolume * $wallet->getShare();
            $wallet->setProfit($wallet->getProfit() + $profit);
            $wallet->save();

            $at_btc->setDone(1);
            $at_btc->setTransactionHash($transaction_hash);
            $at_btc->setValue($value_in_btc);
            $at_btc->setTimestamp(Core::timestamp_gmp());
            $at_btc->save();
        }
    }

    static public function BTC_transaction_o()
    {
        $usr = usr::getCurrentUser(1);
        if($usr == null)
        {
            header('Location: /');
            exit;
        }

        $address = Core::validate($_POST['address']);
        $amount = Core::validate($_POST['amount']);

        if ($amount == 0 || !Core::isDouble($amount, 8))
        {
            print json_encode(array('location' => URL_WRONG_MONEY_VALUE));
            return;
        }

        if ($address == null)
        {
            print json_encode(array('location' => URL_WRONG_DATA_INPUT));
            return;
        }

        $bitcoin = new jsonRPCClient('http://' . BTC_RPC_USER . ':' . BTC_RPC_PASSWORD . '@' . BTC_RPC_HOST . ':' . BTC_RPC_PORT . '/');
        try {
            $response = $bitcoin->validateaddress($address);
        } catch (Exception $e) {
            print json_encode(array('location' => URL_SERVER_ERROR));
            return;
        }

        if ($response['isvalid'] === false)
        {
            print json_encode(array('location' => URL_WRONG_DATA_INPUT));
            return;
        }

        $currency = new Currency();
        $currency->findBy(array('Name' => 'BTC'));

        $purses = Purse::findBy(array('UID' => $usr->getId(), 'CurId' => $currency->getId()));
        if(empty($purses))
        {
            return;
        }

        $limits = self::transactionLimits($currency->getId(), 'BTC', 1);

        $feeVolume = $amount * $limits['fee'];
        $feeVolume = Core::round_up($feeVolume, 8);

        $purse = new Purse();
        $purse->findById($purses[0]['id']);

        if ($purse->getValue() < $amount + $feeVolume)
        {
            print json_encode(array('location' => URL_WRONG_DATA_INPUT));
            return;
        }

        if ($amount < $limits['min'])
        {
            print json_encode(array('location' => URL_WRONG_DATA_INPUT));
            return;
        }

        if ($limits['max'] != null)
        {
            $transaction_history = new AtBtc();
            $transactions = $transaction_history->findAllByForLastPeriod(array(
                'UID' => $usr->getid(),
                'type' => 1,
                'done' => 1,
            ));

            $totalAmount = 0.0;
            if (isset($transactions)) {
                foreach ($transactions as $transaction) {
                    $totalAmount += $transaction['value'];
                }
            }

            if ($totalAmount + $amount > $limits['max'])
            {
                print json_encode(array('location' => URL_WRONG_DATA_INPUT));
                return;
            }
        }

        $at = new AtBtc();
        $at->setUID($usr->getId());
        $at->setAddress($address);
        $at->setType(1);
        $at->setDone(0);
        $at->setValue($amount);
        $at->insert();

        $success = self::send_output_link('BTC', $at->getId(), $usr);
        if (!$success)
        {
            print json_encode(array('location' => URL_SERVER_ERROR));
            return;
        }

        print json_encode(array('location' => URL_NOTIFICATION_SEND));
    }

    static public function BTC_transaction_o_complete()
    {
        $id = Core::validate($_GET['id']);
        $hash = Core::validate($_GET['hash']);

        $at = new AtBtc();
        if (!$at->findById($id))
        {
            header ('Location: '. URL_WRONG_DATA_INPUT);
            exit;
        }

        if ($hash != self::hash_for_money_output_link($at->getId(), $at->getUID()))
        {
            header ('Location: '. URL_WRONG_DATA_INPUT);
            exit;
        }

        $amount = $at->getValue();

        $wallets = self::BTC_MinAndMaxWallets();
        if (($wallets['max'] == null) ||
            ($wallets['max']['current_balance'] < $amount))
        {
            header ('Location: '. URL_SERVER_ERROR);
            exit;
        }
        $wallet = new WalletsBtc();
        $wallet->findById($wallets['max']['id']);

        $rpcClient = new jsonRPCClient('http://' . BTC_RPC_USER . ':' . BTC_RPC_PASSWORD . '@' . BTC_RPC_HOST . ':' . BTC_RPC_PORT . '/');
        try {
            $transaction_hash = $rpcClient->sendfrom($wallet->getAccount(), $amount, BTC_CONFIRMATIONS);
        } catch (Exception $e) {
            header ('Location: '. URL_SERVER_ERROR);
            return;
        }

        $currency = new Currency();
        $currency->findBy(array('Name' => 'BTC'));

        $limits = self::transactionLimits($currency->getId(), 'BTC', 1);

        $feeVolume = $amount * $limits['fee'];
        $feeVolume = Core::round_up($feeVolume, 8);

        $purses = Purse::findBy(array('UID' => $at->getUID(), 'CurId' => $currency->getId()));
        $purse = new Purse();
        $purse->findById($purses[0]['id']);
        $purse->addValue(-($amount + $feeVolume));
        $purse->save();

        $systemFeeVolume = $amount * $limits['system_fee'];
        $systemFeeVolume = Core::round_up($systemFeeVolume, 8);

        $profit = ($feeVolume - $systemFeeVolume) * $wallet->getShare();
        $wallet->setProfit($wallet->getProfit() + $profit);
        $wallet->save();

        $at->setDone(1);
        $at->setTransactionHash($transaction_hash);
        $at->setTimestamp(Core::timestamp_gmp());
        $at->save();

        header ('Location: '. URL_SUCCESS_PAYMENT);
    }

    static private function BTC_MinAndMaxWallets()
    {
        $wallets = WalletsBtc::getAllWallets();
        if (empty($wallets)) {
            return null;
        }

        $maxBalance = null;
        $keyMax = null;

        $minBalance = null;
        $keyMin = null;

        $bitcoin = new jsonRPCClient('http://' . BTC_RPC_USER . ':' . BTC_RPC_PASSWORD . '@' . BTC_RPC_HOST . ":" . BTC_RPC_PORT . '/');
        foreach ($wallets as $key=>$value)
        {
            try {
                $balance = $bitcoin->getbalance($value['account']);
            } catch (Exception $e) {
                $balance = null;
            }

            if ($balance !== null)
            {
                $wallets[$key]['current_balance'] = $balance;

                if (($minBalance === null) || ($minBalance > $balance)) {
                    $minBalance = $balance;
                    $keyMin = $key;
                }

                if (($maxBalance === null) || ($maxBalance < $balance)) {
                    $maxBalance = $balance;
                    $keyMax = $key;
                }
            }
        }

        $result['min'] = $wallets[$keyMin];
        $result['max'] = $wallets[$keyMax];

        return $result;
    }


    /* Litecoin */
    static public function LTC_transaction()
    {
        $usr = usr::getCurrentUser(1);
        if($usr == null)
        {
            header('Location: /');
            exit;
        }

        $at_ltc = new AtLtc();
        $exist = $at_ltc->findBy(array('UID' => $usr->getId(), 'type' => 0, 'done' => 0));
        if ($exist == true) // user already has unused address to input
        {
            $currentAddress = Core::validate($_POST['currentAddress']);
            if ($currentAddress == $at_ltc->getAddress()){
                Core::printErrorJson(URL_ERROR . self::UNUSED_COIN_ADDRESS);
            } else {
                Core::printSuccessJson($at_ltc->getAddress());
            }
            return;
        }

        $wallets = self::LTC_MinAndMaxWallets();
        if ($wallets['min'] == null)
        {
            Core::printErrorJson(URL_SERVER_ERROR);
            exit;
        }
        $ourAccount = $wallets['min']['account'];

        $litecoin = new jsonRPCClient('http://' . LTC_RPC_USER . ':' . LTC_RPC_PASSWORD . '@' . LTC_RPC_HOST . ':' . LTC_RPC_PORT . '/');
        try {
            $address = $litecoin->getnewaddress($ourAccount);
        } catch (Exception $e) {
            Core::printErrorJson(URL_SERVER_ERROR);
            return;
        }

        $at_ltc->setUID($usr->getId());
        $at_ltc->setAddress($address);
        $at_ltc->setType(0);
        $at_ltc->setDone(0);
        $at_ltc->insert();

        Core::printSuccessJson($address);
    }

    static public function LTC_processing()
    {
        $transaction_hash = Core::validate($_POST['transaction_hash']);
        $address = Core::validate($_POST['address']);
        $value_in_ltc = Core::validate($_POST['amount']);
        $confirmations = Core::validate($_POST['confirmations']);

        if ($_GET['test'] == true) {
            exit;
        }

        $at_ltc = new AtLtc();
        $exist = $at_ltc->findBy(array('address' => $address, 'type' => 0, 'done' => 0));
        if ($exist == false) {
            return;
        }

        if($confirmations >= LTC_CONFIRMATIONS)
        {
            $currency = new Currency();
            $currency->findBy(array('Name' => 'LTC'));

            $limits = self::transactionLimits($currency->getId(), 'LTC', 0);

            if (($value_in_ltc < $limits['min']) ||
                ($limits['max'] != null && $value_in_ltc > $limits['max']))
            {
                return;
            }

            $litecoin = new jsonRPCClient('http://' . LTC_RPC_USER . ':' . LTC_RPC_PASSWORD . '@' . LTC_RPC_HOST . ":" . LTC_RPC_PORT . '/');
            try {
                $account = $litecoin->getaccount($address);
            } catch (Exception $e) {
                return;
            }

            $wallets = WalletsLtc::findBy(array('account' => $account));
            if (empty($wallets)) {
                return;
            }

            $feeVolume = $value_in_ltc * $limits['fee'];
            $feeVolume = Core::round_up($feeVolume, 8);

            $purses = Purse::findBy(array('UID' => $at_ltc->getUID(), 'CurId' => $currency->getId()));
            $purse = new Purse();
            $purse->findById($purses[0]['id']);
            $purse->addValue($value_in_ltc - $feeVolume);
            $purse->save();

            $wallet = new WalletsLtc();
            $wallet->findById($wallets[0]['id']);
            $profit = $feeVolume * $wallet->getShare();
            $wallet->setProfit($wallet->getProfit() + $profit);
            $wallet->save();

            $at_ltc->setDone(1);
            $at_ltc->setTransactionHash($transaction_hash);
            $at_ltc->setValue($value_in_ltc);
            $at_ltc->setTimestamp(Core::timestamp_gmp());
            $at_ltc->save();
        }
    }

    static public function LTC_transaction_o()
    {
        $usr = usr::getCurrentUser(1);
        if($usr == null)
        {
            header('Location: /');
            return;
        }

        $address = Core::validate($_POST['address']);
        $amount = Core::validate($_POST['amount']);

        if ($amount == 0 || !Core::isDouble($amount, 8))
        {
            print json_encode(array('location' => URL_WRONG_MONEY_VALUE));
            return;
        }

        if ($address == null)
        {
            print json_encode(array('location' => URL_WRONG_DATA_INPUT));
            return;
        }

        $litecoin = new jsonRPCClient('http://' . LTC_RPC_USER . ':' . LTC_RPC_PASSWORD . '@' . LTC_RPC_HOST . ':' . LTC_RPC_PORT . '/');
        try {
            $response = $litecoin->validateaddress($address);
        } catch (Exception $e) {
            print json_encode(array('location' => URL_SERVER_ERROR));
            return;
        }

        if ($response['isvalid'] === false)
        {
            print json_encode(array('location' => URL_WRONG_DATA_INPUT));
            return;
        }

        $currency = new Currency();
        $currency->findBy(array('Name' => 'LTC'));

        $purses = Purse::findBy(array('UID' => $usr->getId(), 'CurId' => $currency->getId()));
        if(empty($purses))
        {
            return;
        }

        $limits = self::transactionLimits($currency->getId(), 'LTC', 1);

        $feeVolume = $amount * $limits['fee'];
        $feeVolume = Core::round_up($feeVolume, 8);

        $purse = new Purse();
        $purse->findById($purses[0]['id']);

        if ($purse->getValue() < $amount + $feeVolume)
        {
            Core::printErrorJson(URL_WRONG_DATA_INPUT);
            return;
        }

        if ($amount < $limits['min'])
        {
            print json_encode(array('location' => URL_ERROR . self::LIMITS));
            return;
        }

        if ($limits['max'] != null)
        {
            $transaction_history = new AtLtc();
            $transactions = $transaction_history->findAllByForLastPeriod(array(
                'UID' => $usr->getid(),
                'type' => 1,
                'done' => 1,
            ));

            $totalAmount = 0.0;
            if (isset($transactions)) {
                foreach ($transactions as $transaction) {
                    $totalAmount += $transaction['value'];
                }
            }

            if ($totalAmount + $amount > $limits['max'])
            {
                print json_encode(array('location' => URL_ERROR . self::LIMITS));
                return;
            }
        }

        $at = new AtLtc();
        $at->setUID($usr->getId());
        $at->setAddress($address);
        $at->setType(1);
        $at->setDone(0);
        $at->setValue($amount);
        $at->insert();

        $success = self::send_output_link('LTC', $at->getId(), $usr);
        if (!$success)
        {
            print json_encode(array('location' => URL_SERVER_ERROR));
            return;
        }

        print json_encode(array('location' => URL_NOTIFICATION_SEND));
    }

    static public function LTC_transaction_o_complete()
    {
        $id = Core::validate($_GET['id']);
        $hash = Core::validate($_GET['hash']);

        $at = new AtLtc();
        if (!$at->findById($id))
        {
            header('Location: '.URL_WRONG_DATA_INPUT);
            exit;
        }

        if ($hash != self::hash_for_money_output_link($at->getId(), $at->getUID()))
        {
            header('Location: '.URL_WRONG_DATA_INPUT);
            exit;
        }

        $amount = $at->getValue();

        $wallets = self::LTC_MinAndMaxWallets();
        if (($wallets['max'] == null) ||
            ($wallets['max']['current_balance'] < $amount))
        {
            header('Location: '.URL_SERVER_ERROR);
            exit;
        }
        $wallet = new WalletsLtc();
        $wallet->findById($wallets['max']['id']);

        $rpcClient = new jsonRPCClient('http://' . LTC_RPC_USER . ':' . LTC_RPC_PASSWORD . '@' . LTC_RPC_HOST . ':' . LTC_RPC_PORT . '/');
        try {
            $transaction_hash = $rpcClient->sendfrom($wallet->getAccount(), $amount, LTC_CONFIRMATIONS);
        } catch (Exception $e) {
            header('Location: '.URL_SERVER_ERROR);
            exit;
        }

        $currency = new Currency();
        $currency->findBy(array('Name' => 'LTC'));

        $limits = self::transactionLimits($currency->getId(), 'LTC', 1);

        $feeVolume = $amount * $limits['fee'];
        $feeVolume = Core::round_up($feeVolume, 8);

        $purses = Purse::findBy(array('UID' => $at->getUID(), 'CurId' => $currency->getId()));
        $purse = new Purse();
        $purse->findById($purses[0]['id']);
        $purse->addValue(-($amount + $feeVolume));
        $purse->save();

        $systemFeeVolume = $amount * $limits['system_fee'];
        $systemFeeVolume = Core::round_up($systemFeeVolume, 8);

        $profit = ($feeVolume - $systemFeeVolume) * $wallet->getShare();
        $wallet->setProfit($wallet->getProfit() + $profit);
        $wallet->save();

        $at->setDone(1);
        $at->setTransactionHash($transaction_hash);
        $at->setTimestamp(Core::timestamp_gmp());
        $at->save();

        header ('Location: '. URL_SUCCESS_PAYMENT);
    }

    static private function LTC_MinAndMaxWallets()
    {
        $wallets = WalletsLtc::getAllWallets();
        if (empty($wallets)) {
            return null;
        }

        $maxBalance = null;
        $keyMax = null;

        $minBalance = null;
        $keyMin = null;

        $litecoin = new jsonRPCClient('http://' . LTC_RPC_USER . ':' . LTC_RPC_PASSWORD . '@' . LTC_RPC_HOST . ":" . LTC_RPC_PORT . '/');
        foreach ($wallets as $key=>$value)
        {
            try {
                $balance = $litecoin->getbalance($value['account']);
            } catch (Exception $e) {
                $balance = null;
            }

            if ($balance !== null)
            {
                $wallets[$key]['current_balance'] = $balance;

                if (($minBalance === null) || ($minBalance > $balance)) {
                    $minBalance = $balance;
                    $keyMin = $key;
                }

                if (($maxBalance === null) || ($maxBalance < $balance)) {
                    $maxBalance = $balance;
                    $keyMax = $key;
                }
            }
        }

        $result['min'] = $wallets[$keyMin];
        $result['max'] = $wallets[$keyMax];

        return $result;
    }

    /* Perfect Money */
    static public function PM_transaction()
    {
        $usr = usr::getCurrentUser(1);
        if($usr == null)
        {
            exit;
        }

        $units = Core::validate($_POST['units']);
        $amount = Core::validate($_POST['amount']);

        if ($amount == 0 || !Core::isDouble($amount))
        {
            Core::printErrorJson(URL_WRONG_MONEY_VALUE);
            exit;
        }

        $currency = new Currency();
        if (!$currency->findBy(array('Name' => $units)))
        {
            Core::printErrorJson(URL_SERVER_ERROR);
            exit;
        }

        $limits = self::transactionLimits($currency->getId(), 'PM', 0);

        if (($amount < $limits['min']) ||
            ($limits['max'] != null && $amount > $limits['max']))
        {
            Core::printErrorJson(URL_ERROR . self::LIMITS);
            exit;
        }

        $wallets = self::PM_MinAndMaxWallets($units);
        if ($wallets['min'] == null)
        {
            Core::printErrorJson(URL_SERVER_ERROR);
            exit;
        }
        $ourAccount = $wallets['min']['account'];

        $purseList = Purse::findBy(array('UID' => $usr->getId(), 'CurId' =>  $currency->getId()));
        if(empty($purseList))
        {
            exit;
        }

        $at = new AtPm();
        $result = $at->findBy(array('UID' => $usr->getId(), 'units' => $units, 'type' => 0, 'status' => 0));
        if ($result) {
            $at->delete();
        }

        $at_pm = new AtPm();
        $at_pm->setUID($usr->getId());
        $at_pm->setPayeeAccount($ourAccount);
        $at_pm->setAmount($amount);
        $at_pm->setUnits($units);
        $at_pm->setType(0);
        $at_pm->setTimestamp(Core::timestamp_gmp());
        $at_pm->setStatus(0);
        $at_pm->insert();

        $data['success'] = 1;
        $data['PAYMENT_ID'] = $at_pm->getId();
        $data['PAYEE_ACCOUNT'] = $ourAccount;
        print json_encode($data);
    }

    static public function PM_transaction_status()
    {
        $paymentId = Core::validate(self::getVar('PAYMENT_ID'));
        $payeeAccount = Core::validate(self::getVar('PAYEE_ACCOUNT'));
        $amount = Core::validate(self::getVar('PAYMENT_AMOUNT'));
        $paymentUnits = Core::validate(self::getVar('PAYMENT_UNITS'));
        $paymentBatchNum = Core::validate(self::getVar('PAYMENT_BATCH_NUM'));
        $payerAccount = Core::validate(self::getVar('PAYER_ACCOUNT'));
        $timestampMgt = Core::validate(self::getVar('TIMESTAMPGMT'));
        $V2_HASH = Core::validate(self::getVar('V2_HASH'));

        $wallets = WalletsPm::findBy(array('account' => $payeeAccount));
        if (empty($wallets)) {
            return;
        }
        $alternatePassPhraseHash = strtoupper(md5($wallets[0]['alternate_pass_hash']));

        $string =
            $paymentId.':'.$payeeAccount.':'.
            $amount.':'.$paymentUnits.':'.
            $paymentBatchNum.':'.$payerAccount.':'.
            $alternatePassPhraseHash.':'.$timestampMgt;

        $hash = strtoupper(md5($string));

        if($hash == $V2_HASH)
        {
            $at_pm = new AtPm();
            $exist = $at_pm->findBy(array(
                'id'        => $paymentId,
                'amount'    => $amount,
                'units'     => $paymentUnits,
                'type'      => 0,
                'status'    => 0,
            ));

            if($exist)
            {
                $currency = new Currency();
                $currency->findBy(array('Name' => $paymentUnits));

                $limits = self::transactionLimits($currency->getId(), 'PM', 0);

                if (($amount < $limits['min']) ||
                    ($limits['max'] != null && $amount > $limits['max']))
                {
                    return;
                }

                $feeVolume = $amount * $limits['fee'];
                $feeVolume = Core::round_up($feeVolume, 2);

                $purseList = Purse::findBy(array('UID' => $at_pm->getUID(), 'CurId' =>  $currency->getId()));
                $purse = new Purse();
                $purse->findById($purseList[0]['id']);
                $purse->addValue($amount - $feeVolume);
                $purse->save();

                $wallet = new WalletsPm();
                $wallet->findById($wallets[0]['id']);
                $profit = $feeVolume * $wallet->getShare();
                $wallet->setProfit($wallet->getProfit() + $profit);
                $wallet->save();

                $at_pm->update(array(
                    'payer_account'   => $payerAccount,
                    'batch_num' => $paymentBatchNum,
                    'status'    => 1,
                ));
            }
        }
    }

    static public function PM_transaction_success()
    {
        header('Location: '.URL_SUCCESS_PAYMENT);
    }

    static public function PM_transaction_cancel()
    {
        $usr = usr::getCurrentUser(1);
        if($usr == null) {
            exit;
        }

        $paymentId = Core::validate(self::getVar('PAYMENT_ID'));
        $payeeAccount = Core::validate(self::getVar('PAYEE_ACCOUNT'));
        $paymentAmount = Core::validate(self::getVar('PAYMENT_AMOUNT'));
        $paymentUnits = Core::validate(self::getVar('PAYMENT_UNITS'));

        $at_pm = new AtPm();
        $exist = $at_pm->findBy(array(
            'id'            => $paymentId,
            'UID'           => $usr->getId(),
            'payee_account'  => $payeeAccount,
            'amount'        => $paymentAmount,
            'units'         => $paymentUnits,
            'type'          => 0,
            'status'        => 0,
        ));

        if ($exist) {
            $at_pm->delete();
        }

        header('Location: /usr/mypage');
    }

    static public function PM_transaction_o()
    {
        $usr = usr::getCurrentUser(1);
        if($usr == null)
        {
            header('Location: /');
            exit;
        }

        $amount = Core::validate($_POST['amount']);
        $userAccount = Core::validate($_POST['account']);
        $units = Core::validate($_POST['currency']);

        if ($amount == 0 || !Core::isDouble($amount, 2))
        {
            print json_encode(array('location' => URL_WRONG_MONEY_VALUE));
            exit;
        }

        $currency = new Currency();
        if (!$currency->findBy(array('Name' => $units)))
        {
            print json_encode(array('location' => URL_SERVER_ERROR));
            exit;
        }

        $curName = $currency->getName();
        if (!preg_match("/^[$curName[0]][0-9]{7}$/", $userAccount))
        {
            print json_encode(array('location' => URL_WRONG_DATA_INPUT));
            exit;
        }

        $purseList = Purse::findBy(array('UID' => $usr->getId(), 'CurId' => $currency->getId()));
        if(empty($purseList))
        {
            exit;
        }

        $limits = self::transactionLimits($currency->getId(), 'PM', 1);
        $feeVolume = $amount * $limits['fee'];
        $feeVolume = Core::round_up($feeVolume, 2);

        $purse = new Purse();
        $purse->findById($purseList[0]['id']);

        if ($purse->getValue() < $amount + $feeVolume)
        {
            print json_encode(array('location' => URL_WRONG_MONEY_VALUE));
            exit;
        }

        if ($amount < $limits['min'])
        {
            print json_encode(array('location' => URL_ERROR . self::LIMITS));
            return;
        }

        if ($limits['max'] != null)
        {
            $transaction_history = new AtPm();
            $transactions = $transaction_history->findAllByForLastPeriod(array(
                'UID' => $usr->getid(),
                'type' => 1,
                'status' => 1,
            ));

            $totalAmount = 0.0;
            if (isset($transactions)) {
                foreach ($transactions as $transaction) {
                    $totalAmount += $transaction['amount'];
                }
            }

            if ($totalAmount + $amount > $limits['max'])
            {
                print json_encode(array('location' => URL_ERROR . self::LIMITS));
                return;
            }
        }

        $at = new AtPm();
        $at->setUID($usr->getId());
        $at->setPayeeAccount($userAccount);
        $at->setAmount($amount);
        $at->setUnits($units);
        $at->setType(1);
        $at->setStatus(0);
        $at->insert();

        $success = self::send_output_link('PM', $at->getId(), $usr);
        if (!$success)
        {
            print json_encode(array('location' => URL_SERVER_ERROR));
            return;
        }

        print json_encode(array('location' => URL_NOTIFICATION_SEND));
    }

    static public function PM_transaction_o_complete()
    {
        $id = Core::validate($_GET['id']);
        $hash = Core::validate($_GET['hash']);

        $at = new AtPm();
        if (!$at->findById($id))
        {
            header('Location: '.URL_WRONG_DATA_INPUT);
            exit;
        }

        if ($hash != self::hash_for_money_output_link($at->getId(), $at->getUID()))
        {
            header('Location: '.URL_WRONG_DATA_INPUT);
            exit;
        }

        $amount = $at->getAmount();

        $wallets = self::PM_MinAndMaxWallets($at->getUnits());
        if (($wallets['max'] == null) ||
            ($wallets['max']['current_balance'] < $amount))
        {
            header('Location: '.URL_SERVER_ERROR);
            exit;
        }
        $wallet = new WalletsPm();
        $wallet->findById($wallets['max']['id']);

        $perfectMoney = new PerfectMoney();
        $perfectMoney->setAccountID($wallet->getAccountId());
        $perfectMoney->setPassPhrase($wallet->getPassPhrase());

        $resp = $perfectMoney->transfer($wallet->getAccount(), $at->getPayeeAccount(), $amount, 'Transfer from bitmonex', false);
        if ($resp == null)
        {
            header('Location: '.URL_SERVER_ERROR);
            exit;
        }

        $currency = new Currency();
        $currency->findBy(array('Name' => $at->getUnits()));

        $limits = self::transactionLimits($currency->getId(), 'PM', 1);

        $feeVolume = $amount * $limits['fee'];
        $feeVolume = Core::round_up($feeVolume, 2);

        $purses = Purse::findBy(array('UID' => $at->getUID(), 'CurId' => $currency->getId()));
        $purse = new Purse();
        $purse->findById($purses[0]['id']);
        $purse->addValue(-($amount + $feeVolume));
        $purse->save();

        $systemFeeVolume = $amount * $limits['system_fee'];
        $systemFeeVolume = Core::round_up($systemFeeVolume, 2);

        $profit = ($feeVolume - $systemFeeVolume) * $wallet->getShare();
        $wallet->setProfit($wallet->getProfit() + $profit);
        $wallet->save();

        $at->setStatus(1);
        $at->setPayerAccount($wallet->getAccount());
        $at->setBatchNum($resp['PAYMENT_BATCH_NUM']);
        $at->setTimestamp(Core::timestamp_gmp());
        $at->save();

        header('Location: '.URL_SUCCESS_PAYMENT);
    }

    static private function PM_MinAndMaxWallets($units)
    {
        $pm_wallets = WalletsPm::findBy(array('units' => $units));
        if (empty($pm_wallets)) {
            return null;
        }

        $maxBalance = null;
        $keyMax = null;

        $minBalance = null;
        $keyMin = null;

        $perfectMoney = new PerfectMoney();
        foreach ($pm_wallets as $key=>$value)
        {
            $perfectMoney->setAccountID($value['account_id']);
            $perfectMoney->setPassPhrase($value['pass_phrase']);

            $response = $perfectMoney->balance();
            if ($response !== null)
            {
                $balance = $response[$value['account']];
                $pm_wallets[$key]['current_balance'] = $balance;

                if (($minBalance === null) || ($minBalance > $balance)) {
                    $minBalance = $balance;
                    $keyMin = $key;
                }

                if (($maxBalance === null) || ($maxBalance < $balance)) {
                    $maxBalance = $balance;
                    $keyMax = $key;
                }
            }
        }

        $result['min'] = $pm_wallets[$keyMin];
        $result['max'] = $pm_wallets[$keyMax];

        return $result;
    }

    /* OKPay */
    static public function OKP_transaction()
    {
        $usr = usr::getCurrentUser(1);
        if($usr == null)
        {
            exit;
        }

        $currency_name = Core::validate($_POST['currency']);

        $currency = new Currency();
        if (!$currency->findBy(array('Name' => $currency_name)))
        {
            Core::printErrorJson(URL_SERVER_ERROR);
            exit;
        }

        $wallets = self::OKP_MinAndMaxWallets($currency_name);
        if ($wallets['min'] == null)
        {
            Core::printErrorJson(URL_SERVER_ERROR);
            exit;
        }
        $wallet = new WalletsOkp();
        $wallet->findById($wallets['min']['id']);

        $purseList = Purse::findBy(array('UID' => $usr->getId(), 'CurId' =>  $currency->getId()));
        if(empty($purseList))
        {
            exit;
        }

        $at = new AtOkp();
        $result = $at->findBy(array('UID' => $usr->getId(), 'currency' => $currency_name, 'type' => 0, 'status' => 0));
        if ($result) {
            $at->delete();
        }

        $at = new AtOkp();
        $at->setUID($usr->getId());
        $at->setPayeeEmail($wallet->getEmail());
        $at->setCurrency($currency_name);
        $at->setType(0);
        $at->setTimestamp(Core::timestamp_gmp());
        $at->setStatus(0);
        $at->insert();

        $data['success'] = 1;
        $data['receiver'] = $wallet->getWalletId();
        $data['invoice'] = $at->getId();
        $data['item_name'] = 'Deposit to Bitmonex #'.$at->getId();
        print json_encode($data);
    }

    static public function OKP_transaction_success()
    {
        header('Location: '.URL_SUCCESS_PAYMENT);
    }

    static public function OKP_transaction_fail()
    {
        $usr = usr::getCurrentUser(1);
        if($usr == null) {
            exit;
        }

        $invoice = Core::validate($_POST['ok_invoice']);

        $at = new AtOkp();
        $exist = $at->findBy(array(
            'id'            => $invoice,
            'UID'           => $usr->getId(),
            'type'          => 0,
            'status'        => 0,
        ));

        if ($exist) {
            $at->delete();
        }

        header('Location: /usr/mypage');
    }

    static public function OKP_transaction_notification()
    {
        $okpayAPI = new OKPay();
        $result = $okpayAPI->verify_notification($_POST);

        if ($result == 'VERIFIED')
        {
            $ok_txn_kind = Core::validate($_POST['ok_txn_kind']);
            $ok_txn_status = Core::validate($_POST['ok_txn_status']);
            $ok_txn_id = Core::validate($_POST['ok_txn_id']);
            $ok_receiver_email = Core::validate($_POST['ok_receiver_email']);
            $ok_payer_email = Core::validate($_POST['ok_payer_email']);
            $ok_invoice = Core::validate($_POST['ok_invoice']);
            $ok_txn_currency = Core::validate($_POST['ok_txn_currency']);
            $ok_txn_gross = Core::validate($_POST['ok_txn_gross']);
            $ok_txn_fee = Core::validate($_POST['ok_txn_fee']);

            $wallets = WalletsOkp::findBy(array('payee_email' => $ok_receiver_email, 'currency' => $ok_txn_currency));
            if (empty($wallets)) {
                return;
            }

            if ($ok_txn_kind != 'payment_link') {
                exit;
            }

            if ($ok_txn_status != 'completed') {
                exit;
            }

            $at = new AtOkp();
            $result = $at->findBy(array('transaction_id' => $ok_txn_id));
            if ($result) {
                exit;
            }

            $result = $at->findBy(array(
                'id' => $ok_invoice,
                'payee_email' => $ok_receiver_email,
                'currency' => $ok_txn_currency,
                'type' => 0,
                'status' => 0)
            );
            if ($result == false) {
                exit;
            }

            $currency = new Currency();
            $currency->findBy(array('Name' => $ok_txn_currency));

            $limits = self::transactionLimits($currency->getId(), 'OKP', 0);

            $amount = $ok_txn_gross - $ok_txn_fee;

            if (($amount < $limits['min']) ||
                ($limits['max'] != null && $amount > $limits['max']))
            {
                exit;
            }

            $feeVolume = $amount * $limits['fee'];
            $feeVolume = Core::round_up($feeVolume, 2);

            $purseList = Purse::findBy(array('UID' => $at->getUID(), 'CurId' =>  $currency->getId()));
            $purse = new Purse();
            $purse->findById($purseList[0]['id']);
            $purse->addValue($amount - $feeVolume);
            $purse->save();

            $wallet = new WalletsOkp();
            $wallet->findById($wallets[0]['id']);
            $profit = $feeVolume * $wallet->getShare();
            $wallet->setProfit($wallet->getProfit() + $profit);
            $wallet->save();

            $at->update(array(
                'payer_mail'   => $ok_payer_email,
                'amount' => $amount,
                'transaction_id' => $ok_txn_id,
                'status' => 1,
            ));
        }
        elseif($result == 'INVALID')
        {
            $fp=fopen("/tmp/notify_OKP.txt","a");
            fwrite($fp, $result . " : " .$_POST);
        }
        elseif($result == 'TEST')
        {
            $fp=fopen("/tmp/notify_OKP.txt","a");
            fwrite($fp, $result . " : " .$_POST);
        }
        else
        {
            $fp=fopen("/tmp/notify_OKP.txt","a");
            fwrite($fp, $result . " : " .$_POST);
        }
    }

    static public function OKP_transaction_o()
    {
        $usr = usr::getCurrentUser(1);
        if($usr == null)
        {
            header('Location: /');
            exit;
        }

        $currency_name = Core::validate($_POST['currency']);
        $receiver_email = Core::validate($_POST['email']);
        $amount = Core::validate($_POST['amount']);

        if ($amount == 0 || !Core::isDouble($amount, 2))
        {
            print json_encode(array('location' => URL_WRONG_MONEY_VALUE));
            exit;
        }

        if (!Core::isEmailAddress($receiver_email))
        {
            print json_encode(array('location' => URL_WRONG_DATA_INPUT));
            exit;
        }

        $currency = new Currency();
        if (!$currency->findBy(array('Name' => $currency_name)))
        {
            print json_encode(array('location' => URL_SERVER_ERROR));
            exit;
        }

        $purseList = Purse::findBy(array('UID' => $usr->getId(), 'CurId' => $currency->getId()));
        if(empty($purseList))
        {
            exit;
        }

        $limits = self::transactionLimits($currency->getId(), 'OKP', 1);
        $feeVolume = $amount * $limits['fee'];
        $feeVolume = Core::round_up($feeVolume, 2);

        $purse = new Purse();
        $purse->findById($purseList[0]['id']);

        if ($purse->getValue() < $amount + $feeVolume)
        {
            print json_encode(array('location' => URL_WRONG_MONEY_VALUE));
            exit;
        }

        if ($amount < $limits['min'])
        {
            print json_encode(array('location' => URL_ERROR . self::LIMITS));
            return;
        }

        if ($limits['max'] != null)
        {
            $transaction_history = new AtOkp();
            $transactions = $transaction_history->findAllByForLastPeriod(array(
                'UID' => $usr->getid(),
                'type' => 1,
                'status' => 1,
            ));

            $totalAmount = 0.0;
            if (isset($transactions)) {
                foreach ($transactions as $transaction) {
                    $totalAmount += $transaction['amount'];
                }
            }

            if ($totalAmount + $amount > $limits['max'])
            {
                print json_encode(array('location' => URL_ERROR . self::LIMITS));
                return;
            }
        }

        $at = new AtOkp();
        $at->setUID($usr->getId());
        $at->setPayeeEmail($receiver_email);
        $at->setAmount($amount);
        $at->setCurrency($currency_name);
        $at->setType(1);
        $at->setStatus(0);
        $at->insert();

        $success = self::send_output_link('OKP', $at->getId(), $usr);
        if (!$success)
        {
            print json_encode(array('location' => URL_SERVER_ERROR));
            return;
        }

        print json_encode(array('location' => URL_NOTIFICATION_SEND));
    }

    static public function OKP_transaction_o_complete()
    {
        $id = Core::validate($_GET['id']);
        $hash = Core::validate($_GET['hash']);

        $at = new AtOkp();
        if (!$at->findById($id))
        {
            header('Location: '.URL_WRONG_DATA_INPUT);
            exit;
        }

        if ($hash != self::hash_for_money_output_link($at->getId(), $at->getUID()))
        {
            header('Location: '.URL_WRONG_DATA_INPUT);
            exit;
        }

        $amount = $at->getAmount();

        $wallets = self::OKP_MinAndMaxWallets($at->getCurrency());
        if (($wallets['max'] == null) ||
            ($wallets['max']['current_balance'] < $amount))
        {
            header('Location: '.URL_SERVER_ERROR);
            exit;
        }
        $wallet = new WalletsOkp();
        $wallet->findById($wallets['max']['id']);

        $okpay_api = new OKPay();
        $resp = $okpay_api->send_money($wallet->getWalletId(), $wallet->getApiPassword(), $at->getCurrency(), $at->getPayeeEmail(), $amount, 'Transfer from bitmonex', true, $at->getId());
        if ($resp == null)
        {
            header('Location: '.URL_SERVER_ERROR);
            exit;
        }

        $currency = new Currency();
        $currency->findBy(array('Name' => $at->getCurrency()));

        $limits = self::transactionLimits($currency->getId(), 'OKP', 1);

        $feeVolume = $amount * $limits['fee'];
        $feeVolume = Core::round_up($feeVolume, 2);

        $purses = Purse::findBy(array('UID' => $at->getUID(), 'CurId' => $currency->getId()));
        $purse = new Purse();
        $purse->findById($purses[0]['id']);
        $purse->addValue(-($amount + $feeVolume));
        $purse->save();

        $systemFeeVolume = $amount * $limits['system_fee'];
        $systemFeeVolume = Core::round_up($systemFeeVolume, 2);

        $profit = ($feeVolume - $systemFeeVolume) * $wallet->getShare();
        $wallet->setProfit($wallet->getProfit() + $profit);
        $wallet->save();

        $at->setPayerEmail($wallet->getEmail());
        $at->setTransactionId($resp['ID']);
        $at->setTimestamp(Core::timestamp_gmp());
        $at->setStatus(1);
        $at->save();

        header('Location: '.URL_SUCCESS_PAYMENT);
    }

    static private function OKP_MinAndMaxWallets($currency)
    {
        $wallets = WalletsOkp::findBy(array('currency' => $currency));
        if (empty($wallets)) {
            return null;
        }

        $maxBalance = null;
        $keyMax = null;

        $minBalance = null;
        $keyMin = null;

        $okpay = new OKPay();
        foreach ($wallets as $key=>$value)
        {
            $balance = $okpay->currency_balance($value['wallet_id'], $value['api_password'], $currency);
            if ($balance !== null)
            {
                if (($minBalance === null) || ($minBalance > $balance)) {
                    $minBalance = $balance;
                    $keyMin = $key;
                }

                if (($maxBalance === null) || ($maxBalance < $balance)) {
                    $maxBalance = $balance;
                    $keyMax = $key;
                }
            }
        }

        $result['min'] = $wallets[$keyMin];
        $result['max'] = $wallets[$keyMax];

        return $result;
    }


    /* EgoPay */
    static public function EGOP_transaction()
    {
        $usr = usr::getCurrentUser(1);
        if($usr == null)
        {
            exit;
        }

        $currency_name = Core::validate($_POST['currency']);
        $amount = Core::validate($_POST['amount']);

        if ($amount == 0 || !Core::isDouble($amount, 2))
        {
            Core::printErrorJson(URL_WRONG_MONEY_VALUE);
            exit;
        }

        $currency = new Currency();
        if (!$currency->findBy(array('Name' => $currency_name)))
        {
            Core::printErrorJson(URL_SERVER_ERROR);
            exit;
        }

        $wallets = self::EGOP_MinAndMaxWallets($currency);
        if ($wallets['min'] == null)
        {
            Core::printErrorJson(URL_SERVER_ERROR);
            exit;
        }
        $wallet = new WalletsEgop();
        $wallet->findById($wallets['min']['id']);

        $purseList = Purse::findBy(array('UID' => $usr->getId(), 'CurId' =>  $currency->getId()));
        if(empty($purseList))
        {
            exit;
        }

        $at = new AtEgop();
        $at->setUID($usr->getId());
        $at->setOurWalletId($wallet->getId());
        $at->setCurrencyId($currency->getId());
        $at->setAmount($amount);
        $at->setType(0);
        $at->setTimestamp(Core::timestamp_gmp());
        $at->setStatus(0);
        $at->insert();

        $result = array(
            'success'   => 1,
            'store_id'  => $wallet->getStoreId(),
            'at_id'     => $at->getId(),
        );

        print json_encode($result);
    }

    static public function EGOP_transaction_callback()
    {
        // /* for debug
        Core::write_log("/tmp/callback_EGOP.txt", __FUNCTION__ . ' : First POST : ' . print_r($_POST, true));
        reset($_POST);
        // */

        $amount = Core::validate($_POST['fAmount']);
        $currency_name = Core::validate($_POST['sCurrency']);
        $at_id = Core::validate($_POST['cf_1']);

        $currency = new Currency();
        if (!$currency->findBy(array('Name' => $currency_name)))
        {
            exit;
        }

        $at = new AtEgop();
        $exist = $at->findBy(array(
            'id'            => $at_id,
            'amount'        => $amount,
            'currency_id'   => $currency->getId(),
            'type'          => 1,
            'status'        => 0,
        ));

        if (!$exist) {
            exit;
        }

        $wallet = new WalletsEgop();
        $wallet ->findById($at->getOurWalletId());

        try
        {
            $oEgopay = new EgoPaySciCallback(array(
                'store_id'          => $wallet->getStoreId(),
                'store_password'    => $wallet->getStorePassword(),
                'checksum_key'      => $wallet->getChecksumKey(),
                'verify_peer'       => false,
            ));
            $aResponse = $oEgopay->getResponse($_POST);

            $status = $aResponse['sStatus'];
            if ($status == 'Completed') // probably need to add 'Pending' status
            {
                $limits = self::transactionLimits($currency->getId(), 'EGOP', 0);

                if (($amount < $limits['min']) ||
                    ($limits['max'] != null && $amount > $limits['max']))
                {
                    Core::write_log(EGOPAY_LOG_PATH, __FUNCTION__ . ' Error : Incorrect amount (limits are not complied) : ' . $amount);
                    return;
                }

                $feeVolume = $amount * $limits['fee'];
                $feeVolume = Core::round_up($feeVolume, 2);

                $purseList = Purse::findBy(array('UID' => $at->getUID(), 'CurId' =>  $currency->getId()));
                $purse = new Purse();
                $purse->findById($purseList[0]['id']);
                $purse->addValue($amount - $feeVolume);
                $purse->save();

                $profit = $feeVolume * $wallet->getShare();
                $wallet->setProfit($wallet->getProfit() + $profit);
                $wallet->save();

                $at->setStatus(1);
                $at->setTransactionId($aResponse['sId']);
                $at->setClientAccount($aResponse['sEmail']);
                $at->save();
            }
            else if ($status == 'Cancelled')
            {
                $at->delete();
            }
            else if ($status == 'TEST SUCCESS')
            {
                Core::write_log(EGOPAY_LOG_PATH, __FUNCTION__ . ' : TEST SUCCESS POST : ' . print_r($_POST, true));
            }
        }
        catch(EgoPayException $e)
        {
            Core::write_log(EGOPAY_LOG_PATH, __FUNCTION__ . ' Exception : ' . $e->getCode() . " : " . $e->getMessage());
        }
    }

    static public function EGOP_transaction_success()
    {
        header('Location: '.URL_SUCCESS_PAYMENT);
    }

    static public function EGOP_transaction_fail()
    {
        header('Location: /usr/mypage');
    }

    static public function EGOP_transaction_o()
    {
        $usr = usr::getCurrentUser(1);
        if($usr == null)
        {
            header('Location: /');
            exit;
        }

        $client_account = Core::validate($_POST['email']);
        $amount = Core::validate($_POST['amount']);
        $currency_name = Core::validate($_POST['currency']);

        if ($amount == 0 || !Core::isDouble($amount, 2))
        {
            print json_encode(array('location' => URL_WRONG_MONEY_VALUE));
            exit;
        }

        if (!Core::isEmailAddress($client_account))
        {
            print json_encode(array('location' => URL_WRONG_DATA_INPUT));
            exit;
        }

        $currency = new Currency();
        if (!$currency->findBy(array('Name' => $currency_name)))
        {
            print json_encode(array('location' => URL_SERVER_ERROR));
            exit;
        }

        $purseList = Purse::findBy(array('UID' => $usr->getId(), 'CurId' => $currency->getId()));
        if(empty($purseList))
        {
            exit;
        }

        $limits = self::transactionLimits($currency->getId(), 'EGOP', 1);
        $feeVolume = $amount * $limits['fee'];
        $feeVolume = Core::round_up($feeVolume, 2);

        $purse = new Purse();
        $purse->findById($purseList[0]['id']);

        if ($purse->getValue() < $amount + $feeVolume)
        {
            print json_encode(array('location' => URL_WRONG_MONEY_VALUE));
            exit;
        }

        if ($amount < $limits['min'])
        {
            print json_encode(array('location' => URL_ERROR . self::LIMITS));
            return;
        }

        if ($limits['max'] != null)
        {
            $transaction_history = new AtEgop();
            $transactions = $transaction_history->findAllByForLastPeriod(array(
                'UID' => $usr->getid(),
                'type' => 1,
                'status' => 1,
            ));

            $totalAmount = 0.0;
            if (isset($transactions)) {
                foreach ($transactions as $transaction) {
                    $totalAmount += $transaction['amount'];
                }
            }

            if ($totalAmount + $amount > $limits['max'])
            {
                print json_encode(array('location' => URL_ERROR . self::LIMITS));
                return;
            }
        }

        $at = new AtEgop();
        $at->setUID($usr->getId());
        $at->setClientAccount($client_account);
        $at->setAmount($amount);
        $at->setCurrencyId($currency->getId());
        $at->setType(1);
        $at->setStatus(0);
        $at->setTimestamp(Core::timestamp_gmp());
        $at->insert();

        $success = self::send_output_link('EGOP', $at->getId(), $usr);
        if (!$success)
        {
            print json_encode(array('location' => URL_SERVER_ERROR));
            return;
        }

        print json_encode(array('location' => URL_NOTIFICATION_SEND));
    }

    static public function EGOP_transaction_o_complete()
    {
        $id = Core::validate($_GET['id']);
        $hash = Core::validate($_GET['hash']);

        $at = new AtEgop();
        if (!$at->findById($id))
        {
            header('Location: '.URL_WRONG_DATA_INPUT);
            exit;
        }

        if ($hash != self::hash_for_money_output_link($at->getId(), $at->getUID()))
        {
            header('Location: '.URL_WRONG_DATA_INPUT);
            exit;
        }

        $currency = new Currency();
        $currency->findById($at->getCurrencyId());

        $wallets = self::EGOP_MinAndMaxWallets($currency);

        $amount = $at->getAmount();

        if (($wallets['max'] == null) ||
            ($wallets['max']['current_balance'] < $amount))
        {
            Core::write_log(EGOPAY_LOG_PATH, __FUNCTION__ . ' : Not enough money on this wallet (id) : ' . $wallets['max']['id'] . '(at_egop id=' . $at->getId() . ')');
            header('Location: '.URL_SERVER_ERROR);
            exit;
        }
        $wallet = new WalletsEgop();
        $wallet->findById($wallets['max']['id']);

        $oAuth = new EgoPayAuth($wallet->getEmail(), $wallet->getApiId(), $wallet->getApiPassword());
        $oEgoPayJsonAgent = new EgoPayJsonApiAgent($oAuth);
        $oEgoPayJsonAgent->setVerifyPeer(false);
        try
        {
            $oResponse = $oEgoPayJsonAgent->getTransfer($at->getClientAccount(), $at->getAmount(), $currency->getName(), 'Transfer from Bitmonex');
            // for debug
            Core::write_log(EGOPAY_LOG_PATH, __FUNCTION__ . ' : getTransfer response: ' . print_r($oResponse, true));
            reset($oResponse);
            //
        }
        catch (EgoPayApiException $e)
        {
            // error codes from here: https://www.egopay.com/developers/api/payments/send-payment
            if ($e->getCode() == 11 || $e->getCode() == 12) {
                header('Location: '.URL_WRONG_DATA_INPUT);
                exit;
            }

            Core::write_log(EGOPAY_LOG_PATH, __FUNCTION__ . ' : getTransfer Error: ' . $e->getCode() . " : " . $e->getMessage());
            header('Location: '.URL_SERVER_ERROR);
            exit;
        }

        $limits = self::transactionLimits($currency->getId(), 'EGOP', 1);

        $feeVolume = $amount * $limits['fee'];
        $feeVolume = Core::round_up($feeVolume, 2);

        $purses = Purse::findBy(array('UID' => $at->getUID(), 'CurId' => $currency->getId()));
        $purse = new Purse();
        $purse->findById($purses[0]['id']);
        $purse->addValue(-($amount + $feeVolume));
        $purse->save();

        $systemFeeVolume = $amount * $limits['system_fee'];
        $systemFeeVolume = Core::round_up($systemFeeVolume, 2);

        $profit = ($feeVolume - $systemFeeVolume) * $wallet->getShare();
        $wallet->setProfit($wallet->getProfit() + $profit);
        $wallet->save();

        $at->setStatus(1);
        $at->setOurWalletId($wallet->getId());
        $at->setTransactionId($oResponse->transaction->sId);
        $at->setTimestamp(Core::timestamp_gmp());
        $at->save();

        header('Location: '.URL_SUCCESS_PAYMENT);
    }

    static private function EGOP_MinAndMaxWallets(Currency $currency)
    {
        $wallets = WalletsEgop::findBy(array('currency_id' => $currency->getId()));
        if (empty($wallets))
        {
            return null;
        }

        $maxBalance = null;
        $keyMax = null;

        $minBalance = null;
        $keyMin = null;

        foreach ($wallets as $key=>$value)
        {
            $oAuth = new EgoPayAuth($value['email'], $value['api_id'], $value['api_password']);
            $oEgoPayJsonAgent = new EgoPayJsonApiAgent($oAuth);
            $oEgoPayJsonAgent->setVerifyPeer(false);
            try
            {
                $balance = $oEgoPayJsonAgent->getBalance($currency->getName());

                if (($minBalance === null) || ($minBalance > $balance)) {
                    $minBalance = $balance;
                    $keyMin = $key;
                }

                if (($maxBalance === null) || ($maxBalance < $balance)) {
                    $maxBalance = $balance;
                    $keyMax = $key;
                }
            }
            catch (EgoPayApiException $e)
            {
                Core::write_log(EGOPAY_LOG_PATH, __FUNCTION__ . ' : getBalance Error: ' . $e->getCode() . " : " . $e->getMessage());
            }
        }

        $result['min'] = $wallets[$keyMin];
        $result['max'] = $wallets[$keyMax];

        return $result;
    }


    static private function transactionLimits($currencyId, $paymentSystemName, $type = 0)
    {
        $paySystem = new PaymentSystem();
        $paySystem->findBy(array('name' => $paymentSystemName));

        $curPaySystem = new CurrencyPaymentSystem();
        $result = $curPaySystem->findBy(array(
            'cur_id'    => $currencyId,
            'system_id' => $paySystem->getId(),
        ));

        if (!$result){
            $system_fee = 0.0;
            $fee = 0.0;
            $min = 0.0;
            $max = null;
        } else if ($type == 1){
            $system_fee = $curPaySystem->getSystemFee();
            $fee = $curPaySystem->getOutputFee();
            $min = $curPaySystem->getOutputMin();
            $max = $curPaySystem->getOutputMax();
        } else {
            $system_fee = $curPaySystem->getSystemFee();
            $fee = $curPaySystem->getInputFee();
            $min = $curPaySystem->getInputMin();
            $max = $curPaySystem->getInputMax();
        }

        return array('system_fee' => $system_fee, 'fee' => $fee, 'min' => $min, 'max' => $max);
    }


    static private function send_output_link($payment_system, $at_id, User $usr)
    {
        $hash = self::hash_for_money_output_link($at_id, $usr->getId());
        $link = 'http://'.Core::server_url().'/money/'.$payment_system.'_transaction_o_complete?id='.$at_id.'&hash='.$hash;
        $message = self::get_notification_message($link);

        return Core::send_mail($usr->getEmail(), 'Bitmonex: link for withdrawal', $message);
    }

    static private function hash_for_money_output_link($at_id, $UID)
    {
        return Core::calculateHash($at_id . $UID . 'salt, pepper and a pinch of coriander');
    }

    static private function get_notification_message($link)
    {
        $header = '<h1>'. Core::translateToCurrentLocale("Hello") .', </h1>
        <p class="lead">'. Core::translateToCurrentLocale("you have registered money withdraw from the Bitmonex exchange") .'.</p>';
        $body = '<p>'.Core::translateToCurrentLocale("To confirm your withdrawing, please click on this link"). '. <a href="'.$link.'">'.Core::translateToCurrentLocale("Withdraw money").'!</a></p>';
        return usr::getMessage($header,$body);
    }
}
