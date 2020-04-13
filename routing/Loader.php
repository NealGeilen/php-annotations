<?php
namespace Router;
use Annotation\Method;
use Exception;
use ReflectionClass;
use ReflectionException;
use Router\Response\ResponseEnum;

class Loader{

    /**
     * @return string
     */
    public static function getCachedFile(){
        return $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "cash.json";
    }

    /**
     * @param $sDir
     * @throws ReflectionException
     */
    protected function readDir($sDir){
        foreach (scandir($sDir) as $file){
            if (strlen($file) > 3){
                if (is_file($sDir .DIRECTORY_SEPARATOR . $file)){
                    $this->readFile($sDir .DIRECTORY_SEPARATOR . $file);
                } elseif (is_dir($sDir .DIRECTORY_SEPARATOR . $file)){
                    $this->readDir($sDir .DIRECTORY_SEPARATOR . $file);
                }
            }
        }
    }

    /**
     * @param $file
     * @throws ReflectionException
     */
    protected function readFile($file){
        if (is_file($file) && pathinfo($file,PATHINFO_EXTENSION) === "php"){
            $data = $this->classes_in_file($file);
            $class = $data[0]["namespace"] .'\\'. $data[0]["classes"][0]["name"];
        } else {
            $class = $file;
        }
        foreach ((new \Annotation\Loader($class))->load() as $method){
            if ($method->getName() === "Route"){
                $this->setRoute($method);
            }
        }
    }


    /**
     *
     * Looks what classes and namespaces are defined in that file and returns the first found
     * @param String $file Path to file
     * @return array|null NULL if none is found or an array with namespaces and classes found in file
     */
    function classes_in_file($file)
    {
        $classes = $nsPos = $final = array();
        $foundNS = FALSE;
        $ii = 0;

        if (!file_exists($file)) return NULL;

        $er = error_reporting();
        error_reporting(E_ALL ^ E_NOTICE);

        $php_code = file_get_contents($file);
        $tokens = token_get_all($php_code);
        $count = count($tokens);

        for ($i = 0; $i < $count; $i++)
        {
            if(!$foundNS && $tokens[$i][0] == T_NAMESPACE)
            {
                $nsPos[$ii]['start'] = $i;
                $foundNS = TRUE;
            }
            elseif( $foundNS && ($tokens[$i] == ';' || $tokens[$i] == '{') )
            {
                $nsPos[$ii]['end']= $i;
                $ii++;
                $foundNS = FALSE;
            }
            elseif ($i-2 >= 0 && $tokens[$i - 2][0] == T_CLASS && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING)
            {
                if($i-4 >=0 && $tokens[$i - 4][0] == T_ABSTRACT)
                {
                    $classes[$ii][] = array('name' => $tokens[$i][1], 'type' => 'ABSTRACT CLASS');
                }
                else
                {
                    $classes[$ii][] = array('name' => $tokens[$i][1], 'type' => 'CLASS');
                }
            }
            elseif ($i-2 >= 0 && $tokens[$i - 2][0] == T_INTERFACE && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING)
            {
                $classes[$ii][] = array('name' => $tokens[$i][1], 'type' => 'INTERFACE');
            }
        }
        error_reporting($er);
        if (empty($classes)) return NULL;

        if(!empty($nsPos))
        {
            foreach($nsPos as $k => $p)
            {
                $ns = '';
                for($i = $p['start'] + 1; $i < $p['end']; $i++)
                    $ns .= $tokens[$i][1];

                $ns = trim($ns);
                $final[$k] = array('namespace' => $ns, 'classes' => $classes[$k+1]);
            }
            $classes = $final;
        }
        return $classes;
    }

    /**
     * @return array
     */
    protected function readConfig(){
        if (!is_file(self::getCachedFile())){
            $this->setConfig([]);
        }
        return json_decode(file_get_contents(self::getCachedFile()), true);
    }


    /**
     * @param Method $method
     */
    protected function setRoute(Method $method){
        $aData = $this->readConfig();
        $aData[$method->getValue()] = ["class" => $method->getReflectionMethod()->getDeclaringClass()->getName(), "method" => $method->getReflectionMethod()->getName(), "name" => $method->getName(), "options" => $method->getOptions()];
        $this->setConfig($aData);
    }

    /**
     * @param array $data
     */
    protected function setConfig(array $data){
        $file = fopen(self::getCachedFile(), "w");
        fwrite($file, json_encode($data));
        fclose($file);
    }

    /**
     * @param $url
     * @throws Exception
     */
    public function display($url = ""){
        $aData = $this->readConfig();
        if (isset($aData[$url])){
            //Url is defined
            $method = $aData[$url];
            if (class_exists($method["class"])){
                $Reflection = new ReflectionClass($method["class"]);
                if ($Reflection->hasMethod($method["method"])){
                    //Function exists
                    $response = $Reflection->getMethod($method["method"])->invoke($Reflection->newInstance());
                    if ($response instanceof ResponseEnum){
                        $response->display();
                        exit;
                    } else {
                        throw new Exception("Response is not correct type", 500);
                    }
                } else {
                    //method not found
                    $this->readFile($method["class"]);
                    $this->display($url);
                }
            } else {
//                throw new Exception("Class not found");
                $this->readDir(Router::getControllerDirectorie());
                //Class not found
            }
            exit;
        } else {
            $this->readDir(Router::getControllerDirectorie());
            $aData = $this->readConfig();
            if (isset($aData[$url])){
                $this->display($url);
                exit;
            }
        }
        http_response_code(404);
        throw new Exception("Not found", 404);

        //Page not defined
    }
}