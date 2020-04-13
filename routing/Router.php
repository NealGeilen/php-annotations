<?php
namespace Router;

use Exception;

class Router{
    protected static $ControllerDirectorie;

    /**
     * @param string $url
     */
    public static function displayPage($url = ""){
        try {
            $L = new Loader();
            $L->display($url);
        } catch (Exception $e){
            var_dump($e->getMessage());
        }
    }

    /**
     * @return string
     */
    public static function getControllerDirectorie()
    {
        return self::$ControllerDirectorie;
    }

    /**
     * @param string $ControllerDirectorie
     */
    public static function setControllerDirectorie($ControllerDirectorie): void
    {
        self::$ControllerDirectorie = $ControllerDirectorie;
    }
}