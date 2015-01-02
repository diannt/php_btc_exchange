<?php

class AtOkp extends MainEntity
{
    private $id;
    private $UID;
    private $payee_email;
    private $amount;
    private $currency;
    private $type;
    private $timestamp;
    private $payer_email;
    private $transaction_id;
    private $status;

    public function findById($id)
    {
        $query = "SELECT * FROM at_okp
                        WHERE id = '$id';";
        $result = self::query($query);

        if (isset($result))
        {
            $this->id =$result['id'];
            $this->UID = $result['UID'];
            $this->payee_email = $result['payee_email'];
            $this->currency = $result['currency'];
            $this->type = $result['type'];
            $this->status = $result['status'];
            $this->timestamp = $result['timestamp'];
            $this->payer_email = $result['payer_email'];
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

        $query = "SELECT * FROM at_okp
                    WHERE " . $whereString . ";";
        $result = self::queryAll($query);

        if (isset($result))
        {
            $this->id =$result[0]['id'];
            $this->UID = $result[0]['UID'];
            $this->payee_email = $result[0]['payee_email'];
            $this->currency = $result[0]['currency'];
            $this->type = $result[0]['type'];
            $this->status = $result[0]['status'];
            $this->timestamp = $result[0]['timestamp'];
            $this->payer_email = $result[0]['payer_email'];
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

        $query = "SELECT * FROM at_okp
                    WHERE " . $whereString . "
                        AND (timestamp BETWEEN " . $currentGMTTime . " AND " . $currentGMTDate . ");";
        $result = self::queryAll($query);
        return $result;
    }

    public function insert()
    {
        $query = "INSERT INTO at_okp (UID, payee_email, currency, `type`, status, `timestamp`, payer_email, amount, transaction_id)
                    VALUES ('$this->UID', '$this->payee_email', '$this->currency', '$this->type', '$this->status', '$this->timestamp', '$this->payer_email', '$this->amount', '$this->transaction_id');";
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

        $query = "UPDATE at_okp SET " . $setString . "
                    WHERE id='$this->id';";
        self::execute($query);
    }

    public function save()
    {
        $query = "UPDATE at_okp
                    SET
                        UID='$this->UID',
                        payee_email='$this->payee_email',
                        currency='$this->currency',
                        `type`='$this->type',
                        status='$this->status',
                        `timestamp`='$this->timestamp',
                        payer_email='$this->payer_email',
                        amount='$this->amount',
                        transaction_id='$this->transaction_id'
                    WHERE id='$this->id';";
        self::execute($query);
    }

    public function delete()
    {
        $query = "DELETE FROM at_okp
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

    public function setPayerEmail($payer_email)
    {
        $this->payer_email = $payer_email;
    }

    public function getPayerEmail()
    {
        return $this->payer_email;
    }

    public function setPayeeEmail($payee_email)
    {
        $this->payee_email = $payee_email;
    }

    public function getPayeeEmail()
    {
        return $this->payee_email;
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    public function getCurrency()
    {
        return $this->currency;
    }



}