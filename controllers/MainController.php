<?php

class MainController
{
    static function getVar($varName)
    {
        if(isset($_GET[$varName]))
            return $_GET[$varName];

        if(isset($_POST[$varName]))
            return $_POST[$varName];

        return null;
    }

    static function beforeAction()
    {

    }
}