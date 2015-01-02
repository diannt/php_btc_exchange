<?php

class Localization extends MainEntity
{

    static public function translate($phrase, $lang)
    {
        $query = "SELECT * FROM `Localization`
                    WHERE BINARY EN='$phrase';";

        $result = self::query($query);
        return urldecode($result[$lang]);
    }

    static public function getAllData()
    {
        $query = "SELECT * FROM `Localization` ORDER BY id DESC;";
        $result = self::queryAll($query);
        return $result;
    }

    static public function availableLanguages()
    {
        $query = "SHOW COLUMNS FROM `Localization`;";
        $columns = self::queryAll($query);

        $fields = array_map(function($columns){
            return $columns['Field'];
        }, $columns);

        unset($fields[0]); // it's id

        return $fields;
    }

    static public function insert($row)
    {
        $fields = array();
        $values = array();
        foreach($row as $key=>$value)
        {
            array_push($fields, $key);
            array_push($values, "'" . $value . "'");
        }
        $fields = implode(", ", $fields);
        $values = implode(", ", $values);

        $query = "INSERT INTO `Localization` (" . $fields . ") VALUES (" . $values . ");";
        self::execute($query);
    }

    static public function update($id, $input)
    {
        $setQuery = array();
        foreach($input as $key=>$value)
        {
            array_push($setQuery, $key . "='" . $value . "'");
        }
        $setString = implode(", ", $setQuery);

        $query = "UPDATE `Localization` SET " . $setString . "
                    WHERE id='$id';";
        self::execute($query);
    }
}