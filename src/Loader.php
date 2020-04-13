<?php
namespace Annotation;

use ReflectionClass;
use ReflectionException;

class Loader{

    /**
     * @author Kevin Sentjens
     */
    const REGEX_BLOCK = '/[*\s]* 
(?P<annotation_name>[A-Z][\w\\\\]+) 
\("
(?P<first>
[\/a-zA-z0-9]+
)?
"
  (?P<value> 
    (?:
      [^@]*
      [^*\s)] 
    )
  )? 
(?:\s|\n|\))/sxmu';

    /**
     * @author Kevin Sentjens
     */
    const REGEX_OPTIONS = '/(?P<key>[a-zA-Z]+)\="(?P<value>[a-zA-Z]+)?(?:["])/';

    protected $reflection;

    /**
     * Loader constructor.
     * @param string $class
     * @throws ReflectionException
     */
    public function __construct(string $class)
    {
        $this->reflection = new ReflectionClass($class);
    }


    /**
     * @return Method[]
     */
    public function load(){
        $a = [];
        foreach ($this->reflection->getMethods() as $method){
            preg_match_all(self::REGEX_BLOCK, $method->getDocComment(), $matches, PREG_SET_ORDER, 0);
            foreach ($matches as $match){
                $Method = new Method($match["annotation_name"], $match["first"], $method);
                if (isset($match["value"])){
                    preg_match_all(self::REGEX_OPTIONS, $match["value"], $options, PREG_SET_ORDER, 0);
                    foreach ($options as $aOption){
                        $Method->addOption(new Option($aOption["key"], $aOption["value"]));
                    }
                }
                $a[] = $Method;
            }
        }
        return $a;
    }
}