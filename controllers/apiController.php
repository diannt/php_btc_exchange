<?php

class api extends MainController
{
    /*
     GET/POST http://.../api/trades

     params:
         firstCurrency - first currency name
         secondCurrency - second currency name
         limit - number of the best trades to display
     returns:
         json
         [{
             "price":
             "volume":
             "trade_id":
             "currency_buy":
             "currency_sell":
             "trade_type":
         },...]
    */
    static public function trades()
    {
        $firstCurName = Core::validate(self::getVar('firstCurrency'));
        $secondCurName = Core::validate(self::getVar('secondCurrency'));
        $limit = Core::validate(self::getVar('limit'));

        $rate = self::getRate($firstCurName, $secondCurName);
        if ($rate == null)
            return;

        $result = Deal::findByRate($rate->getId(), $limit);

        $data = array();
        $row = array();
        foreach ($result as $value)
        {
            $row['price'] = $value['Price'];
            $row['volume'] = $value['Volume'];
            $row['trade_id'] = $value['id'];
            $row['currency_buy'] = $firstCurName;
            $row['currency_sell'] = $secondCurName;
            $row['trade_type'] = ($value['Type'] == 0) ? 'ask' : 'bid';

            array_push($data, $row);
        }

        $res['data'] = $data;
        print json_encode($res);
    }

    /*
     GET/POST http://.../api/ticker

     params:
         firstCurrency - first currency name
         secondCurrency - second currency name
         period - period in minutes (default 1440 - one day)
         interval - in minutes (default - 60 - one hour)
     returns:
        json
        {
            "success": 0
            "error":
        }
        or
        json
        {
            "success": 1
            "result":
            {
                [
                "high":
                "low":
                "avg":
                "first":
                "last":
                "vol":
                "vol_cur":
                "time": (unix-time)
                "ask":
                "bid":
                ]
            }
         }
    */
    static public function ticker($firstCurName = null, $secondCurName = null, $period = null, $interval = null)
    {
        $isAjax = 0;

        if ($firstCurName == null) { $firstCurName = Core::validate(self::getVar('firstCurrency')); $isAjax = 1; }
        if ($secondCurName == null) { $secondCurName = Core::validate(self::getVar('secondCurrency')); $isAjax = 1; }
        if ($period == null) { $period = Core::validate(self::getVar('period')); }
        if ($interval == null) { $interval = Core::validate(self::getVar('interval')); }

        if ($period == null)
        {
            $period = 10080;
        }

        if ($interval == null)
        {
            $interval = 1440;
        }

        if ($interval > $period)
        {
            self::printErrorJson("incorrect interval value");
            return 0;
        }

        $rate = self::getRate($firstCurName, $secondCurName);
        if ($rate == null)
        {
            self::printErrorJson("rate $firstCurName" . "_" . " $secondCurName not exist");
            return 0;
        }

        $getPrices = function($deals){
            return $deals['Price'];
        };

        $getVol = function($deals){
            if ($deals['Type'] == 0)
                return $deals['Price'] * $deals['Volume'];
            return 0;
        };

        $getVolCur = function($deals){
            if ($deals['Type'] == 1)
                return $deals['Volume'];
            return 0;
        };

        $current_date = date("Y-m-d H:i:s");
        $result = array();

        $periodCount = $period / $interval;
        for ($num = 0; $num < $periodCount; $num++)
        {
            $minutes = $period - $interval * $num;
            $from = date("Y-m-d H:i:s", strtotime("$current_date - $minutes minutes"));
            $minutes -= $interval;
            $to = date("Y-m-d H:i:s", strtotime("$current_date - $minutes minutes"));

            if (strtotime($to) > strtotime($current_date))
                $to = $current_date;

            $deals = Deal::getDealsForPeriod($rate->getId(), $from, $to);

            if (!empty($deals))
            {
                $prices = array_map($getPrices, $deals);

                $highPrice = max($prices);
                $lowPrice = min($prices);

                $avg = array_sum($prices) / count($prices);

                $firstPrice = $prices[0];
                $lastPrice = end($prices);

                $volumes = array_map($getVol, $deals);
                $vol = array_sum($volumes);

                $cur_volumes = array_map($getVolCur, $deals);
                $vol_cur = array_sum($cur_volumes);
            }
            else
            {
                if ($num != 0)
                {
                    $highPrice = $result[$num - 1]['high'];
                    $lowPrice = $result[$num - 1]['low'];
                    $firstPrice = $result[$num - 1]['last'];
                    $lastPrice = $result[$num - 1]['last'];
                }
                else
                {
                    $highPrice = $rate->getAsk();
                    $lowPrice = $rate->getBid();
                    $firstPrice = $rate->getAsk();
                    $lastPrice = $rate->getBid();
                }

                $avg = ($highPrice + $lowPrice) / 2;
                $vol = 0;
                $vol_cur = 0;
            }

            $interv['high'] = $highPrice;
            $interv['low'] = $lowPrice;
            $interv['avg'] = $avg;
            $interv['first'] = $firstPrice;
            $interv['last'] = $lastPrice;
            $interv['vol'] = $vol;
            $interv['vol_cur'] = $vol_cur;
            $interv['time'] = strtotime($to);
            $interv['ask'] = $rate->getAsk();
            $interv['bid'] = $rate->getBid();

            array_push($result, $interv);
        }

        $data['success'] = 1;
        $data['result'] = $result;

        if ($isAjax==0)
            return $result;
        print json_encode($data);
    }

    static public function graph()
    {
        $firstCurName = Core::validate(self::getVar('firstCurrency'));
        $secondCurName = Core::validate(self::getVar('secondCurrency'));
        $period = Core::validate(self::getVar('period'));
        $interval = Core::validate(self::getVar('interval'));
        $bars = Core::validate(self::getVar('bars'));

        $data = self::ticker($firstCurName, $secondCurName, $period, $interval);
        if ($data!=0)
        {
            if ($bars == null)
            {
                $result0 = array();
                $result1 = array();
                foreach ($data as $val)
                {
                    array_push($result0,array(1000*$val['time'],$val['avg']));
                    array_push($result1,array(1000*$val['time'],$val['vol']));
                }

                $result['ticks1'] = $result0;
                $result['ticks2'] = $result1;

                print json_encode($result);
            }
            if ($bars == 1)
            {
                $result0 = array();
                foreach ($data as $val)
                {
                    array_push($result0,array($val['time'],$val['first'],$val['high'],$val['low'],$val['last']));
                }

                $result['ticks1'] = $result0;

                print json_encode($result);
            }
        }
    }

    /*
     GET/POST http://.../api/depth

     params:
         firstCurrency - first currency name
         secondCurrency - second currency name
         limit (default: 30)
     returns:
         json
         [{
             "asks":[[price, volume],[price, volume]...]
             "bids":[[price, volume],[price, volume]...]
         }]
    */
    static public function depth($firstCurName = null, $secondCurName = null, $limit = null)
    {
        $isAjax = 0;
        if ($firstCurName == null) { $firstCurName = Core::validate(self::getVar('firstCurrency')); $isAjax = 1; }
        if ($secondCurName == null) { $secondCurName = Core::validate(self::getVar('secondCurrency')); $isAjax = 1; }
        if ($limit == null) $limit = Core::validate(self::getVar('limit'));



        $rate = self::getRate($firstCurName, $secondCurName);
        if ($rate == null)
            return;

        if ($limit == null)
            $limit = 30;

        $searchConditions = array();

        $searchConditions['Done'] = 0;
        $searchConditions['RateId'] = $rate->getId();
        $searchConditions['Type'] = 0;
        $buyDeals = Deal::findBy($searchConditions, $limit);

        $searchConditions['Type'] = 1;
        $sellDeals = Deal::findBy($searchConditions, $limit);

        $asks = array();
        $bids = array();

        foreach ($sellDeals as $value)
        {
            $ask[0] = $value['Price'];
            $ask[1] = $value['Volume'];
            array_push($asks, $ask);
        }

        foreach($buyDeals as $value)
        {
            $bid[0] = $value['Price'];
            $bid[1] = $value['Volume'];
            array_push($bids, $bid);
        }

        $data['asks'] = $asks;
        $data['bids'] = $bids;
        if ($isAjax == 0)
            return $data;
        $result['data'] = $data;
        print json_encode($result);
    }

    static public function rateInfo($firstCurName = null, $secondCurName = null)
    {
        $isAjax = 0;
        if ($firstCurName == null) { $firstCurName = Core::validate(self::getVar('firstCurrency')); $isAjax = 1; }
        if ($secondCurName == null) { $secondCurName = Core::validate(self::getVar('secondCurrency')); $isAjax = 1; }

        $rate = self::getRate($firstCurName, $secondCurName);
        if ($rate == null)
            return;

        $searchConditions['RateId'] = $rate->getId();
        $searchConditions['Done'] = 0;
        $searchConditions['Type'] = 0;
        $buyDeals = Deal::findBy($searchConditions);
        $searchConditions['Type'] = 1;
        $sellDeals = Deal::findBy($searchConditions);

        $priceVolume = array_map(function($deals){
            return $deals['Volume'] * $deals['Price'];
        }, $buyDeals);
        $total_price = array_sum($priceVolume);

        $volumes = array_map(function($deals){
                return $deals['Volume'];
            }, $sellDeals);
        $total_volume = array_sum($volumes);

        $data['bid'] = $rate->getBid();
        $data['ask'] = $rate->getAsk();
        $data['total_volume'] = $total_volume;
        $data['total_price'] = $total_price;
        $result['data'] = $data;
        if ($isAjax == 0)
           return $data;
        print json_encode($data);
    }



    /*
     GET/POST http://.../api/fee

     params:
         firstCurrency - first currency name
         secondCurrency - second currency name
     returns:
         json
         {
             "trade": 0.2
         }
    */
    static public function fee()
    {
        $firstCurName = Core::validate(self::getVar('firstCurrency'));
        $secondCurName = Core::validate(self::getVar('secondCurrency'));

        $rate = self::getRate($firstCurName, $secondCurName);
        if ($rate == null)
            return;

        $data['trade'] = $rate->getFee();

        print json_encode($data);
    }

    /*
     * params:
     * $firstCurName - first currency name
     * $secondCurName - second currency name
     *
     * returns:
     * Object Rate or null
     */
    static public function getRate($firstCurName, $secondCurName)
    {
        $searchConditions = array();

        $firstCurrency = new Currency();
        $searchConditions['Name'] = $firstCurName;

        if (!$firstCurrency->findBy($searchConditions))
            return null;

        $secondCurrency = new Currency();
        $searchConditions['Name'] = $secondCurName;

        if (!$secondCurrency->findBy($searchConditions))
            return null;

        $rate = new Rate();
        $searchConditions = array();
        $searchConditions['FirstId'] = $firstCurrency->getId();
        $searchConditions['SecondId'] = $secondCurrency->getId();

        if (!$rate->findBy($searchConditions))
            return null;

        return $rate;
    }

    /*
     GET/POST http://.../api/bestRates

     params:
         limit - number of the best rates to display (default 10)
     returns:
         json
         [{
             "firstCurrency":
             "secondCurrency":
             "lastPrice":
         }]
    */
    static public function bestRates()
    {
        $limit = Core::validate(self::getVar('limit'));
        if ($limit == null)
            $limit = 10;

        $bestRates = Rate::getBestRates($limit);

        $data = array();

        $currency = new Currency();
        $deal = new Deal();
        foreach ($bestRates as $rate)
        {
            $currency->findById($rate['FirstId']);
            $row['firstCurrency'] = $currency->getName();
            $currency->findById($rate['SecondId']);
            $row['secondCurrency'] = $currency->getName();
            $deal->findLastDealByRate($rate['id']);
            $row['lastPrice'] = $deal->getPrice();
            $row['id'] = $rate['id'];

            array_push($data, $row);
        }
        //$result['data'] = $data;

        return $data;
    }

    public function getAllCurrencies()
    {
        $allCurrencies = Currency::getAll();

        return $allCurrencies;
    }

    static public function rate($firstCurName, $secondCurName)
    {
      //  $firstCurName = Core::validate(self::getVar('firstCurrency'));
      //  $secondCurName = Core::validate(self::getVar('secondCurrency'));

        $rate = self::getRate($firstCurName,$secondCurName);
        if ($rate == null)
            return;
        $deal = new Deal();
        $deal->findLastDealByRate($rate->getId());
        $row = $deal->getPrice();

        return $row;
    }

    /*
     GET/POST http://.../api/trade

     params:
        firstCurrency
        secondCurrency
        type ("buy"|"sell")
        rate (price)
        amount (volume)

     returns:
         json
         {
            "success": 0
            "error":
         }
        or
        json
        {
            "success": 1
            "return":
            {
                "order_id":
                "funds":
                {
                    "(curname)": (amount),
                    "(curname)": (amount)
                    ...
                }
            }
        }
    */
    static public function trade()
    {
        $user = usr::getCurrentUser(1);
        if ($user == null)
        {
            self::printErrorJson("You are not authorized");
            return;
        }

        $firstCurName = Core::validate(self::getVar('firstCurrency'));
        $secondCurName = Core::validate(self::getVar('secondCurrency'));
        $type = Core::validate(self::getVar('type'));
        $price = Core::validate(self::getVar('rate'));
        $volume = Core::validate(self::getVar('amount'));

        if ($firstCurName == null || $secondCurName == null || $type == null || $price == null || $volume == null)
        {
            self::printErrorJson("not all parameters are defined");
            return;
        }

        if (!Core::isDouble($price) || !Core::isDouble($volume))
        {
            self::printErrorJson("parameters are defined incorrectly");
            return;
        }

        $rate = self::getRate($firstCurName, $secondCurName);
        if ($rate == null)
        {
            self::printErrorJson("rate $firstCurName" . "_" . " $secondCurName not exist");
            return;
        }

        $currency = new Currency();
        $currency->findBy(array('Name' => $firstCurName));
        if ($volume < $currency->getMinOrderAmount())
        {
            self::printErrorJson("Please, check min order amount for this currency");
            return;
        }

        if ($type == 'buy')
        {
            $orderType = OrderType::BUY;
        }
        elseif ($type = 'sell')
        {
            $orderType = OrderType::SELL;
        }
        else
        {
            self::printErrorJson("undefined order type");
            return;
        }

        $orderId = OrderModel::createOrder($user->getId(), $rate, $orderType, $volume, $price);
        if ($orderId === false)
        {
            self::printErrorJson(OrderModel::getErrorMessage());
            return;
        }

        $return['order_id'] = $orderId;
        $return['funds'] = self::getFunds($user->getId());

        $result['success'] = 1;
        $result['return'] = $return;

        print json_encode($result);
    }

    static public function priceDifference()
    {
        $orderId = Core::validate(self::getVar('orderId'));
        if ($orderId == null)
        {
            self::printErrorJson("not all parameters are defined");
            return;
        }

        $order = new Order();
        if (!$order->findById($orderId))
        {
            self::printErrorJson("order with id = '$orderId' is not exist");
            return;
        }

        $rate = new Rate();
        $rate->findById($order->getRateId());

        $result['success'] = 1;
        $result['return'] = ($order->getType() == OrderType::BUY) ? ($rate->getBid() - $order->getPrice()) : ($rate->getAsk() - $order->getPrice());

        print json_encode($result);
    }

    static private function printErrorJson($errorMessage)
    {
        $result['success'] = 0;
        $result['error'] = $errorMessage;
        print json_encode($result);
    }

    static private function getFunds($userId)
    {
        $funds = array();

        $currency = new Currency();
        $purseStorage = Purse::findBy(array(
            'UID' => $userId
        ));
        foreach ($purseStorage as $purse)
        {
            $currency->findById($purse['CurId']);
            $funds[$currency->getName()] = $purse['Value'];
        }

        return $funds;
    }

    /*
     GET/POST http://.../api/cancelOrder

     params:
        order_id

     returns:
         json{
            "success": 0
            "error":
         }
        or
        json{
            "success": 1
            "return":{
                "order_id":
                "funds":{
                    "(curname)": (amount),
                    "(curname)": (amount)
                    ...
                }
            }
        }
    */

    static public function cancelOrder()
    {
        $user = usr::getCurrentUser(1);
        if ($user == null)
        {
            self::printErrorJson("You are not authorized");
            return;
        }

        $orderId = Core::validate(self::getVar('order_id'));
        if ($orderId == null)
        {
            self::printErrorJson("parameter order_id is not defined");
            return;
        }

        $order = new Order();
        if (!$order->findById($orderId))
        {
            self::printErrorJson("order with id = '$orderId' is not exist");
            return;
        }

        if ($order->getUserId() != $user->getId())
        {
            self::printErrorJson("order cannot be canceled because it isn't your order");
            return;
        }

        $success = OrderModel::cancelOrder($order);
        if (!$success)
        {
            self::printErrorJson(OrderModel::getErrorMessage());
            return;
        }

        $return['order_id'] = $orderId;
        $return['funds'] = self::getFunds($user->getId());

        $result['success'] = 1;
        $result['return'] = $return;

        print json_encode($result);
    }

    static public function changePrice()
    {
        $user = usr::getCurrentUser(1);
        if ($user == null)
        {
            self::printErrorJson("You are not authorized");
            return;
        }

        $orderId = Core::validate(self::getVar('orderId'));
        $price = Core::validate(self::getVar('price'));
        if ($orderId == null || $price == null || !Core::isDouble($price))
        {
            self::printErrorJson("parameters are defined incorrectly");
            return;
        }

        $order = new Order();
        if (!$order->findById($orderId))
        {
            self::printErrorJson("order with id = '$orderId' is not exist");
            return;
        }

        if ($order->getUserId() != $user->getId())
        {
            self::printErrorJson("order cannot be canceled because it isn't your order");
            return;
        }

        $success = OrderModel::changeOrderPrice($order, $price);
        if (!$success)
        {
            self::printErrorJson('Price is not changed');
            return;
        }

        $return['order_id'] = $orderId;
        $return['new_price'] = $price;

        $result['success'] = 1;
        $result['return'] = $return;

        print json_encode($result);
    }

    static public function changeAmount()
    {
        $user = usr::getCurrentUser(1);
        if ($user == null)
        {
            self::printErrorJson("You are not authorized");
            return;
        }

        $orderId = Core::validate(self::getVar('orderId'));
        $volume = Core::validate(self::getVar('amount'));
        if ($orderId == null || $volume == null || !Core::isDouble($volume))
        {
            self::printErrorJson("parameters are defined incorrectly");
            return;
        }

        $order = new Order();
        if (!$order->findById($orderId))
        {
            self::printErrorJson("order with id = '$orderId' is not exist");
            return;
        }

        if ($order->getUserId() != $user->getId())
        {
            self::printErrorJson("order cannot be canceled because it isn't your order");
            return;
        }

        $rate = new Rate();
        $rate->findById($order->getRateId());

        $currency = new Currency();
        $currency->findById($rate->getFirstCurrencyId());
        if ($volume < $currency->getMinOrderAmount())
        {
            self::printErrorJson("Please, check min order amount for this currency");
            return;
        }

        $success = OrderModel::changeOrderVolume($order, $volume);
        if (!$success)
        {
            self::printErrorJson('Amount is not changed');
            return;
        }

        $return['order_id'] = $orderId;
        $return['new_amount'] = $volume;

        $result['success'] = 1;
        $result['return'] = $return;

        print json_encode($result);
    }


    /*
     GET/POST http://.../api/getInfo

     params:

     returns:
         json{
            "success": 0
            "error":
         }
        or
        json{
            "success": 1
            "return":{
                "funds":{
                    "(curname)": (amount),
                    "(curname)": (amount)
                    ...
                }
                "open_orders":
                "server_time":
            }
        }
    */
    static public function getInfo()
    {
        $user = usr::getCurrentUser(1);
        if ($user == null)
        {
            //self::printErrorJson("you are not authorized");
            return;
        }

        $return['funds'] = self::getFunds($user->getId());

        $activeOrders = Order::findBy(array(
            'UID'   => $user->getId(),
            'Status'  => OrderStatus::ACTIVE,
        ));

        $return['open_orders'] = count($activeOrders);
        $return['server_time'] = time();

        $result['success'] = 1;
        $result['return'] = $return;

        return $result;
    }

    /*
     GET/POST http://.../api/activeOrders

     params:
        firstCurrency, secondCurrency - not necessary
     returns:
         json{
            "success": 0
            "error":
         }
        or
        json{
            "success": 1
            "return":{
                "order_id":
                "pair":
                "type":
                "amount":
                "rate":
                "timestamp_created":
                "status":
            }
        }
    */
    static public function activeOrders($firstCurrencyName = null, $secondCurrencyName = null)
    {
        $user = usr::getCurrentUser(1);
        if ($user == null)
        {
            self::printErrorJson("You are not authorized");
            return;
        }

        $isAjax = 0;
        if ($firstCurrencyName == null) { $firstCurrencyName = Core::validate(self::getVar('firstCurrency')); $isAjax = 1; }
        if ($secondCurrencyName == null) { $secondCurrencyName = Core::validate(self::getVar('secondCurrency')); $isAjax = 1; }

        $searchConditions['UID'] = $user->getId();
        $searchConditions['Status'] = OrderStatus::ACTIVE;

        $rate = self::getRate($firstCurrencyName, $secondCurrencyName);
        if ($rate != null)
            $searchConditions['RateId'] = $rate->getId();
        else
            $rate = new Rate();

        $activeOrders = Order::findBy($searchConditions);

        $return = array();
        $currency = new Currency();
        foreach ($activeOrders as $value)
        {
            $order['order_id'] = $value['id'];

            $rate->findById($value['RateId']);
            $currency->findById($rate->getFirstCurrencyId());
            $order['pair'] = $currency->getName();
            $currency->findById($rate->getSecondCurrencyId());
            $order['pair'] .= " - " . $currency->getName();

            $order['type'] = ($value['Type'] == OrderType::BUY) ? "buy" : "sell";
            $order['amount'] = $value['Volume'];
            $order['rate'] = $value['Price'];
            $order['timestamp_created'] = $value['Date'];
            $order['status'] = $value['Status'];

            array_push($return, $order);
        }

        $result['success'] = 1;
        $result['return'] = $return;

        if ($isAjax == 0)
            return $result;
        print json_encode($result);
    }

    /*
     GET/POST http://.../api/tradeHistory

     params: (all not necessary)
        count - deals count limit (default: 1000)
        from_id - from deal id (default: 0)
        end_id - end deal id (default: infinity)
        order - ASC or DESC (default: DESC)
        since - since date (UNIX time) (default: 0)
        end - end date (UNIX time) (default: infinity)
        firstCurrency, secondCurrency - pair (default all pairs)
     returns:
        json{
        "success": 0
        "error":
        }
        or
        json{
            "success": 1
            "return":{
                "pair":
                "type":
                "amount":
                "rate":
                "order_id":
                "is_your_order":
                "timestamp_created":
            }
        }
    */
    static public function tradeHistory($firstCurrencyName = null, $secondCurrencyName = null, $count = null)
    {
        $user = usr::getCurrentUser(1);
        $isAjax = 0;
        if ($count == null){
            $count = Core::validate(self::getVar('count'));
        }

        $from_id = Core::validate(self::getVar('from_id'));
        $end_id = Core::validate(self::getVar('end_id'));
        $order = Core::validate(self::getVar('order'));
        $since = Core::validate(self::getVar('since'));
        $end = Core::validate(self::getVar('end'));

        if ($firstCurrencyName == null) { $firstCurrencyName = Core::validate(self::getVar('firstCurrency')); $isAjax = 1; }
        if ($secondCurrencyName == null) { $secondCurrencyName = Core::validate(self::getVar('secondCurrency')); $isAjax = 1; }

        $rate = self::getRate($firstCurrencyName, $secondCurrencyName);
        if ($rate != null)
            $params['RateId'] = $rate->getId();

        $params['count'] = $count;
        $params['from_id'] = $from_id;
        $params['end_id'] = $end_id;
        $params['order'] = $order;
        $params['since'] = ($since != null) ? date("Y-m-d H:i:s", $since) : null;
        $params['end'] = ($end != null) ? date("Y-m-d H:i:s", $end) : null;

        $deals = Deal::getHistory($params);

        $return = array();

        $rate = new Rate();
        $currency = new Currency();
        foreach ($deals as $value)
        {
            $rate->findById($value['RateId']);
            $currency->findById($rate->getFirstCurrencyId());
            $deal['pair'] = $currency->getName();
            $currency->findById($rate->getSecondCurrencyId());
            $deal['pair'] .= " - " . $currency->getName();

            $deal['type'] = ($value['Type'] == 0) ? "buy" : "sell";
            $deal['amount'] = $value['Volume'];
            $deal['rate'] = $value['Price'];
            $deal['order_id'] = $value['OrderId'];
            $deal['is_your_order'] = ($user != null && $user->getId() == $value['UID']) ? 1 : 0;
            $deal['timestamp'] = strtotime($value['Date']);

            array_push($return, $deal);
        }

        $result['success'] = 1;
        $result['return'] = $return;
        if ($isAjax == 0)
            return $result;
        print json_encode($result);
    }

    static public function SwitchLanguage()
    {
        $newLangId = Core::validate(self::getVar('langId'));
        switch($newLangId)
        {
            case(2):
                $lang = 'RU';
                break;
            case(3):
                $lang = 'ES';
                break;
            default:
                $lang = 'EN';
        }

        Session::setSessionVariable('lang', $lang);

        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }

    function checkJSLoad($var){
        if(!defined($var)){
            define($var, 1);
            return false;
        } else {
            return true;
        }
    }

    function javascriptLoad($location, $defined = ''){
        if(!$defined) { $defined = 'JS_' . $location; }

        if( !self::checkJSLoad($defined) ){
            print '<script type="text/javascript" src="'.$location.'"></script>';
        }
    }

    static public function widget_rate()
    {
        Core::runView('Widgets/widget_rate');
    }

    static public function widget_glass()
    {
        Core::runView('Widgets/widget_glass');
    }

    static public function account_balance()
    {
        Core::runView('Shared/account_balance');
    }

    static public function widget_orders()
    {
        Core::runView('Widgets/widget_orders');
    }

    static public function widget_tradehistory()
    {
        Core::runView('Widgets/widget_tradehistory');
    }

    static public function widget_specialtrading()
    {
        Core::runView('Widgets/widget_specialtrading');
    }

    static public function popup_login()
    {
        Core::runView('Shared/popup_login');
    }

    static public function popup_register()
    {
        Core::runView('Shared/popup_register');
    }

    static public function popup_widgetadd()
    {
        Core::runView('Shared/widget_add');
    }

    static public function popup_loader()
    {
        Core::runView('Shared/popup_loader');
    }

    static public function widgetpager()
    {
        Core::runView('Shared/widgetpager');
    }

    static public function main_middle()
    {
        Core::runView('Shared/main_middle');
    }

    static public function captcha()
    {
        Core::generateCaptcha();
    }

}