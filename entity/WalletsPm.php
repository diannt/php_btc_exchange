<?php

class WalletsPm extends MainEntity
{
    private $id;
    private $account_id;
    private $pass_phrase;
    private $alternate_pass_phrase;
    private $units;
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

    public function setPassPhrase($pass_phrase)
    {
        $this->pass_phrase = $pass_phrase;
    }

    public function getPassPhrase()
    {
        return $this->pass_phrase;
    }

    public function setAlternatePassPhrase($alternate_pass_phrase)
    {
        $this->alternate_pass_phrase = $alternate_pass_phrase;
    }

    public function getAlternatePassPhrase()
    {
        return $this->alternate_pass_phrase;
    }

    public function setUnits($units)
    {
        $this->units = $units;
    }

    public function getUnits()
    {
        return $this->units;
    }

    public function setAccountId($account_id)
    {
        $this->account_id = $account_id;
    }

    public function getAccountId()
    {
        return $this->account_id;
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
        $query = "SELECT * FROM wallets_pm
            WHERE id = '$id';";
        $result = self::query($query);

        if (isset($result))
        {
            $this->id = $id;
            $this->account_id = $result['account_id'];
            $this->pass_phrase = $result['pass_phrase'];
            $this->alternate_pass_phrase = $result['alternate_pass_phrase'];
            $this->units = $result['units'];
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

        $query = "SELECT * FROM wallets_pm
                    WHERE " . $whereString . ";";
        $result = self::queryAll($query);
        return $result;
    }

    public function insert()
    {
        $query = "INSERT INTO wallets_pm (account_id, pass_phrase, alternate_pass_phrase, units, account, value, share, profit)
                    VALUES ('$this->account_id', '$this->pass_phrase', '$this->alternate_pass_phrase', '$this->units', '$this->account', '$this->value', '$this->share', '$this->profit');";
        self::execute($query);
        $dbh = self::getDBConnection();
        $this->id = $dbh->lastInsertId();
    }

    public function save()
    {
        $query = "UPDATE wallets_pm
                    SET
                      account_id='$this->account_id',
                      pass_phrase='$this->pass_phrase',
                      alternate_pass_phrase='$this->alternate_pass_phrase',
                      units='$this->units',
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

        $query = "UPDATE wallets_pm SET " . $setString . "
                    WHERE id='$this->id';";
        self::execute($query);
    }

    public function delete()
    {
        $query = "DELETE FROM wallets_pm
                    WHERE id='$this->id';";
        self::execute($query);
    }

    static public function getAllWallets()
    {
        $query = "SELECT * FROM wallets_pm;";
        $result = self::queryAll($query);
        return $result;
    }
}