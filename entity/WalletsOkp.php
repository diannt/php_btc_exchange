<?php

class WalletsOkp extends MainEntity
{
    private $id;
    private $email;
    private $wallet_id;
    private $api_password;
    private $currency;
    private $value;
    private $share;
    private $profit;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setWalletId($wallet_id)
    {
        $this->wallet_id = $wallet_id;
    }

    public function getWalletId()
    {
        return $this->wallet_id;
    }

    public function setApiPassword($api_password)
    {
        $this->api_password = $api_password;
    }

    public function getApiPassword()
    {
        return $this->api_password;
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setShare($share)
    {
        $this->share = $share;
    }

    public function getShare()
    {
        return $this->share;
    }

    public function setProfit($profit)
    {
        $this->profit = $profit;
    }

    public function getProfit()
    {
        return $this->profit;
    }


    public function findById($id)
    {
        $query = "SELECT * FROM wallets_okp
            WHERE id = '$id';";
        $result = self::query($query);

        if (isset($result))
        {
            $this->id = $id;
            $this->email = $result['email'];
            $this->wallet_id = $result['wallet_id'];
            $this->api_password = $result['api_password'];
            $this->currency = $result['currency'];
            $this->value = $result['value'];
            $this->share = $result['share'];
            $this->profit = $result['profit'];
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

        $query = "SELECT * FROM wallets_okp
                    WHERE " . $whereString . ";";
        $result = self::queryAll($query);
        return $result;
    }

    public function insert()
    {
        $query = "INSERT INTO wallets_okp (email, wallet_id, api_password, currency, value, share, profit)
                    VALUES ('$this->email', '$this->wallet_id', '$this->api_password', '$this->currency', '$this->value', '$this->share', '$this->profit');";
        self::execute($query);
        $dbh = self::getDBConnection();
        $this->id = $dbh->lastInsertId();
    }

    public function save()
    {
        $query = "UPDATE wallets_okp
                    SET
                      email='$this->email',
                      wallet_id='$this->wallet_id',
                      api_password='$this->api_password',
                      currency='$this->currency',
                      value='$this->value',
                      share='$this->share',
                      profit='$this->profit'
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

        $query = "UPDATE wallets_okp SET " . $setString . "
                    WHERE id='$this->id';";
        self::execute($query);
    }

    public function delete()
    {
        $query = "DELETE FROM wallets_okp
                    WHERE id='$this->id';";
        self::execute($query);
    }

    static public function getAllWallets()
    {
        $query = "SELECT * FROM wallets_okp;";
        $result = self::queryAll($query);
        return $result;
    }
}