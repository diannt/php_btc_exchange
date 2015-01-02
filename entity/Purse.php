<?php

class Purse extends MainEntity
{
    private $id;
    private $currencyId;
    private $value;
    private $userId;
    private $out;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setOut($out)
    {
        $this->out = $out;
    }

    public function getOut()
    {
        return $this->out;
    }

    public function setCurrencyId($currencyId)
    {
        $this->currencyId = $currencyId;
    }

    public function getCurrencyId()
    {
        return $this->currencyId;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function addValue($value)
    {
        $this->value += $value;
    }

    public function findById($id)
    {
        $query = "SELECT * FROM Purse
            WHERE id = '$id';";
        $result = self::query($query);

        if (isset($result))
        {
            $this->id = $id;
            $this->currencyId = $result['CurId'];
            $this->value = $result['Value'];
            $this->userId = $result['UID'];
            $this->out = $result['out_id'];
            return true;
        }
        return false;
    }

    static public function findBy($where)
    {
        $whereQuery = array();
        foreach($where as $key=>$value)
        {
            array_push($whereQuery, $key . "='" . $value . "'");
        }
        $whereString = implode(" AND ", $whereQuery);

        $query = "SELECT * FROM `Purse`
                    WHERE " . $whereString . ";";
        $result = self::queryAll($query);
        return $result;
    }

    public function insert()
    {
        $query = "INSERT INTO Purse (CurId, Value, UID)
                    VALUES ('$this->currencyId', '$this->value', '$this->userId');";
        self::execute($query);
    }

    public function save()
    {
        $query = "UPDATE Purse SET CurId='$this->currencyId', Value='$this->value', UID='$this->userId'
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

        $query = "UPDATE Purse SET " . $setString . "
                    WHERE id='$this->id';";
        self::execute($query);
    }

    public function delete()
    {
        $query = "DELETE FROM Purse
                    WHERE id='$this->id';";
        self::execute($query);
    }




}