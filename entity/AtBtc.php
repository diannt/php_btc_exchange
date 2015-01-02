<?php

class AtBtc extends MainEntity
{
    private $id;
    private $UID;
    private $address;
    private $type;
    private $done;
    private $transactionHash;
    private $value;
    private $timestamp;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setAddress($address)
    {
        $this->address = $address;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setDone($done)
    {
        $this->done = $done;
    }

    public function getDone()
    {
        return $this->done;
    }

    public function setUID($UID)
    {
        if (isset($UID))
        {
            $this->UID = $UID;
        }
    }

    public function getUID()
    {
        return $this->UID;
    }

    public function setTransactionHash($transactionHash)
    {
        if (isset($transactionHash))
        {
            $this->transactionHash = $transactionHash;
        }
    }

    public function getTransactionHash()
    {
        return $this->transactionHash;
    }

    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }


    public function findById($id)
    {
        $query = "SELECT * FROM at_btc
                        WHERE id = '$id';";
        $result = self::query($query);

        if (isset($result))
        {
            $this->id = $id;
            $this->UID = $result['UID'];
            $this->address = $result['address'];
            $this->type = $result['type'];
            $this->done = $result['done'];
            $this->transactionHash = $result['transaction_hash'];
            $this->value = $result['value'];
            $this->timestamp = $result['timestamp'];
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

        $query = "SELECT * FROM at_btc
                    WHERE " . $whereString . ";";
        $result = self::queryAll($query);

        if (!empty($result))
        {
            $this->id =$result[0]['id'];
            $this->UID = $result[0]['UID'];
            $this->address = $result[0]['address'];
            $this->type = $result[0]['type'];
            $this->done = $result[0]['done'];
            $this->transactionHash = $result[0]['transaction_hash'];
            $this->value = $result[0]['value'];
            $this->timestamp = $result[0]['timestamp'];
            return true;
        }
        return false;
    }

    public function findAllByForLastPeriod($input) // current day
    {
        $currentGMTTime = Core::timestamp_gmp();
        $currentGMTDate = gmdate('Y-m-d');

        $whereQuery = array();
        foreach($input as $key=>$value)
        {
            array_push($whereQuery, $key . "='" . $value . "'");
        }
        $whereString = implode(" AND ", $whereQuery);

        $query = "SELECT * FROM at_btc
                    WHERE " . $whereString . "
                        AND (timestamp BETWEEN " . $currentGMTTime . " AND " . $currentGMTDate . ");";
        return self::queryAll($query);
    }


    public function insert()
    {
        $query = "INSERT INTO at_btc (UID, address, type, done, transaction_hash, value, timestamp)
                    VALUES ('$this->UID', '$this->address', '$this->type', '$this->done', '$this->transactionHash', '$this->value', '$this->timestamp');";
        self::execute($query);
        $dbh = self::getDBConnection();
        $this->id = $dbh->lastInsertId();
    }

    public function save()
    {
        $query = "UPDATE at_btc
                    SET UID='$this->UID', address='$this->address', type='$this->type', done='$this->done',
                      transaction_hash='$this->transactionHash', value='$this->value', timestamp='$this->timestamp'
                    WHERE id='$this->id';";
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

        $query = "UPDATE at_btc SET " . $setString . "
                    WHERE id='$this->id';";
        self::execute($query);
    }

    public function delete()
    {
        $query = "DELETE FROM at_btc
                    WHERE id='$this->id';";
        self::execute($query);
    }
}