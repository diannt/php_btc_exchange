<?php

class usr extends MainController
{
    static public function register()
    {
        $email = Core::validate(self::getVar('email'));
        $pass = Core::validate(self::getVar('password'));
        $captcha = Core::validate(self::getVar('captcha'));

        if ($email == null || $pass == null || $captcha == null)
        {
            Core::printErrorJson('Incorrect data input');
            return;
        }

        $right_code = Session::getSessionVariable('security_code');
        Session::unsetSessionVariable('security_code');

        if ($captcha != $right_code)
        {
            Core::printErrorJson('Incorrect captcha');
            return;
        }

        if (!Core::isEmailAddress($email))
        {
            Core::printErrorJson('Incorrect email');
            return;
        }

        if(User::isExist($email, $email))
        {
            Core::printErrorJson('User ' . $email . ' is already registered.');
            return;
        }

        $usr = new User();
        $usr->setLogin($email);
        $usr->setEmail($email);
        $usr->setDate(date("Y-m-d H:i:s"));
        $usr->setActivation(0);
        $usr->setPassHash(Core::calculateHash($pass));
        $usr->insert();

        $activationCode = self::calcActivationCode($usr);
        $activationUrl = "http://" . $_SERVER['SERVER_NAME'] . "/usr/activation?login=" . urlencode($email) . "&code=" . $activationCode;

        $subject = Core::translateToCurrentLocale("Registration confirmation") . ".";
        $header = '<h1>'. Core::translateToCurrentLocale("Hello") .', </h1>
        <p class="lead">'. Core::translateToCurrentLocale("you have registered on the Bitmonex website") .'.</p>'.
        '<p>' . Core::translateToCurrentLocale("Your login is") . ': ' . $email . '</p><p>' . Core::translateToCurrentLocale("Your password is") . ': ' . $pass . '</p>';
        $body = '<p>'.Core::translateToCurrentLocale("To confirm your registration, please click on this link"). '. <a href="'.$activationUrl.'">'.Core::translateToCurrentLocale("Activate").'!</a></p>';

        $message = self::getMessage($header, $body);


        if (!Core::send_mail($email, $subject, $message))
        {
            $usr->delete();
            Core::printErrorJson('Notification email is not send.');
            return;
        }

        $result['success'] = 1;
        print json_encode($result);
    }

    static public function activation()
    {
        //User::removeNonActivatedUsers(3600); // for last hour

        $login = urldecode(Core::validate(self::getVar('login')));
        $code = Core::validate(self::getVar('code'));

        $nonActivatedUser = User::findBy(array('Login' => $login, 'Activation' => 0));
        if (empty($nonActivatedUser))
        {
            header('Location: /'); // too late
            exit;
        }

        $usr = new User();
        $usr->findById($nonActivatedUser[0]['id']);

        $rightCode = self::calcActivationCode($usr);
        if ($code != $rightCode)
        {
            header('Location: /');
            exit;
        }

        $usr->update(array('Activation' => 1));

        self::createEmptyPursesFor($usr->getId());

        $session = new Session();
        $session->create($usr->getId(), Core::getClientIP());

        header('Location: /usr/mypage/');
    }

    static private function calcActivationCode(User $user)
    {
        return Core::calculateHash($user->getId() . $user->getLogin());
    }

    static private function createEmptyPursesFor($userID)
    {
        $purse = new Purse();
        $purse->setUserId($userID);
        $purse->setValue(0.0);

        $allCurrencies = Currency::getAll();
        foreach($allCurrencies as $currency)
        {
            $purse->setCurrencyId($currency['id']);
            $purse->insert();
        }
    }

    static public function login()
    {
        $login = Core::validate(self::getVar('login'));
        $pass = Core::validate(self::getVar('pass'));

        $usr = new User();
        $userExistResult = $usr->isUserExist($login, Core::calculateHash($pass));
        if(!$userExistResult)
        {
            header("Location: /");
            return false;
        }

        $session = new Session();
        if ($session->isSessionExistByUserId($usr->getId()))
        {
            $session->delete();
        }

        $session->create($usr->getId(), Core::getClientIP());

        header('Location: /usr/mypage/');
    }


    static public function logout()
    {
        $session_id = Core::validate($_COOKIE['PHPSESSID']);
        if ($session_id == null)
        {
            return;
        }

        $session = new Session();
        if ($session->isSessionExist(Core::getClientIP()))
        {
            $session->delete();
        }

        header('Location: /');
    }

    static public function getCurrentUser($security = 0)
    {
        if (!$security)
            return;

        $session_id = Core::validate($_COOKIE['PHPSESSID']);
        if ($session_id == null)
            return null;

        $clientIP = Core::getClientIP();

        $session = new Session();
        $session->findBySessionId(Core::calculateHash($session_id . $clientIP));

        $usr = new User();
        $usr->findById($session->getUserId());

        $login = $usr->getLogin();
        if(isset($login))
            return $usr;
    }

    static private function getUserPurses($userId)
    {
        $session_id = Core::validate($_COOKIE['PHPSESSID']);
        if ($session_id == null)
            return null;

        $purse = new Purse();
        $purseStorage = $purse->findBy(array(
            'UID' => $userId,
        ));

        $curr = new Currency();

        foreach($purseStorage as $key=>$value)
        {
            $curr->findById($value['CurId']);
            $purseStorage[$key]['CurName'] = $curr->getName();
            $purseStorage[$key]['tradeName'] = $curr->getTradeName();
            $purseStorage[$key]['minOrderAmount'] = $curr->getMinOrderAmount();
        }

        return $purseStorage;
    }

    static public function getCurrentUsersPurses()
    {
        $usr = self::getCurrentUser(1);
        if(!isset($usr))
        {
            header("Location: /");
            return;
        }

        $usrPurs = self::getUserPurses($usr->getId());

        return $usrPurs;
    }

    static public function getUserDealsHistory($userId, $security = 0)
    {
        if(!$security)
            return;

        $session_id = Core::validate($_COOKIE['PHPSESSID']);
        if ($session_id == null)
            return null;

        $deals = Deal::findByAndOrderByDate(array(
            'UID' =>$userId,
        ));

        $rate = new Rate();
        $currency = new Currency();

        foreach ($deals as $key=>$value)
        {
            $rate->findById($value['RateId']);
            $currency->findById($rate->getFirstCurrencyId());
            $deals[$key]['FirstCurrency'] = $currency->getName();
            $currency->findById($rate->getSecondCurrencyId());
            $deals[$key]['SecondCurrency'] = $currency->getName();
        }

        return $deals;
    }

    static public function submitFeedback()
    {
        $captcha = Core::validate(self::getVar('captcha'));

        $right_code = Session::getSessionVariable('security_code');
        Session::unsetSessionVariable('security_code');

        if ($captcha != $right_code)
        {
            Core::printErrorJson('Incorrect captcha');
            exit;
        }

        $usr = self::getCurrentUser(1);
        if(!isset($usr))
        {
            header('Location: /');
            exit;
        }

        $feedback = array();
        $feedback['type'] = Core::validate(self::getVar('trouble-type'));
        $feedback['message'] = Core::validate(self::getVar('trouble'));
        $feedback['email'] = Core::validate(self::getVar('email'));

        $fbModel = new Feedback();
        $fbModel->setUID($usr->getId());
        $fbModel->setType($feedback['type']);
        $fbModel->setMessage($feedback['message']);
        $fbModel->setEmail($feedback['email']);
        $fbModel->insert();

        Core::printSuccessJson('Your ticket is active now!');
    }

    static public function getUserActiveTickets($userId, $security = 0)
    {
        if(!$security)
            return;

        $feedback = new Feedback();
        $feedback->setUID($userId);
        $result = $feedback->getAllUserTickets();

        return $result;
    }

    static public function index()
    {
        $usr = self::getCurrentUser(1);

        Core::runView('Shared/index', array(
            'user'  => $usr,
        ));
    }

    static public function mypage()
    {
        $usr = self::getCurrentUser(1);
        if(!isset($usr))
        {
            header("Location: /");
            return;
        }

        $usrPurs = self::getUserPurses($usr->getId());

        Core::runView('ViewTemplate/account', array(
            'pageName'          => 'My wallets',
            'activeMenu'        => 'Finances',
            'pagePath'          => 'AccountProfile/usr_mypage',
            'user'              =>  $usr,
            'purses'            =>  $usrPurs
        ));
    }

    static public function history()
    {
        $usr = self::getCurrentUser(1);
        if(!isset($usr))
        {
            header("Location: /");
            return;
        }

        $orders = Order::findBy(array('UID' => $usr->getId()), ' ORDER BY Date DESC');

        $rate = new Rate();
        $currency = new Currency();

        foreach ($orders as $key=>$order)
        {
            $rate->findById($order['RateId']);
            $currency->findById($rate->getFirstCurrencyId());
            $orders[$key]['FirstCurrency'] = $currency->getName();
            $currency->findById($rate->getSecondCurrencyId());
            $orders[$key]['SecondCurrency'] = $currency->getName();

            $orders[$key]['Type'] = ($order['Type'] == OrderType::BUY) ? 'Buy' : 'Sell';

            $status = '';
            switch($order['Status'])
            {
                case OrderStatus::ACTIVE:
                    $status = 'Active';
                    break;
                case OrderStatus::DONE:
                    $status = 'Done';
                    break;
                case OrderStatus::PARTIALLY_DONE:
                    $status = 'Partially done';
                    break;
                case OrderStatus::CANCELLED:
                    $status = 'Cancelled';
                    break;
            }
            $orders[$key]['Status'] = $status;

            $orderDeals = Deal::findByAndOrderByDate(array('OrderId' => $order['id']));
            $deals = array();
            foreach ($orderDeals as $dealKey=>$deal)
            {
                $deals[$dealKey]['id'] = $deal['id'];
                $deals[$dealKey]['Price'] = $deal['Price'];
                $deals[$dealKey]['Volume'] = $deal['Volume'];
                $deals[$dealKey]['Date'] = $deal['Date'];
                $deals[$dealKey]['Status'] = ($deal['Done'] == 0) ? 'Active' : 'Done';
            }
            $orders[$key]['deals'] = $deals;
        }

        Core::runView('ViewTemplate/account', array(
            'pageName'          =>  'Deals history',
            'activeMenu'        =>  'Deals history',
            'pagePath'          =>  'AccountProfile/usr_dealshistory',
            'user'              =>  $usr,
            'dealsHistory'      =>  $orders
        ));
    }


    static public function feedback()
    {
        $usr = self::getCurrentUser(1);
        if(!isset($usr))
        {
            header("Location: /");
            return;
        }

        $currentTicket = self::getUserActiveTickets($usr->getId(), 1);

        Core::runView('ViewTemplate/account', array(
                'pageName'          => 'Feedback',
                'activeMenu'        => 'Feedback',
                'pagePath'          => 'AccountProfile/usr_feedback',
                'user'              =>  $usr,
                'currentTicket'     =>  $currentTicket
        ));
    }

    static function getMessage($header, $body)
    {
        ob_start();
        ob_implicit_flush(false);
        require(VIEW_PATH.'Shared/email.php');
        return ob_get_clean();
    }

}
