<?php

class MapAutoloader
{
    protected $classesMap = array();

    public function registerClass($className, $absolutePath)
    {
        if (file_exists($absolutePath)) {
            $this->classesMap[$className] = $absolutePath;
            return true;
        }
        return false;
    }

    public function autoload($class)
    {
        $pathToEntity = ENTITY_PATH . $class . '.php';
        $pathToController = CONTROLLERS_PATH. $class . 'Controller.php';
        $pathToModule = MODULES_PATH . $class . '.php';
        $pathToLibrary = LIB_PATH . $class . '.php';

        if (file_exists($pathToEntity)) {
            require_once($pathToEntity);
        } elseif (file_exists($pathToController)) {
            require_once($pathToController);
        } elseif (file_exists($pathToModule)) {
            require_once($pathToModule);
        } elseif (file_exists($pathToLibrary)) {
            require_once($pathToLibrary);
        } elseif (!empty($this->classesMap[$class])) {
            require_once($this->classesMap[$class]);
        } else {
            return false;
        }

        return true;
    }
} 