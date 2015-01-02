<?php

class WalletsLtc extends MainEntity
{
    private $id;
    private $account;
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

    public function setAccount($account)
    {
        $this->account = $account;
    }

    public function getAccount()
    {
        return $this->account;
    }

    public function setProfit($profit)
    {
        $this->profit = $profit;
    }

    public function getProfit()
    {
        return $this->profit;
    }

    public function setShare($share)
    {
        $this->share = $share;
    }

    public function getShare()
    {
        return $this->share;
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
        $query = "SELECT * FROM wallets_ltc
            WHERE id = '$id';";
        $result = self::query($query);

        if (isset($result))
        {
            $this->id = $id;
            $this->account = $result['account'];
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

        $query = "SELECT * FROM wallets_ltc
                    WHERE " . $whereString . ";";
        $result = self::queryAll($query);
        return $result;
    }

    public function insert()
    {
        $query = "INSERT INTO wallets_ltc (account, value, share, profit)
                    VALUES ('$this->account', '$this->value', '$this->share', '$this->profit');";
        self::execute($query);
        $dbh = self::getDBConnection();
        $this->id = $dbh->lastInsertId();
    }

    public function save()
    {
        $query = "UPDATE wallets_ltc
                    SET
                      account='$this->account',
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

        $query = "UPDATE wallets_ltc SET " . $setString . "
                    WHERE id='$this->id';";
        self::execute($query);
    }

    public function delete()
    {
        $query = "DELETE FROM wallets_ltc
                    WHERE id='$this->id';";
        self::execute($query);
    }

    static public function getAllWallets()
    {
        $query = "SELECT * FROM wallets_ltc;";
        $result = self::queryAll($query);
        return $result;
    }

}