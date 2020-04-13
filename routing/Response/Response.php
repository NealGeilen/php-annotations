<?php
namespace Router\Response;

use Smarty;
use SmartyException;

class Response implements ResponseEnum {

    protected $template = "";
    protected $params = [];
    protected static $GlobalParams = [];



    public function __construct($templateFile = "", $parmas = [])
    {
        $this->template=$templateFile;
        $this->params=$parmas;
    }

    /**
     * @return array
     */
    public static function getGlobalParams(): array
    {
        return self::$GlobalParams;
    }

    /**
     * @param array $GlobalParams
     */
    public static function setGlobalParams(array $GlobalParams): void
    {
        self::$GlobalParams = $GlobalParams;
    }

    /**
     * @throws SmartyException
     */
    public function display(): void
    {
        $oSmarty = new Smarty();
        foreach (array_merge(self::$GlobalParams, $this->params) as $key => $value){
            $oSmarty->assign($key, $value);
        }
        $oSmarty->display($this->template);
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @param string $template
     */
    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    /**
     * @param $key
     * @param $value
     */
    public function addParam($key, $value): void
    {
        $this->params[$key] = $value;
    }


}