<?php

class MainEntity
{
    protected static $dbh;

    public function getEntity()
    {
        return $this;
    }

    public static function getDBConnection()
    {
        try
        {
            if(!isset(self::$dbh))
                self::$dbh = new PDO('mysql:host=' . MYSQL_HOST . ';dbname=' . MYSQL_DB_NAME, MYSQL_USER, MYSQL_PASSWORD);

            return self::$dbh;
        }
        catch (PDOException $e)
        {
            echo "Error: " . $e->getMessage() . "<br/>";
            die();
        }
    }

    public static function queryAll($queryBody, $fetchType=PDO::FETCH_ASSOC)
    {
        $dbc = self::getDBConnection();
        $result = $dbc->query($queryBody)->fetchAll($fetchType);
        return $result;
    }

    public static function query($queryBody, $fetchType=PDO::FETCH_ASSOC)
    {
        $dbc = self::getDBConnection();
        $result = $dbc->query($queryBody)->fetch($fetchType);
        return $result;
    }

    public static function execute($queryBody)
    {
        $dbc = self::getDBConnection();
        $result = $dbc->query($queryBody);
        return $result;
    }
}
