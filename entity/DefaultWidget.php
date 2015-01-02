<?php

class DefaultWidget extends MainEntity
{
    private $id;
    private $country;
    private $widgetId;
    private $priority;
    private $rateId;
    private $page;

    static public function findByCountry($country)
    {
        $query = "SELECT * FROM default_widgets WHERE Country = '$country' ORDER BY Priority ASC;";
        $result = self::queryAll($query);
        return $result;
    }
}