<?php

class Order extends MainEntity
{
    private $id;
    private $userId;
    private $rateId;
    private $type;
    private $price;
    private $volume;
    private $date;
    private $part = 0.0;
    private $status = OrderStatus::ACTIVE;

    static public function findBy($where, $orderBy = '')
    {
        $whereQuery = array();
        foreach($where as $key=>$value)
        {
            array_push($whereQuery, $key . "='" . $value . "'");
        }
        $whereString = implode(" AND ", $whereQuery);

        $query = "SELECT * FROM `Order`
                    WHERE " . $whereString . $orderBy . ";";
        $result = self::queryAll($query);
        return $result;
    }

    public function findById($id)
    {
        $query = "SELECT * FROM `Order` WHERE id='$id';";
        $result = self::query($query);

        if (!empty($result))
        {
            $this->id = $id;
            $this->userId = $result['UID'];
            $this->rateId = $result['RateId'];
            $this->type = $result['Type'];
            $this->price = $result['Price'];
            $this->volume = $result['Volume'];
            $this->date = $result['Date'];
            $this->part = $result['Part'];
            $this->status = $result['Status'];
            return true;
        }
        return false;
    }

    public function insert()
    {
        $query = "INSERT INTO `Order` (UID, RateId, Type, Price, Volume, Date, Part, Status)
                    VALUES ('$this->userId', '$this->rateId', '$this->type', '$this->price', '$this->volume',
                    '$this->date', '$this->part', '$this->status');";
        self::execute($query);
        $dbh = self::getDBConnection();
        $this->id = $dbh->lastInsertId();
    }

    public function save()
    {
        $query = "UPDATE `Order` SET
                      UID='$this->userId',
                      RateId='$this->rateId',
                      Type='$this->type',
                      Price='$this->price',
                      Volume='$this->volume',
                      Date='$this->date',
                      Part='$this->part',
                      Status='$this->status'
                    WHERE id='$this->id';";
        self::execute($query);
    }

    public function delete()
    {
        $query = "DELETE FROM `Order` WHERE id='$this->id'";
        self::execute($query);
    }

    public function updatePart()
    {
        $completedDeals = Deal::findBy(array(
            'OrderId' => $this->id,
            'Done' => 1,
        ));

        $volumes = array_map(function($deals){
            return $deals['Volume'];
        }, $completedDeals);
        $volumes = array_sum($volumes);

        $this->part = $volumes / $this->volume;
    }


    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setPart($part)
    {
        $this->part = $part;
    }

    public function getPart()
    {
        return $this->part;
    }

    public function setPrice($price)
    {
        $this->price = $price;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setRateId($rateId)
    {
        $this->rateId = $rateId;
    }

    public function getRateId()
    {
        return $this->rateId;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setVolume($volume)
    {
        $this->volume = $volume;
    }

    public function getVolume()
    {
        return $this->volume;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }

    public function getDate()
    {
        return $this->date;
    }

}