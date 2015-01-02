<?php

class AtPm extends MainEntity
{
    private $id;
    private $UID;
    private $payeeAccount;
    private $amount;
    private $units;
    private $type;
    private $timestamp;
    private $payerAccount;
    private $batchNum;
    private $status;

    public function findById($id)
    {
        $query = "SELECT * FROM at_pm
                        WHERE id = '$id';";
        $result = self::query($query);

        if (isset($result))
        {
            $this->id =$result['id'];
            $this->UID = $result['UID'];
            $this->payeeAccount = $result['payee_account'];
            $this->amount = $result['amount'];
            $this->units = $result['units'];
            $this->units = $result['type'];
            $this->timestamp = $result['timestamp'];
            $this->payerAccount = $result['payer_account'];
            $this->batchNum = $result['batch_num'];
            $this->status = $result['status'];
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

        $query = "SELECT * FROM at_pm
                    WHERE " . $whereString . ";";
        $result = self::queryAll($query);

        if (isset($result))
        {
            $this->id =$result[0]['id'];
            $this->UID = $result[0]['UID'];
            $this->payeeAccount = $result[0]['payee_account'];
            $this->amount = $result[0]['amount'];
            $this->units = $result[0]['units'];
            $this->units = $result[0]['type'];
            $this->timestamp = $result[0]['timestamp'];
            $this->payerAccount = $result[0]['payer_account'];
            $this->batchNum = $result[0]['batch_num'];
            $this->status = $result[0]['status'];
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

        $query = "SELECT * FROM at_pm
                    WHERE " . $whereString . "
                        AND (timestamp BETWEEN " . $currentGMTTime . " AND " . $currentGMTDate . ");";
        $result = self::queryAll($query);
        return $result;
    }

    public function insert()
    {
        $query = "INSERT INTO at_pm (UID, amount, payee_account, units, type, `timestamp`, payer_account, batch_num, status)
                    VALUES ('$this->UID', '$this->amount', '$this->payeeAccount', '$this->units', '$this->type', '$this->timestamp', '$this->payerAccount', '$this->batchNum', '$this->status');";
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

        $query = "UPDATE at_pm SET " . $setString . "
                    WHERE id='$this->id';";
        self::execute($query);
    }

    public function save()
    {
        $query = "UPDATE at_pm
                    SET
                        UID='$this->UID',
                        payee_account='$this->payeeAccount',
                        amount='$this->amount',
                        units='$this->units',
                        `type`='$this->type',
                        `timestamp`='$this->timestamp',
                        payer_account='$this->payerAccount',
                        batch_num='$this->batchNum',
                        status='$this->status'
                    WHERE id='$this->id';";
        self::execute($query);
    }

    public function delete()
    {
        $query = "DELETE FROM at_pm
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

    public function setPayeeAccount($payeeAccount)
    {
        $this->payeeAccount = $payeeAccount;
    }

    public function getPayeeAccount()
    {
        return $this->payeeAccount;
    }

    public function setPayerAccount($payerAccount)
    {
        $this->payerAccount = $payerAccount;
    }

    public function getPayerAccount()
    {
        return $this->payerAccount;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setUnits($units)
    {
        $this->units = $units;
    }

    public function getUnits()
    {
        return $this->units;
    }

    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }

    public function setBatchNum($batchNum)
    {
        $this->batchNum = $batchNum;
    }

    public function getBatchNum()
    {
        return $this->batchNum;
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



}