<?php

class AtEgop extends MainEntity
{
    private $id;
    private $UID;
    private $our_wallet_id;
    private $client_account;
    private $amount;
    private $currency_id;
    private $type;
    private $status;
    private $timestamp;
    private $transaction_id;


    public function findById($id)
    {
        $query = "SELECT * FROM at_egop
                        WHERE id = '$id';";
        $result = self::query($query);

        if (isset($result))
        {
            $this->id =$result['id'];
            $this->UID = $result['UID'];
            $this->our_wallet_id = $result['our_wallet_id'];
            $this->currency_id = $result['currency_id'];
            $this->type = $result['type'];
            $this->status = $result['status'];
            $this->timestamp = $result['timestamp'];
            $this->client_account = $result['client_account'];
            $this->amount = $result['amount'];
            $this->transaction_id = $result['transaction_id'];
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

        $query = "SELECT * FROM at_egop
                    WHERE " . $whereString . ";";
        $result = self::queryAll($query);

        if (isset($result))
        {
            $this->id = $result[0]['id'];
            $this->UID = $result[0]['UID'];
            $this->our_wallet_id = $result[0]['our_wallet_id'];
            $this->currency_id = $result[0]['currency_id'];
            $this->type = $result[0]['type'];
            $this->status = $result[0]['status'];
            $this->timestamp = $result[0]['timestamp'];
            $this->client_account = $result[0]['client_account'];
            $this->amount = $result[0]['amount'];
            $this->transaction_id = $result[0]['transaction_id'];
            return true;
        }
        return false;
    }

    public function findAllByForLastPeriod($input) // current day
    {
        $currentGMTTime = Core::timestamp_gmp();
        $currentGMTDate = gmdate('Y-m-d');

        $whereQuery = array();
        foreach($input as $key=>$value)
        {
            array_push($whereQuery, $key . "='" . $value . "'");
        }
        $whereString = implode(" AND ", $whereQuery);

        $query = "SELECT * FROM at_egop
                    WHERE " . $whereString . "
                        AND (timestamp BETWEEN " . $currentGMTTime . " AND " . $currentGMTDate . ");";
        return self::queryAll($query);
    }

    public function insert()
    {
        $query = "INSERT INTO at_egop (UID, our_wallet_id, currency_id, `type`, status, `timestamp`, client_account, amount, transaction_id)
                    VALUES ('$this->UID', '$this->our_wallet_id', '$this->currency_id', '$this->type', '$this->status', '$this->timestamp', '$this->client_account', '$this->amount', '$this->transaction_id');";
        self::execute($query);
        $dbh = self::getDBConnection();
        $this->id = $dbh->lastInsertId();
    }

    public function update($input)
    {
        $setQuery = array();
        foreach($input as $key=>$value)
        {
            array_push($setQuery, $key . "='" . $value . "'");
        }
        $setString = implode(", ", $setQuery);

        $query = "UPDATE at_egop SET " . $setString . "
                    WHERE id='$this->id';";
        self::execute($query);
    }

    public function save()
    {
        $query = "UPDATE at_egop
                    SET
                        UID='$this->UID',
                        our_wallet_id='$this->our_wallet_id',
                        currency_id='$this->currency_id',
                        `type`='$this->type',
                        status='$this->status',
                        `timestamp`='$this->timestamp',
                        client_account='$this->client_account',
                        amount='$this->amount',
                        transaction_id='$this->transaction_id'
                    WHERE id='$this->id';";
        self::execute($query);
    }

    public function delete()
    {
        $query = "DELETE FROM at_egop
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

    public function setUID($UID)
    {
        $this->UID = $UID;
    }

    public function getUID()
    {
        return $this->UID;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setTransactionId($transaction_id)
    {
        $this->transaction_id = $transaction_id;
    }

    public function getTransactionId()
    {
        return $this->transaction_id;
    }

    public function setOurWalletId($our_wallet_id)
    {
        $this->our_wallet_id = $our_wallet_id;
    }

    public function getOurWalletId()
    {
        return $this->our_wallet_id;
    }

    public function setClientAccount($client_account)
    {
        $this->client_account = $client_account;
    }

    public function getClientAccount()
    {
        return $this->client_account;
    }

    public function setCurrencyId($currency_id)
    {
        $this->currency_id = $currency_id;
    }

    public function getCurrencyId()
    {
        return $this->currency_id;
    }


}