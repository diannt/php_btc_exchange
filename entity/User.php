<?php

class User extends MainEntity
{
    private $id;
    private $login;
    private $email;
    private $passHash;
    private $date;
    private $activation;

    public static function isExist($login, $email)
    {
        $query = "SELECT * FROM `User` WHERE Login='$login' OR Email='$email';";
        $result = self::query($query);
        if(empty($result))
            return false;
        return true;
    }

    public function isUserExist($login, $passHash)
    {
        $query = "SELECT * FROM User
                        WHERE Login='$login' AND PassHash='$passHash' AND Activation='1';";
        $result = self::query($query);
        if(empty($result))
            return false;

        $this->login = $login;
        $this->passHash = $passHash;
        $this->id = $result['id'];
        $this->email = $result['Email'];
        $this->date = $result['Date'];
        $this->activation = $result['Activation'];
        return true;
    }

    public function getPassHash()
    {
        return $this->passHash;
    }

    public function findById($id)
    {
        $query = "SELECT id, Login, Email, PassHash FROM User
                    WHERE id = '$id';";
        $result = self::queryAll($query);
        if ($result)
        {
            $this->id = $id;
            $this->login = $result[0]['Login'];
            $this->email = $result[0]['Email'];
            $this->passHash = $result[0]['PassHash'];
            return true;
        }
        return false;
    }

    static public function findBy($input)
    {
        $whereQuery = array();
        foreach($input as $key=>$value)
        {
            array_push($whereQuery, $key . "='" . $value . "'");
        }
        $whereString = implode(" AND ", $whereQuery);

        $query = "SELECT * FROM `User` WHERE " . $whereString . ";";
        $result = self::queryAll($query);
        return $result;
    }

    static public function removeNonActivatedUsers($period) // period in seconds
    {
        $query = "DELETE FROM `User` WHERE Activation='0' AND UNIX_TIMESTAMP() - UNIX_TIMESTAMP(Date) > $period;";
        self::execute($query);
    }

    public function insert()
    {
        $query = "INSERT INTO `User` (Login, PassHash, Email, `Date`, Activation)
                    VALUES ('$this->login', '$this->passHash', '$this->email', '$this->date', '$this->activation');";
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
        $query = "UPDATE User SET " . $setString . "
                    WHERE id='$this->id';";
        self::execute($query);
    }

    public function delete()
    {
        $query = "DELETE FROM User
                    WHERE Id='$this->id';";
        self::execute($query);
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setLogin($login)
    {
        $this->login = $login;
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setPassHash($passHash)
    {
        $this->passHash = $passHash;
    }

    public function setActivation($activation)
    {
        $this->activation = $activation;
    }

    public function getActivation()
    {
        return $this->activation;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }

    public function getDate()
    {
        return $this->date;
    }
}