<?php

class Core
{
    static function getObject()
    {
        return self;
    }

    //----------secure block--------------
    static public function secure_stripslashesGpc($value)
    {
        $value = trim($value);
        $search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
        $replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");

        $value = str_replace($search, $replace, $value);
        return $value;
    }

    //------------------------------------

    static function validate(&$param)
    {
        if(is_array($param))
        {
            $obj = self::getObject();
            array_walk_recursive($param, array(&$obj, 'secure_stripslashesGpc'));
        }
        else
            self::secure_stripslashesGpc($param);

        return $param;
    }

    static function runView($viewName, $data = array())
    {
        $filePath = VIEW_PATH . $viewName . '.php';

        if(!file_exists($filePath))
            return false;

        include($filePath);
    }

    //-------------------------------------

    static function runController($controller, $action)
    {
        if(method_exists($controller, $action))
        {
            $controller::beforeAction();
            $controller::$action();
        }
    }

    //-------------------------------------

    static function translateToCurrentLocale($phrase)
    {
        $lang = Session::getSessionVariable('lang');
        if ($lang === null)
        {
            $country = self::identifyCountry();
            $lang = self::identifyCountryLanguage($country);
            Session::setSessionVariable('lang', $lang);
        }

        if ($lang == 'EN')
            return $phrase;

        return Locale::translateTo($phrase, $lang);
    }

    static function identifyCountry()
    {
        $country = Session::getSessionVariable('country');
        if ($country !== null)
            return $country;

        $clientIP = self::getClientIP();
        $url = "http://api.ipinfodb.com/v3/ip-country/?key=7be18449bab1128e1f8c507b927112f34c634ba095c360842c536e49ddbddc56&ip=$clientIP&format=json";
        $jsonResponse = file_get_contents($url);

        $response = json_decode($jsonResponse, true);
        if ($response['countryCode'])
        {
            $country = $response['countryCode'];
        }
        else
        {
            $country = 'RU';
        }

        Session::setSessionVariable('country', $country);

        return $country;
    }

    static function identifyCountryLanguage($country)
    {
        if($country == "RU")
            $lang = 'RU';
        else
            $lang = 'EN';
        return $lang;
    }

    static function getClientIP()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return Core::validate($ip);
    }

    static function generateCaptcha()
    {
        $width = 99;                  //Ширина изображения
        $height = 40;                  //Высота изображения
        $font_size = 10;   			//Размер шрифта
        $let_amount = 5;               //Количество символов, которые нужно набрать
        $fon_let_amount = 5;          //Количество символов, которые находятся на фоне
        $path_fonts = $_SERVER['DOCUMENT_ROOT'] . '/public/fonts/ttf/';        //Путь к шрифтам

        $letters = array('a','b','c','d','e','f','g','h','j','k','m','n','p','q','r','s','t','u','v','w','x','y','z','2','3','4','5','6','7','9');
        $colors = array('10','30','50','70','90','110','130','150','170','190','210');

        $src = imagecreatetruecolor($width,$height);
        $fon = imagecolorallocate($src,255,255,255);
        imagefill($src,0,0,$fon);

        $fonts = array();
        $dir=opendir($path_fonts);
        while($fontName = readdir($dir))
        {
            if($fontName != "." && $fontName != "..")
            {
                $fonts[] = $fontName;
            }
        }
        closedir($dir);

        for($i=0;$i<$fon_let_amount;$i++)
        {
            $color = imagecolorallocatealpha($src,rand(0,255),rand(0,255),rand(0,255),100);
            $font = $path_fonts.$fonts[rand(0,sizeof($fonts)-1)];
            $letter = $letters[rand(0,sizeof($letters)-1)];
            $size = rand($font_size-2,$font_size+2);
            imagettftext($src,$size,rand(0,45),rand($width*0.1,$width-$width*0.1),rand($height*0.2,$height),$color,$font,$letter);
        }

        for($i=0;$i<$let_amount;$i++)
        {
            $color = imagecolorallocatealpha($src,$colors[rand(0,sizeof($colors)-1)],$colors[rand(0,sizeof($colors)-1)],$colors[rand(0,sizeof($colors)-1)],rand(20,40));
            $font = $path_fonts.$fonts[rand(0,sizeof($fonts)-1)];
            $letter = $letters[rand(0,sizeof($letters)-1)];
            $size = rand($font_size*2.1-2,$font_size*2.1+2);
            $x = ($i+1)*$font_size + rand(4,7);
            $y = (($height*2)/3) + rand(0,5);
            $cod[] = $letter;
            imagettftext($src,$size,rand(0,15),$x,$y,$color,$font,$letter);
        }

        $_SESSION['sdf'];
        Session::setSessionVariable('security_code', implode('',$cod));

        header("Content-type: image/gif");
        imagegif($src);
    }

    static function isAdministrator(User $user)
    {
        if ($user->getId() == 2)
            return true;
        return false;
    }

    static function isInteger($text)
    {
        if(preg_match("|^-?[\d]*$|", $text))
        {
            return true;
        }
        return false;
    }

    static function isDouble($text, $digitsAfterPoint = 0)
    {
        if ($digitsAfterPoint < 0)
            $digitsAfterPoint = 0;

        if ($digitsAfterPoint == 0) // unlimited number of digits after the decimal point
            return preg_match("|^[\d]*[\.]?[\d]*$|", $text);

        return preg_match("|^[\d]*[\.]?[\d]{0,$digitsAfterPoint}$|", $text);
    }

    static function round_up($value, $precision = 0)
    {
        if ($precision < 0)
            $precision = 0;

        $mult = pow(10, $precision);
        $tmp_value = $value * $mult;

        if (self::isInteger($tmp_value))
            return $value;

        return ceil($tmp_value) / $mult;
    }

    static function round_down($value, $precision = 0)
    {
        if ($precision < 0)
            $precision = 0;

        $mult = pow(10, $precision);
        $tmp_value = $value * $mult;

        if (self::isInteger($tmp_value))
            return $value;

        return floor($tmp_value) / $mult;
    }

    static function isEmailAddress($text)
    {
        return preg_match("/[0-9a-z_\.]+@[0-9a-z_^\.]+\.[a-z]{2,3}/i", $text);
    }

    static function printErrorJson($errorMessage)
    {
        $result['success'] = 0;
        $result['error'] = $errorMessage;
        print json_encode($result);
    }

    static function printSuccessJson($message)
    {
        $result['success'] = 1;
        $result['message'] = $message;
        print json_encode($result);
    }

    static function timestamp_gmp()
    {
        return gmdate('Y-m-d H:i:s');
    }

    static function server_url()
    {
        return $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'];
    }

    static function array_search($array, $field, $searchingValue)
    {
        foreach($array as $key => $value)
        {
            if(is_array($value))
            {
                $k = array_search($searchingValue, $value);
                if($k == $field)
                    return $key;
            }
        }
        return -1;
    }

    static function array_sort($array, $on, $order=SORT_ASC)
    {
        $new_array = array();
        $sortable_array = array();

        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }

            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                    break;
                case SORT_DESC:
                    arsort($sortable_array);
                    break;
            }

            foreach ($sortable_array as $k => $v) {
                $new_array[$k] = $array[$k];
            }
        }

        return $new_array;
    }

    static function send_mail($recipientEmail, $subject, $message)
    {
        //Core::useLib('PHPMailer/PHPMailerAutoload');

        $host = 'smtp.yandex.ru';
        $login = 'noreply@emonex.info';
        $password = '20802080a';

        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->CharSet = 'utf-8';
        $mail->Host = $host;
        $mail->SMTPAuth = true;
        $mail->Username = $login;
        $mail->Password = $password;
        $mail->Port = 587;

        $mail->setFrom($login, 'Bitmonex');
        $mail->addAddress($recipientEmail);

        $mail->isHTML();
        $mail->Subject = $subject;
        $mail->Body = $message;

        return $mail->send();
    }

    static function write_log($path, $text)
    {
        $fp = fopen($path,"ab");
        fwrite($fp, date('Y-m-d H:i:s') . '  ' . $text . "\n");
        fclose($fp);
    }

    static function calculateHash($var)
    {
        return hash('sha256', $var);
    }

}