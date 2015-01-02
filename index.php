<?php

error_reporting(E_ERROR);

define("CORE_PATH", $_SERVER['DOCUMENT_ROOT'] . "/core/");
define("CONTROLLERS_PATH", $_SERVER['DOCUMENT_ROOT'] . "/controllers/");
define("MODULES_PATH", $_SERVER['DOCUMENT_ROOT'] . "/modules/");
define("ENTITY_PATH", $_SERVER['DOCUMENT_ROOT'] . "/entity/");
define("VIEW_PATH", $_SERVER['DOCUMENT_ROOT'] . "/view/");
define("LIB_PATH", $_SERVER['DOCUMENT_ROOT'] . "/lib/");


if(!file_exists(CORE_PATH . 'core.php') || !file_exists(CONTROLLERS_PATH . 'MainController.php') || !file_exists(ENTITY_PATH . 'MainEntity.php'))
{
    print "not found!";
    exit;
}

require_once(CORE_PATH . 'core.php');
require_once(CONTROLLERS_PATH . 'MainController.php');
require_once(ENTITY_PATH . 'MainEntity.php');

//localization class
require_once(CORE_PATH . 'locale.php');

//autoloader class
require_once(CORE_PATH . 'MapAutoloader.php');

// Please! Do not give the same name to classes from different modules!
$autoloader = new MapAutoloader();

$autoloader->registerClass('OrderType', MODULES_PATH . 'OrderModel.php');
$autoloader->registerClass('OrderStatus', MODULES_PATH . 'OrderModel.php');

$autoloader->registerClass('PHPMailer', LIB_PATH . 'PHPMailer/class.phpmailer.php');

$autoloader->registerClass('EgoPayAuth', LIB_PATH . 'EgoPay/api/api.php');
$autoloader->registerClass('EgoPayApiException', LIB_PATH . 'EgoPay/api/api.php');
$autoloader->registerClass('EgoPayJsonApiAgent', LIB_PATH . 'EgoPay/api/EgoPayJsonApiAgent.php');
$autoloader->registerClass('EgoPaySciCallback', LIB_PATH . 'EgoPay/sci/EgoPaySci.php');
$autoloader->registerClass('EgoPayException', LIB_PATH . 'EgoPay/sci/EgoPaySci.php');

spl_autoload_register(array($autoloader, 'autoload'));

$configPath = 'config/config.php';
if (file_exists($configPath))
    require_once $configPath;
else
    die('Where is my config, dude?');

require_once ('config/moneyConfig.php');

date_default_timezone_set('Europe/Moscow');

$url = explode('?', $_SERVER['REQUEST_URI']);

$pathUrl = explode('/', $url[0]);
$module = $pathUrl[1] ? $pathUrl[1] : 'usr';
$action = $pathUrl[2] ? $pathUrl[2] : 'index';

Core::runController($module, $action);
