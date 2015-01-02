<?php

class OrderPriority extends MainEntity
{
    private $id;
    private $priority;
    private $from;
    private $to;
    private $color;


    public function setColor($color)
    {
        $this->color = $color;
    }

    public function getColor()
    {
        return $this->color;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setFrom($address)
    {
        $this->from = $address;
    }

    public function getFrom()
    {
        return $this->from;
    }

    public function setTo($type)
    {
        $this->to = $type;
    }

    public function getTo()
    {
        return $this->to;
    }


    public function setPriority($UID)
    {
        if (isset($UID))
        {
            $this->priority = $UID;
        }
    }

    public function getPriority()
    {
        return $this->priority;
    }

    public function findById($id)
    {
        $query = "SELECT * FROM order_priority
                        WHERE id = '$id';";
        $result = self::query($query);

        if (isset($result))
        {
            $this->id = $id;
            $this->priority = $result['priority'];
            $this->from = $result['from'];
            $this->to = $result['to'];
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

        $query = "SELECT * FROM order_priority
                    WHERE " . $whereString . ";";
        $result = self::queryAll($query);

        if (!empty($result))
        {
            $this->id =$result[0]['id'];
            $this->priority = $result[0]['priority'];
            $this->from = $result[0]['from'];
            $this->to = $result[0]['to'];
            return true;
        }
        return false;
    }

    public function insert()
    {
        $query = "INSERT INTO order_priority (priority, from, to)
                    VALUES ('$this->priority', '$this->from', '$this->to');";
        self::execute($query);
    }

    public function save()
    {
        $query = "UPDATE order_priority
                    SET `priority`='$this->priority', `from`='$this->from', `to`='$this->to'
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

        $query = "UPDATE order_priority SET " . $setString . "
                    WHERE id='$this->id';";
        self::execute($query);
    }

    public function delete()
    {
        $query = "DELETE FROM order_priority
                    WHERE id='$this->id';";
        self::execute($query);
    }

    public function getAll()
    {
        $query = 'SELECT * FROM order_priority ORDER BY priority';
        $result = self::queryAll($query);
        return $result;
    }


}