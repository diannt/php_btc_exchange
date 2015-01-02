<?php

class Currency extends MainEntity
{
    private $id;
    private $name;
    private $tradeName;
    private $minOrderAmount;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setName($Name)
    {
        if (isset($name))
        {
            $this->name = $Name;
        }
    }

    public function getName()
    {
        return $this->name;
    }

    public function setTradeName($tradeName)
    {
        if (isset($tradeName))
        {
            $this->tradeName = $tradeName;
        }
    }

    public function getTradeName()
    {
        return $this->tradeName;
    }

    public function setMinOrderAmount($minOrderAmount)
    {
        $this->minOrderAmount = $minOrderAmount;
    }

    public function getMinOrderAmount()
    {
        return $this->minOrderAmount;
    }


    public function findById($id)
    {
        $query = "SELECT * FROM Currency
                    WHERE id = '$id';";
        $result = self::query($query);

        if (isset($result))
        {
            $this->id = $id;
            $this->name = $result['Name'];
            $this->tradeName = $result['tradeName'];
            $this->minOrderAmount = $result['min_order_amount'];
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

        $query = "SELECT * FROM Currency
                    WHERE " . $whereString . ";";
        $result = self::queryAll($query);

        if (!empty($result))
        {
            $this->id =$result[0]['id'];
            $this->name = $result[0]['Name'];
            $this->tradeName = $result[0]['tradeName'];
            $this->minOrderAmount = $result[0]['min_order_amount'];
            return true;
        }
        return false;
    }


    public function insert()
    {
        $query = "INSERT INTO Currency (Name, tradeName, min_order_amount)
                    VALUES ('$this->name', '$this->tradeName', '$this->minOrderAmount');";
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

        $query = "UPDATE Currency SET " . $setString . "
                    WHERE id='$this->id';";
        self::execute($query);
    }

    public function delete()
    {
        $query = "DELETE FROM Currency
                    WHERE id='$this->id';";
        self::execute($query);
    }

    public static function getAll()
    {
        $query = "SELECT * FROM `Currency`;";
        $result = self::queryAll($query);
        return $result;
    }
}