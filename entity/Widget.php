<?php

class Widget extends MainEntity
{
    private $id;
    private $UID;
    private $widget_id;
    private $priority;
    private $rate;
    private $page;

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

    public function setWidgetId($widget_id)
    {
        $this->widget_id = $widget_id;
    }

    public function getWidgetId()
    {
        return $this->widget_id;
    }

    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    public function getPriority()
    {
        return $this->priority;
    }

    public function setRate($rate)
    {
        $this->rate = $rate;
    }

    public function getRate()
    {
        return $this->rate;
    }

    public function setPage($page)
    {
        $this->page = $page;
    }

    public function getPage()
    {
        return $this->page;
    }

    static public function getWidgetsCategories()
    {
        $query = "SELECT category FROM widgets;";
        $result = self::query($query);

        if (isset($result))
            return $result;

        return false;
    }

    static public function getWidgetsByCategory($category)
    {
        $query = ($category != 0) ? "SELECT * FROM widgets WHERE category = '" . $category . "';": "SELECT * FROM widgets;";
        $result = self::query($query);

        if (isset($result))
            return $result;

        return false;
    }

    static public function getWidgetDescription($id)
    {
        $query = "SELECT * FROM widgets
            WHERE id = '$id';";
        $result = self::query($query);

        if (isset($result))
            return $result;

        return false;
    }


    public function findById($id)
    {
        $query = "SELECT * FROM user_widgets
            WHERE id = '$id';";
        $result = self::query($query);

        if (isset($result))
        {
            $this->id = $id;
            $this->UID = $result['UID'];
            $this->widget_id = $result['widget_id'];
            $this->priority = $result['priority'];
            $this->rate = $result['rate'];
            $this->page = $result['page'];
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

        $query = "SELECT * FROM user_widgets
                    WHERE " . $whereString . ";";
        $result = self::queryAll($query);
        return $result;
    }

    public function insert()
    {
        $query = "INSERT INTO user_widgets (UID, widget_id, priority, rate, page)
                    VALUES ('$this->UID', '$this->widget_id', '$this->priority', '$this->rate', '$this->page');";
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

        $query = "UPDATE user_widgets SET " . $setString . "
                    WHERE id='$this->id';";
        self::execute($query);
    }

    public function delete()
    {
        $query = "DELETE FROM user_widgets
                    WHERE id='$this->id';";
        self::execute($query);
    }

    public function deletePage()
    {
        $query = "DELETE FROM user_widgets
                    WHERE page='$this->page' AND UID='$this->UID';";
        self::execute($query);
    }

    public function getUserWidgetsCount()
    {
        $query = "SELECT COUNT(*) as widgetCount FROM user_widgets
                    WHERE UID='$this->UID';";
        $result = self::queryAll($query);
        return $result[0]['widgetCount'];
    }

    public function getUserPagesCount()
    {
        $query = "SELECT COUNT(DISTINCT page) as pagesCount FROM user_widgets
                    WHERE UID='$this->UID';";
        $result = self::queryAll($query);
        return $result[0]['pagesCount'];
    }

    public function insertAllWidgetsOnPage()
    {
        $widgets = self::getAllWidgets();
        $priority = 1;
        foreach ($widgets as $value)
        {
            $query = "INSERT INTO user_widgets (UID, widget_id, priority, rate, page)
                        VALUES ('$this->UID','" . $value['id']  . "', '$priority', '$this->rate', '$this->page');";
            self::execute($query);
            $priority++;
        }
    }

    public function getAllPages()
    {
        $query = "SELECT DISTINCT page, rate
                  FROM user_widgets WHERE UID='$this->UID';";
        $result = self::queryAll($query);
        return $result;
    }

    public function getAllWidgets()
    {
        $query = "SELECT * FROM widgets;";
        $result = self::queryAll($query);
        return $result;
    }

}