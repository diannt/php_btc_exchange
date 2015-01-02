<?php

class News extends MainEntity
{
    private $id;
    private $news_id;
    private $lang;
    private $title;
    private $full;
    private $date;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setNewsid($news_id)
    {
        $this->news_id = $news_id;
    }

    public function getNewsid()
    {
        return $this->news_id;
    }

    public function setLang($lang)
    {
        $this->lang = $lang;
    }

    public function getLang()
    {
        return $this->lang;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setFull($full)
    {
        $this->full = $full;
    }

    public function getFull()
    {
        return $this->full;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }

    public function getDate()
    {
        return $this->date;
    }


    public function findById($id)
    {
        $query = "SELECT * FROM news
            WHERE id = '$id';";
        $result = self::query($query);

        if (isset($result))
        {
            $this->id = $id;
            $this->news_id = $result['news_id'];
            $this->lang = $result['lang'];
            $this->title = $result['title'];
            $this->full = $result['full'];
            $this->date = $result['date'];
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

        $query = "SELECT * FROM `news`
                    WHERE " . $whereString . ";";
        $result = self::queryAll($query);
        return $result;
    }

    public function insert()
    {
        if ($this->lang != 'EN')
        {
            $this->title = urlencode($this->title);
            $this->full = urlencode($this->full);
        }

        $query = "INSERT INTO news (news_id, lang, title, full, date)
                    VALUES ('$this->news_id', '$this->lang', '$this->title', '$this->full', '$this->date');";
        self::execute($query);
    }

    public function save()
    {
        $query = "UPDATE news SET news_id='$this->news_id', lang='$this->lang', title='$this->tile', full='$this->full', date='$this->date'
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

        $query = "UPDATE news SET " . $setString . "
                    WHERE id='$this->id';";
        self::execute($query);
    }

    public function delete()
    {
        $query = "DELETE FROM news
                    WHERE id='$this->id';";
        self::execute($query);
    }

    public function getLastNewsByLocation()
    {
        $language = Session::getSessionVariable('lang');
        if($language === null)
            return;

        $sql = "SELECT * FROM `news` WHERE `lang` =  '" . $language . "' ORDER BY `news_id` DESC LIMIT 1";
        $result = self::query($sql);

        if ($result && $language != 'EN')
        {
            $result['title'] = urldecode($result['title']);
            $result['full'] = urldecode($result['full']);
        }

        return $result;
    }

    public function getLastNewsId()
    {
        $query = 'SELECT MAX( news_id ) AS id FROM  `news`';
        $result = self::query($query);

        if(isset($result))
            return $result['id'];

    }
}