<?php

class WalletsEgop extends MainEntity
{
    private $id;
    private $currency_id;
    private $email;
    private $api_id;
    private $api_password;
    private $store_id;
    private $store_password;
    private $checksum_key;

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

    public function setApiPassword($api_password)
    {
        $this->api_password = $api_password;
    }

    public function getApiPassword()
    {
        return $this->api_password;
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

    public function setStorePassword($store_password)
    {
        $this->store_password = $store_password;
    }

    public function getStorePassword()
    {
        return $this->store_password;
    }

    public function setStoreId($store_id)
    {
        $this->store_id = $store_id;
    }

    public function getStoreId()
    {
        return $this->store_id;
    }

    public function setCurrencyId($currency_id)
    {
        $this->currency_id = $currency_id;
    }

    public function getCurrencyId()
    {
        return $this->currency_id;
    }

    public function setApiId($api_id)
    {
        $this->api_id = $api_id;
    }

    public function getApiId()
    {
        return $this->api_id;
    }

    public function setChecksumKey($checksum_key)
    {
        $this->checksum_key = $checksum_key;
    }

    public function getChecksumKey()
    {
        return $this->checksum_key;
    }


    public function findById($id)
    {
        $query = "SELECT * FROM wallets_egop
            WHERE id = '$id';";
        $result = self::query($query);

        if (isset($result))
        {
            $this->id = $id;
            $this->currency_id = $result['currency_id'];
            $this->email = $result['email'];
            $this->api_id = $result['api_id'];
            $this->api_password = $result['api_password'];
            $this->store_id = $result['store_id'];
            $this->store_password = $result['store_password'];
            $this->checksum_key = $result['checksum_key'];
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

        $query = "SELECT * FROM wallets_egop
                    WHERE " . $whereString . ";";
        $result = self::queryAll($query);
        return $result;
    }

    public function insert()
    {
        $query = "INSERT INTO wallets_egop (currency_id, email, api_id, api_password, store_id, store_password, checksum_key, value, share, profit)
                    VALUES ('$this->currency_id', '$this->email', '$this->api_id', '$this->api_password', '$this->store_id', '$this->store_password', '$this->checksum_key', '$this->value', '$this->share', '$this->profit');";
        self::execute($query);
        $dbh = self::getDBConnection();
        $this->id = $dbh->lastInsertId();
    }

    public function save()
    {
        $query = "UPDATE wallets_egop
                    SET
                      currency_id='$this->currency_id',
                      email='$this->email',
                      api_id='$this->api_id',
                      api_password='$this->api_password',
                      store_id='$this->store_id',
                      store_password='$this->store_password',
                      checksum_key='$this->checksum_key',
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

        $query = "UPDATE wallets_egop SET " . $setString . "
                    WHERE id='$this->id';";
        self::execute($query);
    }

    public function delete()
    {
        $query = "DELETE FROM wallets_egop
                    WHERE id='$this->id';";
        self::execute($query);
    }

    static public function getAllWallets()
    {
        $query = "SELECT * FROM wallets_egop;";
        $result = self::queryAll($query);
        return $result;
    }

}
