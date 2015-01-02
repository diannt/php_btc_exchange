<?php

class admin extends MainController
{
    static private $user = null;

    static public function beforeAction()
    {
        $usr = usr::getCurrentUser(1);
        if(!isset($usr) || !Core::isAdministrator($usr))
        {
            header('Location: / ');
            exit;
        }

        self::$user = $usr;
    }

    static public function news()
    {
        Core::runView('ViewTemplate/account', array(
            'pageName'          =>  'News',
            'activeMenu'        =>  'News',
            'pagePath'          =>  'Administration/admin_news',
            'user'              =>  self::$user
        ));
    }

    static public function ym()
    {
        $walletsModel = new WalletsYm();
        $wallets = $walletsModel->getAllWallets();

        Core::runView('ViewTemplate/account', array(
            'pageName'          =>  'YM transactions',
            'activeMenu'        =>  'YM transactions',
            'pagePath'          =>  'Administration/admin_ym',
            'user'              =>  self::$user,
            'wallets'           =>  $wallets
        ));
    }

    static public function pm()
    {
        $wallets = WalletsPm::getAllWallets();

        $perfectMoney = new PerfectMoney();
        foreach ($wallets as $key=>$value)
        {
            $perfectMoney->setAccountID($value['account_id']);
            $perfectMoney->setPassPhrase($value['pass_phrase']);

            $response = $perfectMoney->balance();
            if ($response != null) {
                $balance = $response[$value['account']];
            } else {
                $balance = "currently unavailable"; // always on the localhost
            }
            $wallets[$key]['current_balance'] = $balance;
        }

        Core::runView('ViewTemplate/account', array(
            'pageName'          =>  'PM wallets',
            'activeMenu'        =>  'PM wallets',
            'pagePath'          =>  'Administration/admin_pm',
            'user'              =>  self::$user,
            'wallets'           =>  $wallets
        ));
    }

    static public function addNewPMWallet()
    {
        $account_id = Core::validate($_POST['ACCOUNT_ID']);
        $pass_phrase = Core::validate($_POST['PASS_PHRASE']);
        $alternate_pass_phrase = Core::validate($_POST['ALTERNATE_PASS_PHRASE']);
        $units = Core::validate($_POST['UNITS']);
        $account = Core::validate($_POST['ACCOUNT']);
        $share = Core::validate($_POST['SHARE']); // percent

        if ($account_id == null || $pass_phrase == null || $alternate_pass_phrase == null ||
            $units == null || $account == null ||
            $share == null || !Core::isDouble($share))
        {
            print 'Incorrect input data';
            exit;
        }

        $result = WalletsPm::findBy(array('account' => $account));
        if (!empty($result))
        {
            print 'This account already exists';
            exit;
        }

        $perfectMoney = new PerfectMoney();
        $perfectMoney->setAccountId($account_id);
        $perfectMoney->setPassPhrase($pass_phrase);

        $resp = $perfectMoney->balance();
        if ($resp == null) // always on the localhost
        {
            print $perfectMoney->getError();
            exit;
        }

        if (!isset($resp[$account]))
        {
            print 'Invalid account';
            exit;
        }

        $value = $resp[$account];

        $pmWallet = new WalletsPm();
        $pmWallet->setAccountId($account_id);
        $pmWallet->setPassPhrase($pass_phrase);
        $pmWallet->setAlternatePassPhrase($alternate_pass_phrase);
        $pmWallet->setUnits($units);
        $pmWallet->setAccount($account);
        $pmWallet->setValue($value);
        $pmWallet->setShare($share / 100.0);
        $pmWallet->insert();

        header('Location: /admin/pm');
    }

    static public function ltc()
    {
        $wallets = WalletsLtc::getAllWallets();

        $litecoin = new jsonRPCClient('http://' . LTC_RPC_USER . ':' . LTC_RPC_PASSWORD . '@' . LTC_RPC_HOST . ':' . LTC_RPC_PORT . '/');
        foreach ($wallets as $key=>$value)
        {
            try {
                $balance = $litecoin->getbalance($value['account']);
            } catch (Exception $e) {
                $balance = "currently unavailable";
            }
            $wallets[$key]['current_balance'] = $balance;
        }

        Core::runView('ViewTemplate/account', array(
            'pageName'          =>  'LTC wallets',
            'activeMenu'        =>  'LTC wallets',
            'pagePath'          =>  'Administration/admin_ltc',
            'user'              =>  self::$user,
            'wallets'           =>  $wallets
        ));
    }

    static public function addNewLTCWallet()
    {
        $account = Core::validate($_POST['ACCOUNT']);
        $share = Core::validate($_POST['SHARE']); // percent

        if ($account == null || $share == null || !Core::isDouble($share))
        {
            print 'Incorrect input data';
            exit;
        }

        $result = WalletsLtc::findBy(array('account' => $account));
        if (!empty($result))
        {
            print 'This account already exists';
            exit;
        }

        $litecoin = new jsonRPCClient('http://' . LTC_RPC_USER . ':' . LTC_RPC_PASSWORD . '@' . LTC_RPC_HOST . ':' . LTC_RPC_PORT . '/');
        try {
            $balance = $litecoin->getbalance($account);
        } catch (Exception $e) {
            print $e;
            exit;
        }

        $wallet = new WalletsLtc();
        $wallet->setAccount($account);
        $wallet->setValue($balance);
        $wallet->setShare($share / 100.0);
        $wallet->insert();

        header('Location: /admin/ltc');
    }

    static public function btc()
    {
        $wallets = WalletsBtc::getAllWallets();

        $bitcoin = new jsonRPCClient('http://' . BTC_RPC_USER . ':' . BTC_RPC_PASSWORD . '@' . BTC_RPC_HOST . ':' . BTC_RPC_PORT . '/');
        foreach ($wallets as $key=>$value)
        {
            try {
                $balance = $bitcoin->getbalance($value['account']);
            } catch (Exception $e) {
                $balance = "currently unavailable";
            }
            $wallets[$key]['current_balance'] = $balance;
        }

        Core::runView('ViewTemplate/account', array(
            'pageName'          =>  'BTC wallets',
            'activeMenu'        =>  'BTC wallets',
            'pagePath'          =>  'Administration/admin_btc',
            'user'              =>  self::$user,
            'wallets'           =>  $wallets
        ));
    }

    static public function addNewBTCWallet()
    {
        $account = Core::validate($_POST['ACCOUNT']);
        $share = Core::validate($_POST['SHARE']); // percent

        if ($account == null || $share == null || !Core::isDouble($share))
        {
            print 'Incorrect input data';
            exit;
        }

        $result = WalletsBtc::findBy(array('account' => $account));
        if (!empty($result))
        {
            print 'This account already exists';
            exit;
        }

        $bitcoin = new jsonRPCClient('http://' . BTC_RPC_USER . ':' . BTC_RPC_PASSWORD . '@' . BTC_RPC_HOST . ':' . BTC_RPC_PORT . '/');
        try {
            $balance = $bitcoin->getbalance($account);
        } catch (Exception $e) {
            print $e;
            exit;
        }

        $wallet = new WalletsBtc();
        $wallet->setAccount($account);
        $wallet->setValue($balance);
        $wallet->setShare($share / 100.0);
        $wallet->insert();

        header('Location: /admin/btc');
    }

    static public function okp()
    {
        $wallets = WalletsOkp::getAllWallets();

        $okpay = new OKPay();
        foreach ($wallets as $key=>$value)
        {
            $balance = $okpay->currency_balance($value['wallet_id'], $value['api_password'], $value['currency']);
            if ($balance === null)
                $balance = "currently unavailable";

            $wallets[$key]['current_balance'] = $balance;
        }

        Core::runView('ViewTemplate/account', array(
            'pageName'          =>  'OKP wallets',
            'activeMenu'        =>  'OKP wallets',
            'pagePath'          =>  'Administration/admin_okp',
            'user'              =>  self::$user,
            'wallets'           =>  $wallets
        ));
    }

    static public function addNewOKPWallet()
    {
        $email = Core::validate($_POST['EMAIL']);
        $wallet_id = Core::validate($_POST['WALLET_ID']);
        $api_password = Core::validate($_POST['API_PASSWORD']);
        $currency = Core::validate($_POST['CURRENCY']);
        $share = Core::validate($_POST['SHARE']); // percent

        if ($email == null || $wallet_id == null || $api_password == null || $currency == null ||
            $share == null || !Core::isDouble($share))
        {
            print 'Incorrect input data';
            exit;
        }

        $result = WalletsOkp::findBy(array('email' => $email, 'wallet_id' => $wallet_id, 'currency' => $currency));
        if (!empty($result))
        {
            print 'This wallet already exists';
            exit;
        }

        $okpay = new OKPay();
        $balance = $okpay->currency_balance($wallet_id, $api_password, $currency);
        if ($balance === null)
        {
            print $okpay->Error();
            exit;
        }

        $wallet = new WalletsOkp();
        $wallet->setEmail($email);
        $wallet->setWalletId($wallet_id);
        $wallet->setApiPassword($api_password);
        $wallet->setCurrency($currency);
        $wallet->setValue($balance);
        $wallet->setShare($share / 100.0);
        $wallet->insert();

        header('Location: /admin/okp');
    }

    static public function egop()
    {
        $wallets = WalletsEgop::getAllWallets();
        $currency = new Currency();
        foreach ($wallets as $key=>$value)
        {
            $currency->findById($value['currency_id']);
            $wallets[$key]['currency'] = $currency->getName();

            $oAuth = new EgoPayAuth($value['email'], $value['api_id'], $value['api_password']);
            $oEgoPayJsonAgent = new EgoPayJsonApiAgent($oAuth);
            $oEgoPayJsonAgent->setVerifyPeer(false);
            try
            {
                $balance = $oEgoPayJsonAgent->getBalance($currency->getName());
            }
            catch (EgoPayApiException $e)
            {
                $balance = 'currently unavailable';
                Core::write_log(EGOPAY_LOG_PATH, __FUNCTION__ . ' : getBalance Error: ' . $e->getCode() . " : " . $e->getMessage());
            }

            $wallets[$key]['current_balance'] = $balance;
        }

        Core::runView('ViewTemplate/account', array(
            'pageName'          =>  'EGOP wallets',
            'activeMenu'        =>  'EGOP wallets',
            'pagePath'          =>  'Administration/admin_egop',
            'user'              =>  self::$user,
            'wallets'           =>  $wallets
        ));
    }

    static public function addNewEGOPWallet()
    {
        $email = Core::validate($_POST['EMAIL']);
        $api_id = Core::validate($_POST['API_ID']);
        $api_password = Core::validate($_POST['API_PASSWORD']);
        $store_id = Core::validate($_POST['STORE_ID']);
        $store_password = Core::validate($_POST['STORE_PASSWORD']);
        $checksum_key = Core::validate($_POST['CHECKSUM_KEY']);
        $currency_name = Core::validate($_POST['CURRENCY']);
        $share = Core::validate($_POST['SHARE']); // percent

        if ($email == null || $api_id == null || $api_password == null || $store_id == null || $store_password == null || $checksum_key == null || $currency_name == null ||
            $share == null || !Core::isDouble($share))
        {
            print 'Incorrect data';
            exit;
        }

        $currency = new Currency();
        if (!$currency->findBy(array('Name' => $currency_name)))
        {
            exit;
        }

        $result = WalletsEgop::findBy(array(
            'currency_id'   => $currency->getId(),
            'email'         => $email,
        ));
        if (!empty($result))
        {
            print 'This wallet already exists';
            exit;
        }

        $oAuth = new EgoPayAuth($email, $api_id, $api_password);
        $oEgoPayJsonAgent = new EgoPayJsonApiAgent($oAuth);
        $oEgoPayJsonAgent->setVerifyPeer(false);
        try
        {
            $balance = $oEgoPayJsonAgent->getBalance($currency->getName());
        }
        catch (EgoPayApiException $e)
        {
            Core::write_log(EGOPAY_LOG_PATH, __FUNCTION__ . ' : getBalance Error: ' . $e->getCode() . " : " . $e->getMessage());
            print $e->getMessage();
            exit;
        }

        $wallet = new WalletsEgop();
        $wallet->setCurrencyId($currency->getId());
        $wallet->setEmail($email);
        $wallet->setApiId($api_id);
        $wallet->setApiPassword($api_password);
        $wallet->setStoreId($store_id);
        $wallet->setStorePassword($store_password);
        $wallet->setChecksumKey($checksum_key);
        $wallet->setValue($balance);
        $wallet->setShare($share / 100.0);
        $wallet->insert();

        header('Location: /admin/egop');
    }

    static public function localization()
    {
        $localizationList = Localization::getAllData();
        $languages = Localization::availableLanguages();

        Core::runView('ViewTemplate/account', array(
            'pageName'          =>  'Localization',
            'activeMenu'        =>  'Localization',
            'pagePath'          =>  'Administration/admin_localization',
            'user'              =>  self::$user,
            'localizationList'  =>  $localizationList,
            'languages'         =>  $languages
        ));
    }

    static public function addLocalizationRecord()
    {
        $newRow = array();
        foreach($_POST as $key=>$value)
        {
            $text = Core::validate($value);
            if ($text == null)
                return;

            if ($key != 'EN')
                $newRow[$key] = urlencode($text);
            else
                $newRow[$key] = $text;
        }

        Localization::insert($newRow);
        header("Location: /admin/localization");
    }

    static public function updateLocalizationRow()
    {
        $id = Core::validate(self::getVar('id'));
        $lang = Core::validate(self::getVar('lang'));
        $phrase = Core::validate(self::getVar('phrase'));

        if ($lang != 'EN')
            $phrase = urlencode($phrase);

        Localization::update($id, array($lang => $phrase));
    }

    static public function io_fees()
    {
        $io_fees = CurrencyPaymentSystem::getAll();

        Core::runView('ViewTemplate/account', array(
            'pageName'          =>  'Input/Output fees',
            'activeMenu'        =>  'Input/Output fees',
            'pagePath'          =>  'Administration/admin_io_fees',
            'user'              =>  self::$user,
            'io_fees'           =>  $io_fees
        ));

    }

    static public function update_io_fee()
    {
        $id = Core::validate(self::getVar('id'));
        $fieldName = Core::validate(self::getVar('fieldName'));
        $value = Core::validate(self::getVar('value'));

        $goodInput = false;
        switch ($fieldName)
        {
            case 'input_fee':
            case 'input_min':
            case 'output_fee':
            case 'output_min':
                if ($value != null && Core::isDouble($value) && $value >= 0)
                    $goodInput = true;
                break;

            case 'input_max':
            case 'output_max':
                if ($value == null || (Core::isDouble($value) && $value >= 0))
                {
                    $goodInput = true;
                    if ($value == null)
                    {
                        $value = "NULL";
                    }
                }
                break;

            default:
                break;
        }

        if ($goodInput == true)
        {
            CurrencyPaymentSystem::update($id, array($fieldName => $value));
            Core::printSuccessJson('');
        }
        else
        {
            Core::printErrorJson('Invalid input: ' . $value);
        }
    }

    static public function internal_fees()
    {
        $currency = new Currency();

        $rates = Rate::getAll();
        foreach ($rates as $key=>$value)
        {
            $currency->findById($value['FirstId']);
            $rates[$key]['firstCurrency'] = $currency->getName();
            $currency->findById($value['SecondId']);
            $rates[$key]['secondCurrency'] = $currency->getName();
        }

        Core::runView('ViewTemplate/account', array(
            'pageName'          =>  'Internal fees',
            'activeMenu'        =>  'Internal fees',
            'pagePath'          =>  'Administration/admin_internal_fees',
            'user'              =>  self::$user,
            'rates'             =>  $rates
        ));
    }

    static public function update_internal_fees()
    {
        $id = Core::validate(self::getVar('id'));
        $fieldName = Core::validate(self::getVar('fieldName'));
        $value = Core::validate(self::getVar('value'));

        $goodInput = false;
        switch ($fieldName)
        {
            case 'Fee':
                if ($value != null && Core::isDouble($value) && $value >= 0)
                    $goodInput = true;
                break;

            default:
                break;
        }

        if ($goodInput == true)
        {
            $rate = new Rate();
            $rate->setId($id);
            $rate->update(array($fieldName => $value));
            Core::printSuccessJson('');
        }
        else
        {
            Core::printErrorJson('Invalid input: ' . $value);
        }
    }

    static public function order_settings()
    {
        $currencies = Currency::getAll();

        Core::runView('ViewTemplate/account', array(
            'pageName'          =>  'Order settings',
            'activeMenu'        =>  'Order settings',
            'pagePath'          =>  'Administration/admin_order_settings',
            'user'              =>  self::$user,
            'currencies'        =>  $currencies
        ));
    }

    static public function update_order_settings()
    {
        $id = Core::validate(self::getVar('id'));
        $fieldName = Core::validate(self::getVar('fieldName'));
        $value = Core::validate(self::getVar('value'));

        $goodInput = false;
        switch ($fieldName)
        {
            case 'min_order_amount':
                if ($value != null && Core::isDouble($value) && $value >= 0)
                    $goodInput = true;
                break;

            default:
                break;
        }

        if ($goodInput == true)
        {
            $currency = new Currency();
            $currency->setId($id);
            $currency->update(array($fieldName => $value));
            Core::printSuccessJson($fieldName . " " . $value);
        }
        else
        {
            Core::printErrorJson('Invalid input: ' . $value);
        }
    }

    static public function addNews()
    {
        $title = array();
        $full = array();

        $title['EN'] = Core::validate(self::getVar('titleEN'));
        $title['RU'] = Core::validate(self::getVar('titleRU'));
        $title['ES'] = Core::validate(self::getVar('titleES'));

        $full['EN'] = Core::validate(self::getVar('fullEN'));
        $full['RU'] = Core::validate(self::getVar('fullRU'));
        $full['ES'] = Core::validate(self::getVar('fullES'));


        $news = new News();
        $date = new DateTime();


        $news->setNewsid($news->getLastNewsId() + 1);
        $news->setDate($date->format('Y-m-d H:i:s'));

        // English
        $news->setLang('EN');
        $news->setTitle($title['EN']);
        $news->setFull($full['EN']);
        $news->insert();

        // Russian
        $news->setLang('RU');
        $news->setTitle($title['RU']);
        $news->setFull($full['RU']);
        $news->insert();


        // Spanish
        $news->setLang('ES');
        $news->setTitle($title['ES']);
        $news->setFull($full['ES']);
        $news->insert();

        header('Location: /admin/news ');
    }

    static public function addNewYMWallet()
    {
        $number = Core::validate(self::getVar('number'));
        $client_id = Core::validate(self::getVar('client_id'));
        $secret_id = Core::validate(self::getVar('secret_id'));

        $redirectPage = 'http://emonex.info/money/YM_transaction';

        $scope =
            "account-info " .
            "payment-p2p " .
            "payment-shop";

        $authUri = YandexMoney::authorizeUri($client_id, $redirectPage, $scope);
        header('Location: ' . $authUri);
        exit();

    }

    static public function saveOrderPriority()
    {
        $to1 = self::getVar('to1');
        $from1 = self::getVar('from1');

        $to2 = self::getVar('to2');
        $from2 = self::getVar('from2');

        $to3 = self::getVar('to3');
        $from3 = self::getVar('from3');

        $to4 = self::getVar('to4');
        $from4 = self::getVar('from4');

        $priorityEntity = new OrderPriority();

        $priorityEntity->setId(1);
        $priorityEntity->setPriority(1);
        $priorityEntity->setFrom($from1);
        $priorityEntity->setTo($to1);

        $priorityEntity->save();

        $priorityEntity->setId(2);
        $priorityEntity->setPriority(2);
        $priorityEntity->setFrom($from2);
        $priorityEntity->setTo($to2);

        $priorityEntity->save();



        $priorityEntity->setId(3);
        $priorityEntity->setPriority(3);
        $priorityEntity->setFrom($from3);
        $priorityEntity->setTo($to3);

        $priorityEntity->save();


        $priorityEntity->setId(4);
        $priorityEntity->setPriority(4);
        $priorityEntity->setFrom($from4);
        $priorityEntity->setTo($to4);

        $priorityEntity->save();

        header("Location: /admin/order_priority");
        return;

    }

    static public function order_priority()
    {
        $priorityEntity = new OrderPriority();
        $priority = $priorityEntity->getAll();

        Core::runView('ViewTemplate/account', array(
            'pageName'          =>  'Order priority',
            'activeMenu'        =>  'Order priority',
            'pagePath'          =>  'Administration/admin_order_priority',
            'user'              =>  self::$user,
            'priority'          =>  $priority
        ));
    }

}
