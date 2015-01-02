<?php

class TrailingStop extends MainEntity
{
    private $id;
    private $orderId;
    private $x;
    private $y;

    public function findById($id)
    {
        $query = "SELECT * FROM `TrailingStop` WHERE id = '$id';";
        $result = self::query($query);

        if (isset($result))
        {
            $this->id = $id;
            $this->orderId = $result['orderId'];
            $this->x = $result['X'];
            $this->y = $result['Y'];
            return true;
        }
        return false;
    }

    public function findByOrderId($orderId)
    {
        $query = "SELECT * FROM `TrailingStop` WHERE OrderId = '$orderId';";
        $result = self::query($query);

        if (isset($result))
        {
            $this->id = $result['id'];
            $this->orderId = $orderId;
            $this->x = $result['X'];
            $this->y = $result['Y'];
            return true;
        }
        return false;
    }

    public function insert()
    {
        $query = "INSERT INTO `TrailingStop` (OrderId, X, Y)
                    VALUES ('$this->orderId', '$this->x', '$this->y');";
        self::execute($query);
        $dbh = self::getDBConnection();
        $this->id = $dbh->lastInsertId();
    }

    public function save()
    {
        $query = "UPDATE `TrailingStop` SET
                      OrderId='$this->orderId',
                      X='$this->x',
                      Y='$this->y'
                    WHERE id='$this->id';";
        self::execute($query);
    }

    public function delete()
    {
        $query = "DELETE FROM `TrailingStop`
                    WHERE id='$this->id';";
        self::execute($query);
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }

    public function getOrderId()
    {
        return $this->orderId;
    }

    public function setX($x)
    {
        $this->x = $x;
    }

    public function getX()
    {
        return $this->x;
    }

    public function setY($y)
    {
        $this->y = $y;
    }

    public function getY()
    {
        return $this->y;
    }


}