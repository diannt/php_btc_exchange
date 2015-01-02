<?php

abstract class OrderType
{
    const BUY = 0;
    const SELL = 1;
}

abstract class OrderStatus
{
    const ACTIVE = 0;
    const DONE = 1;
    const PARTIALLY_DONE = 2;
    const CANCELLED = 3;
}

class OrderModel
{
    static private $errorMessage = '';

    static public function getErrorMessage()
    {
        return self::$errorMessage;
    }

    static public function createOrder($userId, Rate $rate, $type, $volume, $price)
    {
        $order = new Order();
        $order->setUserId($userId);
        $order->setRateId($rate->getId());
        $order->setType($type);
        $order->setPrice($price);
        $order->setVolume($volume);
        $order->setDate(date("Y-m-d H:i:s"));
        $order->insert();

        if (!self::takePrepayment($order, $rate))
        {
            self::$errorMessage = 'You do not have funds';
            return false;
        }

        $deal = self::createDealForOrder($order);
        self::makeDealsWith($deal);
        self::refreshRatePrices($rate);

        return $order->getId();
    }

    static public function cancelOrder(Order $order)
    {
        if ($order->getStatus() != OrderStatus::ACTIVE)
        {
            self::$errorMessage = "The order cannot be canceled";
            return false;
        }

        $deal = new Deal();
        $deal->findActiveDealByOrderId($order->getId());

        $rate = new Rate();
        $rate->findById($order->getRateId());

        if ($order->getType() == OrderType::BUY)
        {
            $purse = self::getPurse($order->getUserId(), $rate->getSecondCurrencyId());
            $refund = $deal->getVolume() * $deal->getPrice() * (1.0 + $rate->getFee());
        }
        else
        {
            $purse = self::getPurse($order->getUserId(), $rate->getFirstCurrencyId());
            $refund = $deal->getVolume();
        }
        $purse->addValue($refund);
        $purse->save();
        $deal->delete();

        if ($order->getPart() == 0)
        {
            $order->setStatus(OrderStatus::CANCELLED);
        }
        else
        {
            $order->setStatus(OrderStatus::PARTIALLY_DONE);
        }
        $order->setDate(date("Y-m-d H:i:s"));
        $order->save();

        self::refreshRatePrices($rate);

        return true;
    }

    static public function changeOrderPrice(Order $order, $newPrice)
    {
        if (($order->getStatus() != OrderStatus::ACTIVE) ||
            ($newPrice == $order->getPrice()))
        {
            return false;
        }

        $rate = new Rate();
        $rate->findById($order->getRateId());

        $deal = new Deal();
        $deal->findActiveDealByOrderId($order->getId());

        if ($order->getType() == OrderType::BUY) // in this case we need to return or withdraw more money
        {
            $purseSelect = Purse::findBy(array(
                'UID' => $order->getUserId(),
                'CurId' => $rate->getSecondCurrencyId()
            ));
            $purse = new Purse();
            $purse->findById($purseSelect[0]['id']);

            $difference = ($order->getPrice() - $newPrice) * $deal->getVolume() * (1.0 + $rate->getFee());
            $purse->addValue($difference);

            if ($purse->getValue() < 0)
            {
                return false;
            }
            $purse->save();
        }

        $deal->setDate(date("Y-m-d H:i:s"));
        $deal->setPrice($newPrice);
        $deal->save();

        $order->setPrice($newPrice);
        $order->save();

        self::makeDealsWith($deal);
        self::refreshRatePrices($rate);

        return true;
    }

    static public function changeOrderVolume(Order $order, $newVolume)
    {
        if (($order->getStatus() != OrderStatus::ACTIVE) ||
            ($newVolume == $order->getVolume()))
        {
            return false;
        }

        $deal = new Deal();
        $deal->findActiveDealByOrderId($order->getId());
        $volumeDifference = $order->getVolume() - $newVolume;

        if ($deal->getVolume() < $volumeDifference)
        {
            return false;
        }

        $rate = new Rate();
        $rate->findById($order->getRateId());
        $purse = new Purse();

        if ($order->getType() == OrderType::BUY)
        {
            $purseSelect = Purse::findBy(array(
                'UID' => $order->getUserId(),
                'CurId' => $rate->getSecondCurrencyId()
            ));
            $purse->findById($purseSelect[0]['id']);
            $difference = $order->getPrice() * $volumeDifference * (1.0 + $rate->getFee());
            $purse->addValue($difference);
        }
        else
        {
            $purseSelect = Purse::findBy(array(
                'UID' => $order->getUserId(),
                'CurId' => $rate->getFirstCurrencyId()
            ));
            $purse->findById($purseSelect[0]['id']);
            $purse->addValue($volumeDifference);
        }

        if ($purse->getValue() < 0)
        {
            return false;
        }
        $purse->save();

        $deal->setDate(date("Y-m-d H:i:s"));
        $deal->setVolume($newVolume);
        $deal->save();

        $order->setVolume($newVolume);
        $order->updatePart();
        $order->save();

        return true;
    }

    static private function takePrepayment(Order $order, Rate $rate)
    {
        $firstPurse = self::getPurse($order->getUserId(), $rate->getFirstCurrencyId());
        $secondPurse = self::getPurse($order->getUserId(), $rate->getSecondCurrencyId());

        if ($order->getType() == OrderType::BUY)
        {
            $requiredVolume = ($order->getPrice() * $order->getVolume()) * (1.0 + $rate->getFee());
            if (($secondPurse == null) || ($secondPurse->getValue() < $requiredVolume))
            {
                return false;
            }
            $secondPurse->addValue(-$requiredVolume);
            $secondPurse->save();
        }
        else
        {
            if (($firstPurse == null) || ($firstPurse->getValue() < $order->getVolume()))
            {
                return false;
            }
            $firstPurse->addValue(-$order->getVolume());
            $firstPurse->save();
        }

        return true;
    }

    static private function createDealForOrder(Order $order)
    {
        $deal = new Deal();
        $deal->setOrderId($order->getId());
        $deal->setDate($order->getDate());
        $deal->setRateId($order->getRateId());
        $deal->setPrice($order->getPrice());
        $deal->setVolume($order->getVolume());
        $deal->setUserId($order->getUserId());
        $deal->setType($order->getType());
        $deal->setDone(0);
        $deal->insert();

        return $deal;
    }

    static private function makeDealsWith(Deal $deal)
    {
        $order = new Order();
        $order->findById($deal->getOrderId());

        if ($order->getType() == OrderType::SELL)
            $oppositeDeals = Deal::getOpenedBuyDeals($order->getPrice(), $order->getRateId());
        else
            $oppositeDeals = Deal::getOpenedSellDeals($order->getPrice(), $order->getRateId());

        foreach ($oppositeDeals as $oppDeal)
        {
            $oppositeDeal = new Deal();
            $oppositeDeal->findById($oppDeal['id']);
            $oppositeOrder = new Order();
            $oppositeOrder->findById($oppositeDeal->getOrderId());

            $difference = $deal->getVolume() - $oppositeDeal->getVolume();
            if ($difference == 0)
            {
                $deal->setPrice($oppositeDeal->getPrice());
                $deal->setDone(1);
                $deal->save();
                $order->setPart(1.0);
                $order->setStatus(OrderStatus::DONE);
                $order->save();

                $oppositeDeal->setDone(1);
                $oppositeDeal->setDate($deal->getDate());
                $oppositeDeal->save();

                $oppositeOrder->setPart(1.0);
                $oppositeOrder->setStatus(OrderStatus::DONE);
                $oppositeOrder->setDate($deal->getDate());
                $oppositeOrder->save();

                self::transferMoney($deal, $oppositeDeal);
                break;
            }
            $intermediateDeal = new Deal();
            $intermediateDeal->setRateId($order->getRateId());
            $intermediateDeal->setDate($deal->getDate());
            $intermediateDeal->setDone(1);

            if ($difference < 0)
            {
                $deal->setPrice($oppositeDeal->getPrice());
                $deal->setDone(1);
                $deal->save();
                $order->setPart(1.0);
                $order->setStatus(OrderStatus::DONE);
                $order->save();

                $intermediateDeal->setOrderId($oppositeOrder->getId());
                $intermediateDeal->setUserId($oppositeOrder->getUserId());
                $intermediateDeal->setType($oppositeOrder->getType());
                $intermediateDeal->setPrice($oppositeDeal->getPrice());
                $intermediateDeal->setVolume($deal->getVolume());
                $intermediateDeal->insert();

                $oppositeOrder->updatePart();
                $oppositeOrder->setDate($deal->getDate());
                $oppositeOrder->save();

                $oppositeDeal->setVolume(-$difference);
                $oppositeDeal->setDate($deal->getDate());
                $oppositeDeal->save();

                self::transferMoney($deal, $intermediateDeal);
                break;
            }
            else
            {
                $deal->setVolume($difference);
                $deal->save();

                $intermediateDeal->setOrderId($order->getId());
                $intermediateDeal->setUserId($order->getUserId());
                $intermediateDeal->setType($order->getType());
                $intermediateDeal->setPrice($oppositeDeal->getPrice());
                $intermediateDeal->setVolume($oppositeDeal->getVolume());
                $intermediateDeal->insert();

                $order->updatePart();
                $order->save();

                $oppositeDeal->setDone(1);
                $oppositeDeal->setDate($deal->getDate());
                $oppositeDeal->save();

                $oppositeOrder->setPart(1.0);
                $oppositeOrder->setStatus(OrderStatus::DONE);
                $oppositeOrder->setDate($deal->getDate());
                $oppositeOrder->save();

                self::transferMoney($intermediateDeal, $oppositeDeal);
            }
        }
    }

    static private function transferMoney(Deal $deal1, Deal $deal2)
    {
        if (($deal1->getType() == 1) && ($deal2->getType() == 0))
        {
            $sellDeal = $deal1;
            $buyDeal = $deal2;
        }
        else if (($deal1->getType() == 0) && ($deal2->getType() == 1))
        {
            $sellDeal = $deal2;
            $buyDeal = $deal1;
        }
        else
        {
            return false;
        }

        $rate = new Rate();
        $rate->findById($sellDeal->getRateId());

        $sellOrder = new Order();
        $sellOrder->findById($sellDeal->getOrderId());

        $buyOrder = new Order();
        $buyOrder->findById($buyDeal->getOrderId());

        $sellerSecondCurrencyPurse = self::getPurseOrMakeNew($sellOrder->getUserId(), $rate->getSecondCurrencyId());
        $buyerFirstCurrencyPurse = self::getPurseOrMakeNew($buyOrder->getUserId(), $rate->getFirstCurrencyId());
        $buyerSecondCurrencyPurse = self::getPurseOrMakeNew($buyOrder->getUserId(), $rate->getSecondCurrencyId());

        $firstCurVolume = $sellDeal->getVolume();
        $buyerFirstCurrencyPurse->addValue($firstCurVolume);
        $buyerFirstCurrencyPurse->save();

        $secondCurVolume = $firstCurVolume * $sellDeal->getPrice();
        $sellerSecondCurrencyPurse->addValue($secondCurVolume * (1.0 - $rate->getFee()));

        $refund = ($firstCurVolume * $buyOrder->getPrice() - $secondCurVolume) * (1.0 + $rate->getFee());
        if ($buyOrder->getUserId() != $sellOrder->getUserId())
        {
            $sellerSecondCurrencyPurse->save();
            $buyerSecondCurrencyPurse->addValue($refund);
            $buyerSecondCurrencyPurse->save();
        }
        else // this is the case when user sells currency himself (user is moron)
        {
            $sellerSecondCurrencyPurse->addValue($refund);
            $sellerSecondCurrencyPurse->save();
        }

        return true;
    }

    static private function refreshRatePrices(Rate $rate)
    {
        $where['RateId'] = $rate->getId();
        $where['Done'] = 0;
        $where['Type'] = OrderType::BUY;
        $buyDeals = Deal::findBy($where);

        $where['Type'] = OrderType::SELL;
        $sellDeals = Deal::findBy($where);

        $getOnlyPrices = function($deals){
            return $deals['Price'];
        };

        $isUpdated = false;
        if (!empty($buyDeals))
        {
            $buyPrices = array_map($getOnlyPrices, $buyDeals);
            $bid = max($buyPrices);
            if ($rate->getBid() != $bid)
            {
                $rate->setBid($bid);
                $rate->save();
                $isUpdated = true;
            }
        }

        if (!empty($sellDeals))
        {
            $sellPrices = array_map($getOnlyPrices, $sellDeals);
            $ask = min($sellPrices);
            if ($rate->getAsk() != $ask)
            {
                $rate->setAsk($ask);
                $rate->save();
                $isUpdated = true;
            }
        }

        return $isUpdated;
    }

    static private function getPurseOrMakeNew($userId, $curId)
    {
        $purse = self::getPurse($userId, $curId);
        if ($purse != null)
        {
            return $purse;
        }

        $purse = new Purse();
        $purse->setCurrencyId($curId);
        $purse->setUserId($userId);
        $purse->setValue(0);
        $purse->insert();
        $result = Purse::findBy(array(
            'UID'   => $userId,
            'CurId' => $curId
        ));
        $purse->findById($result[0]['id']);
        return $purse;
    }

    static private function getPurse($userId, $curId)
    {
        $result = Purse::findBy(array(
            'UID'   => $userId,
            'CurId' => $curId
        ));
        if (empty($result))
        {
            return null;
        }

        $purse = new Purse();
        $purse->findById($result[0]['id']);
        return $purse;
    }

}