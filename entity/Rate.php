<?php

class Rate extends MainEntity
{
    private $id;
    private $firstCurrencyId;
    private $secondCurrencyId;
    private $bid; // buying order price
    private $ask; // selling order price
    private $fee;
    private $minPriceDifference;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setFirstCurrencyId($firstCurrencyId)
    {
        $this->firstCurrencyId = $firstCurrencyId;
    }

    public function getFirstCurrencyId()
    {
        return $this->firstCurrencyId;
    }

    public function setSecondCurrencyId($secondCurrencyId)
    {
        $this->secondCurrencyId = $secondCurrencyId;
    }

    public function getSecondCurrencyId()
    {
        return $this->secondCurrencyId;
    }

    public function setBid($bid)
    {
        $this->bid = $bid;
    }

    public function getBid()
    {
        return $this->bid;
    }

    public function setAsk($ask)
    {
        $this->ask = $ask;
    }

    public function getAsk()
    {
        return $this->ask;
    }

    public function getFee()
    {
        return $this->fee;
    }

    public function setMinPriceDifference($minPriceDifference)
    {
        $this->minPriceDifference = $minPriceDifference;
    }

    public function getMinPriceDifference()
    {
        return $this->minPriceDifference;
    }


    public function findById($id)
    {
        $query = "SELECT * FROM Rate
            WHERE id = '$id';";
        $result = self::query($query);

        if (isset($result))
        {
            $this->id = $id;
            $this->firstCurrencyId = $result['FirstId'];
            $this->secondCurrencyId = $result['SecondId'];
            $this->bid = $result['Bid'];
            $this->ask = $result['Ask'];
            $this->fee = $result['Fee'];
            $this->minPriceDifference = $result['MinPriceDifference'];
            return true;
        }
        return false;
    }

    public function findBy($input)
    {
        $whereQuery = array();
        foreach($input as $key=>$value)
        {
            array_push($whereQuery, $key . "='" . $value . "'");
        }
        $whereString = implode(" AND ", $whereQuery);

        $query = "SELECT * FROM `Rate`
                    WHERE " . $whereString . ";";
        $result = self::queryAll($query);

        if (!empty($result))
        {
            $this->id =$result[0]['id'];
            $this->firstCurrencyId = $result[0]['FirstId'];
            $this->secondCurrencyId = $result[0]['SecondId'];
            $this->bid = $result[0]['Bid'];
            $this->ask = $result[0]['Ask'];
            $this->fee = $result[0]['Fee'];
            $this->minPriceDifference = $result[0]['MinPriceDifference'];
            return true;
        }
        return false;
    }

    static public function getBestRates($limit)
    {
        $query = "SELECT
                  Deal.RateId as 'id',
                  Rate.FirstId as 'FirstId',
                  Rate.SecondId as 'SecondId',
                  SUM(Deal.Volume) as 'Volume'
                  FROM Deal, Rate
                  WHERE Deal.RateId = Rate.id
                  GROUP BY Deal.RateId
                  ORDER BY Volume DESC
                  LIMIT $limit;";
        $result = self::queryAll($query);
        return $result;
    }

    static public function getAll()
    {
        $query = "SELECT * FROM `Rate`;";
        $result = self::queryAll($query);
        return $result;
    }

    public function insert()
    {
        $query = "INSERT INTO Rate (FirstId, SecondId, Bid, Ask, Fee, MinPriceDifference)
                    VALUES ('$this->firstCurrencyId', '$this->secondCurrencyId', '$this->bid', '$this->ask', '$this->fee', '$this->minPriceDifference');";
        self::execute($query);
    }

    public function update($input)
    {
        $setQuery = array();
        foreach($input as $key=>$value)
        {
            array_push($setQuery, $key . "='" . $value . "'");
        }
        $setString = implode(", ", $setQuery);

        $query = "UPDATE Rate SET " . $setString . "
                    WHERE id='$this->id';";
        self::execute($query);
    }

    public function save()
    {
        $query = "UPDATE `Rate` SET FirstId='$this->firstCurrencyId', SecondId='$this->secondCurrencyId',
                      Bid='$this->bid', Ask='$this->ask', Fee='$this->fee', MinPriceDifference='$this->minPriceDifference'
                    WHERE id='$this->id';";
        self::execute($query);
    }

    public function delete()
    {
        $query = "DELETE FROM Rate
                    WHERE id='$this->id'";
        self::execute($query);
    }

}