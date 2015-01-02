<?php

class Session extends MainEntity
{
    private $id;
    private $session_id;
    private $user_id;

    public function getUserId()
    {
        return $this->user_id;
    }

    public function findBySessionId($sessionId)
    {
        $query = "SELECT id, session_id, user_id FROM `Session`
                        WHERE session_id = '$sessionId';";
        $result = self::query($query);

        if (isset($result))
        {
            $this->id = $result['id'];
            $this->session_id = $result['session_id'];
            $this->user_id = $result['user_id'];
        }
    }

    public function isSessionExistByUserId($user_id)
    {
        $query = "SELECT id FROM Session
                  WHERE user_id='$user_id';";
        $result = self::query($query);

        if (!empty($result))
        {
            $this->id = $result['id'];
            return true;
        }
        return false;
    }

    public function isSessionExist($clientIP)
    {
        $session_id = self::startSession();
        $hash = Core::calculateHash($session_id . $clientIP);

        $query = "SELECT * FROM `Session`
                        WHERE session_id = '$hash';";
        $result = self::query($query);

        if (!empty($result))
        {
            $this->id = $result['id'];
            $this->user_id = $result['user_id'];
            $this->session_id = $hash;
            return true;
        }
        return false;
    }

    public function delete()
    {
        $query = "DELETE FROM `Session`
                    WHERE id='$this->id';";
        self::execute($query);
    }

    private function insert()
    {
        $query = "INSERT INTO `Session` (session_id, user_id)
                    VALUES ('$this->session_id', '$this->user_id');";
        self::execute($query);
    }

    public function create($userID, $clientIP)
    {
        $session_id = self::startSession();
        $this->session_id = Core::calculateHash($session_id . $clientIP);
        $this->user_id = $userID;

        $this->insert();
    }

    static public function startSession()
    {
        if (!session_id())
            session_start();
        return session_id();
    }

    static public function getSessionVariable($key)
    {
        self::startSession();
        if (isset($_SESSION[$key]))
            return $_SESSION[$key];
        return null;
    }

    static public function setSessionVariable($key, $value)
    {
        self::startSession();
        $_SESSION[$key] = $value;
    }

    static public function unsetSessionVariable($key)
    {
        self::startSession();
        unset($_SESSION[$key]);
    }
}