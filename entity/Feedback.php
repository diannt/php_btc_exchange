<?php

class Feedback extends MainEntity
{
    private $id;
    private $UID;
    private $email;
    private $message;
    private $closed;
    private $type;

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

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setClosed($closed)
    {
        $this->closed = $closed;
    }

    public function getClosed()
    {
        return $this->closed;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }


    public function findById($id)
    {
        $query = "SELECT * FROM feedback
            WHERE id = '$id';";
        $result = self::query($query);

        if (isset($result))
        {
            $this->id = $id;
            $this->UID = $result['UID'];
            $this->email = $result['email'];
            $this->message = $result['message'];
            $this->closed = $result['closed'];
            $this->type = $result['type'];
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

        $query = "SELECT * FROM `feedback`
                    WHERE " . $whereString . ";";
        $result = self::queryAll($query);
        return $result;
    }

    public function insert()
    {
        $dbh = self::getDBConnection();
        $query = "INSERT INTO feedback (UID, email, message, type)
                    VALUES ('$this->UID', '$this->email', '$this->message', '$this->type');";
        $dbh->query($query);
    }

    public function save()
    {
        $query = "UPDATE feedback SET UID='$this->UID', email='$this->email', message='$this->message', type='$this->type'
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

        $query = "UPDATE feedback SET " . $setString . "
                    WHERE id='$this->id';";
        self::execute($query);
    }

    public function delete()
    {
        $query = "DELETE FROM feedback
                    WHERE id='$this->id';";
        self::execute($query);
    }

    public function getAllUserTickets()
    {
        $query = "SELECT * FROM feedback WHERE UID=" . $this->getUID();
        $result = self::queryAll($query);
        return $result;
    }



}