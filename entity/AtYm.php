<?php

class AtYm extends MainEntity
{
    private $id;
    private $UID;
    private $wallet;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setWallet($wallet)
    {
        $this->wallet = $wallet;
    }

    public function getWallet()
    {
        return $this->wallet;
    }

    public function setUID($UID)
    {
        if (isset($UID))
        {
            $this->UID = $UID;
        }
    }

    public function getUID()
    {
        return $this->UID;
    }


    public function findById($id)
    {
        $query = "SELECT * FROM at_ym
                        WHERE id = '$id';";
        $result = self::query($query);

        if (isset($result))
        {
            $this->id = $id;
            $this->UID = $result['UID'];
            $this->wallet = $result['wallet'];
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

        $query = "SELECT * FROM at_ym
                    WHERE " . $whereString . ";";
        $result = self::queryAll($query);

        if (!empty($result))
        {
            $this->id =$result[0]['id'];
            $this->UID = $result[0]['UID'];
            $this->wallet = $result[0]['wallet'];
            return true;
        }
        return false;
    }


    public function insert()
    {
        $query = "INSERT INTO at_ym (UID, wallet)
                    VALUES ('$this->UID', '$this->wallet');";
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

        $query = "UPDATE at_ym SET " . $setString . "
                    WHERE id='$this->id';";
        self::execute($query);
    }

    public function delete()
    {
        $query = "DELETE FROM at_ym
                    WHERE id='$this->id';";
        self::execute($query);
    }

    public function getAll()
    {
        $query = "SELECT * FROM at_ym";
        return self::queryAll($query);
    }

}