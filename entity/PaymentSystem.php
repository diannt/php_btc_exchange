<?php

class PaymentSystem extends MainEntity
{
    private $id;
    private $name;
    private $tradeName;
    private $url;

    public function findBy($input)
    {
        $whereQuery = array();
        foreach($input as $key=>$value)
        {
            array_push($whereQuery, $key . "='" . $value . "'");
        }
        $whereString = implode(" AND ", $whereQuery);

        $query = "SELECT * FROM payment_system
                    WHERE " . $whereString . ";";
        $result = self::queryAll($query);

        if (!empty($result))
        {
            $this->id =$result[0]['id'];
            $this->name = $result[0]['name'];
            $this->tradeName = $result[0]['trade_name'];
            $this->url = $result[0]['URL'];
            return true;
        }
        return false;
    }


    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setTradeName($tradeName)
    {
        $this->tradeName = $tradeName;
    }

    public function getTradeName()
    {
        return $this->tradeName;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getUrl()
    {
        return $this->url;
    }

}