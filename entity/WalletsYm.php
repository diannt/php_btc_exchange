<?php

class WalletsYm extends MainEntity
{
    private $id;
    private $client_id;
    private $secret_id;
    private $token;
    private $value;
    private $number;

    public function setNumber($number)
    {
        $this->number = $number;
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function setClientId($client_id)
    {
        $this->client_id = $client_id;
    }

    public function getClientId()
    {
        return $this->client_id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setSecretId($secret_id)
    {
        $this->secret_id = $secret_id;
    }

    public function getSecretId()
    {
        return $this->secret_id;
    }

    public function setToken($token)
    {
        $this->token = $token;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function findById($id)
    {
        $query = "SELECT * FROM wallets_ym
            WHERE id = '$id';";
        $result = self::query($query);

        if (isset($result))
        {
            $this->id = $id;
            $this->client_id = $result['client_id'];
            $this->secret_id = $result['secret_id'];
            $this->token = $result['token'];
            $this->value = $result['value'];
            $this->number = $result['number'];
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

        $query = "SELECT * FROM `wallets_ym`
                    WHERE " . $whereString . ";";
        $result = self::queryAll($query);
        return $result;
    }

    public function insert()
    {
        $query = "INSERT INTO wallets_ym (number, client_id, secret_id, token, value)
                    VALUES ('$this->number', '$this->client_id', '$this->secret_id', '$this->token', '$this->value');";
        self::execute($query);
    }

    public function save()
    {
        $query = "UPDATE wallets_ym SET number='$this->number', client_id='$this->client_id', secret_id='$this->secret_id', token='$this->token', value='$this->value'
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

        $query = "UPDATE wallets_ym SET " . $setString . "
                    WHERE id='$this->id';";
        self::execute($query);
    }

    public function delete()
    {
        $query = "DELETE FROM wallets_ym
                    WHERE id='$this->id';";
        self::execute($query);
    }

    public function getAllWallets()
    {
        $query = "SELECT * FROM wallets_ym ";
        $result = self::queryAll($query);
        return $result;
    }





}