<?php

class Deal extends MainEntity
{
    private $id;
    private $orderId;
    private $price;
    private $type;
    private $rateId;
    private $volume;
    private $userId;
    private $date;
    private $done;

    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }

    public function getOrderId()
    {
        return $this->orderId;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setPrice($price)
    {
        $this->price = $price;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setRateId($rateId)
    {
        $this->rateId = $rateId;
    }

    public function getRateId()
    {
        return $this->rateId;
    }

    public function setVolume($volume)
    {
        $this->volume = $volume;
    }

    public function getVolume()
    {
        return $this->volume;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDone($done)
    {
        $this->done = $done;
    }

    public function getDone()
    {
        return $this->done;
    }

    public function findById($id)
    {
        $query = "SELECT * FROM Deal
                    WHERE id = '$id';";
        $result = self::query($query);

        if (!empty($result))
        {
            $this->id = $id;
            $this->orderId = $result['OrderId'];
            $this->price = $result['Price'];
            $this->type = $result['Type'];
            $this->rateId = $result['RateId'];
            $this->volume = $result['Volume'];
            $this->userId = $result['UID'];
            $this->date = $result['Date'];
            $this->done = $result['Done'];
            return true;
        }
        return false;
    }


    public function findActiveDealByOrderId($orderId)
    {
        $query = "SELECT * FROM Deal WHERE OrderId='$orderId' AND Done='0';";
        $result = self::query($query);

        if (!empty($result))
        {
            $this->id = $result['id'];
            $this->orderId = $result['OrderId'];
            $this->price = $result['Price'];
            $this->type = $result['Type'];
            $this->rateId = $result['RateId'];
            $this->volume = $result['Volume'];
            $this->userId = $result['UID'];
            $this->date = $result['Date'];
            $this->done = $result['Done'];
            return true;
        }
        return false;
    }

    static public function findByRate($id, $limit = null)
    {
        $query = "SELECT * FROM Deal
                    WHERE RateId = '$id'
                    ORDER BY Price DESC";
        $query .= ($limit != null) ? " LIMIT $limit;" : ";";

        $result = self::queryAll($query);

        $data = array();
        $row = array();

        foreach($result as $value)
        {
            $row['id'] = $value['id'];
            $row['Price'] = $value['Price'];
            $row['Type'] = $value['Type'];
            $row['Volume'] = $value['Volume'];

            array_push($data, $row);
        }

        return $data;
    }

    public function findLastDealByRate($id)
    {
        $query = "SELECT * FROM Deal
                    WHERE RateId = '$id'
                    ORDER BY Date DESC;";
        $result = self::queryAll($query);

        if (!empty($result))
        {
            $this->id = $result[0]['id'];
            $this->price = $result[0]['Price'];
            $this->type = $result[0]['Type'];
            $this->rateId = $result[0]['RateId'];
            $this->volume = $result[0]['Volume'];
            $this->userId = $result[0]['UID'];
            $this->date = $result[0]['Date'];
            $this->done = $result[0]['Done'];
        }
    }

    static public function findBy($input, $limit = null)
    {
        $whereQuery = array();
        foreach($input as $key=>$value)
        {
            array_push($whereQuery, $key . "='" . $value . "'");
        }
        $whereString = implode(" AND ", $whereQuery);

        $query = "SELECT * FROM `Deal`
                    WHERE " . $whereString;
        if (isset($input['Type']))
        {
            $query .= " ORDER BY Price " . (($input['Type'] == 1) ? "ASC" : "DESC");
        }
        $query .= ($limit != null) ? " LIMIT $limit;" : ";";

        $result = self::queryAll($query);
        return $result;
    }

    static public function findByAndOrderByDate($input, $limit = null)
    {
        $whereQuery = array();
        foreach($input as $key=>$value)
        {
            array_push($whereQuery, $key . "='" . $value . "'");
        }
        $whereString = implode(" AND ", $whereQuery);

        $query = "SELECT * FROM `Deal`
                    WHERE " . $whereString . " ORDER BY Date DESC";
        $query .= ($limit != null) ? " LIMIT $limit;" : ";";

        $result = self::queryAll($query);
        return $result;
    }


    static public function getDealsForPeriod($rateId, $from, $to)
    {
        $query = "SELECT * FROM `Deal`
                    WHERE
                      RateId='$rateId' AND
                      Done='1' AND
                      Date >= '$from'AND
                      Date < '$to'
                      ORDER BY DATE ASC;";

        $result = self::queryAll($query);
        return $result;
    }

    static public function getOpenedSellDeals($price, $rateId)
    {
        $query = "SELECT * FROM `Deal`
                    WHERE Price <= '$price' AND RateId='$rateId' AND Done='0' AND Type='1'
                    ORDER BY Price ASC, Date ASC;";
        $result = self::queryAll($query);
        return $result;
    }

    static public function getOpenedBuyDeals($price, $rateId)
    {
        $query = "SELECT * FROM `Deal`
                    WHERE Price >= '$price' AND RateId='$rateId' AND Done='0' AND Type='0'
                    ORDER BY Price DESC, Date ASC;";
        $result = self::queryAll($query);
        return $result;
    }

    static public function getHistory($params)
    {
        $query = "SELECT * FROM `Deal` ";
        $where = "WHERE Done='1'";

        $order = "DESC";
        if ($params['order'] != null)
            $order = $params['order'];

        $from_id = $params['from_id'];
        if ($from_id != null)
            $where .= " AND id >= '$from_id''";

        $end_id = $params['end_id'];
        if ($end_id != null)
            $where .= " AND id <= '$end_id''";

        $rate_id = $params['RateId'];
        if ($rate_id != null)
            $where .= " AND RateId = '$rate_id'";

        $since = $params['since'];
        if ($since != null)
        {
            $where .= " AND Date >= '$since''";
            $order = "ASC";
        }

        $end = $params['end'];
        if ($end != null)
        {
            $where .= " AND Date <= '$end''";
            $order = "ASC";
        }

        $query .= $where;
        $query .= " ORDER BY DATE " . $order;

        $count = $params['count'];
        if ($count != null)
            $query .= " LIMIT $count";
        $query .= ";";

        $result = self::queryAll($query);
        return $result;
    }

    public function insert()
    {
        $query = "INSERT INTO Deal (OrderId, Price, Type, RateId, Volume, UID, Date, Done)
                    VALUES ('$this->orderId', '$this->price', '$this->type', '$this->rateId', '$this->volume', '$this->userId',
                    '$this->date', '$this->done');";
        self::execute($query);
        $dbh = self::getDBConnection();
        $this->id = $dbh->lastInsertId();
    }

    public function update($input)
    {
        $setQuery = array();
        foreach($input as $key=>$value)
        {
            array_push($setQuery, $key . "='" . $value . "'");
        }
        $setString = implode(", ", $setQuery);

        $query = "UPDATE Deal SET " . $setString . "
                    WHERE id='$this->id';";
        self::execute($query);
    }

    public function save()
    {
        $query = "UPDATE Deal SET OrderId='$this->orderId', Price='$this->price', Type='$this->type', RateId='$this->rateId',
                      Volume='$this->volume', UID='$this->userId', Date='$this->date', Done='$this->done'
                    WHERE id='$this->id';";
        self::execute($query);
    }

    public function delete()
    {
        $query = "DELETE FROM Deal
                    WHERE id='$this->id'";
        self::execute($query);
    }
}