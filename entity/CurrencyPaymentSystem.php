<?php

class CurrencyPaymentSystem extends MainEntity
{
    private $id;
    private $currencyId;
    private $systemId;
    private $systemFee;
    private $inputFee;
    private $inputMin;
    private $inputMax;
    private $outputFee;
    private $outputMin;
    private $outputMax;

    static public function availablePaymentSystems($currencyName)
    {
        $query = "SELECT currency_payment_system.id,
                        Currency.name as 'currencyName',
                        payment_system.name,
                        payment_system.trade_name,
                        payment_system.URL,
                        currency_payment_system.system_fee,
                        currency_payment_system.input_fee,
                        currency_payment_system.output_fee
                    FROM currency_payment_system,
                        Currency,
                        payment_system
                    WHERE Currency.name = '$currencyName' AND
                        currency_payment_system.cur_id=Currency.id AND
                        payment_system.id = currency_payment_system.system_id;";
        $result = self::queryAll($query);
        return $result;
    }

    static public function getAll()
    {
        $query = "SELECT currency_payment_system.id,
                        Currency.name as 'currencyName',
                        payment_system.name,
                        payment_system.trade_name,
                        payment_system.URL,
                        currency_payment_system.system_fee,
                        currency_payment_system.input_fee,
                        currency_payment_system.input_min,
                        currency_payment_system.input_max,
                        currency_payment_system.output_fee,
                        currency_payment_system.output_min,
                        currency_payment_system.output_max
                    FROM currency_payment_system,
                        Currency,
                        payment_system
                    WHERE currency_payment_system.cur_id=Currency.id AND
                        payment_system.id = currency_payment_system.system_id;";
        $result = self::queryAll($query);
        return $result;
    }

    static public function update($id, $input)
    {
        $setQuery = array();
        foreach($input as $key=>$value)
        {
            array_push($setQuery, $key . "=" . $value);
        }
        $setString = implode(", ", $setQuery);

        $query = "UPDATE currency_payment_system SET " . $setString . "
                    WHERE id='$id';";
        self::execute($query);
    }


    public function findBy($input)
    {
        $whereQuery = array();
        foreach($input as $key=>$value)
        {
            array_push($whereQuery, $key . "='" . $value . "'");
        }
        $whereString = implode(" AND ", $whereQuery);

        $query = "SELECT * FROM currency_payment_system
                    WHERE " . $whereString . ";";
        $result = self::queryAll($query);

        if (!empty($result))
        {
            $this->id = $result[0]['id'];
            $this->currencyId = $result[0]['cur_id'];
            $this->systemId = $result[0]['system_id'];
            $this->systemFee = $result[0]['system_fee'];
            $this->inputFee = $result[0]['input_fee'];
            $this->inputMin = $result[0]['input_min'];
            $this->inputMax = $result[0]['input_max'];
            $this->outputFee = $result[0]['output_fee'];
            $this->outputMin = $result[0]['output_min'];
            $this->outputMax = $result[0]['output_max'];
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

    public function setCurrencyId($currencyId)
    {
        $this->currencyId = $currencyId;
    }

    public function getCurrencyId()
    {
        return $this->currencyId;
    }

    public function setSystemId($systemId)
    {
        $this->systemId = $systemId;
    }

    public function getSystemId()
    {
        return $this->systemId;
    }

    public function setSystemFee($systemFee)
    {
        $this->systemFee = $systemFee;
    }

    public function getSystemFee()
    {
        return $this->systemFee;
    }

    public function setInputFee($inputFee)
    {
        $this->inputFee = $inputFee;
    }

    public function getInputFee()
    {
        return $this->inputFee;
    }

    public function setOutputFee($outputFee)
    {
        $this->outputFee = $outputFee;
    }

    public function getOutputFee()
    {
        return $this->outputFee;
    }

    public function setInputMax($inputMax)
    {
        $this->inputMax = $inputMax;
    }

    public function getInputMax()
    {
        return $this->inputMax;
    }

    public function setInputMin($inputMin)
    {
        $this->inputMin = $inputMin;
    }

    public function getInputMin()
    {
        return $this->inputMin;
    }

    public function setOutputMax($outputMax)
    {
        $this->outputMax = $outputMax;
    }

    public function getOutputMax()
    {
        return $this->outputMax;
    }

    public function setOutputMin($outputMin)
    {
        $this->outputMin = $outputMin;
    }

    public function getOutputMin()
    {
        return $this->outputMin;
    }




}