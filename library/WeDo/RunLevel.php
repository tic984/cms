<?php

class WeDo_RunLevel extends WeDo_Descriptors_Descriptor
{

    public function __construct($simplexml)
    {
        try {
            parent::fromSimpleXml($simplexml);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getIncludes()
    {
        return $this->toSimpleXml()->includes->include;
    }

    public function getDefines()
    {
        return $this->toSimpleXml()->defines->define;
    }

    public function getAutoloadPool()
    {
        return $this->toSimpleXml()->autoload_pool->dir;
    }

    public function getParam($group, $param_name)
    {
        return $this->toSimpleXml()->params->xpath(sprintf('/group[@name="%s"]/param[@name="%s"]', $group, $param_name));
    }

    /**
     * each runlevels has a set of includes, has its set of autoload pools,
     * and makes available application params,
     * Enter description here ...
     */
    public function run()
    {
        try {
            $this->addIncludePath();
            $this->doDefines();
            $this->addInclude();
            //performs autoload
            spl_autoload_register('WeDo_RunLevel::autoload');
        } catch (Exception $e) {
            throw $e;
        }
    }

    private function addInclude()
    {
        foreach ($this->getIncludes() as $node)
        {
            $mode = $node["mode"];
            $file = APPLICATION_PATH . $node;

            if (!file_exists($file))
            {
                throw new Exception($file . " Not Found");
            }
//            Logger::getLogger(__CLASS__)->info("Including: $file");
            switch ($mode)
            {
                case 'require_once':
                    require_once $file;
                    break;
                case 'require':
                    require $file;
                    break;
                case 'include':
                    include $file;
                    break;
                case 'include_once':
                    include_once $file;
                    break;
                default:
                    break;
            }
        }
    }

    private static function autoLoad($class)
    {
        try {
            
            $tokens = (explode("_", $class)); 
            $classname = array_pop($tokens).".php";
            $dir = implode(DIRECTORY_SEPARATOR, $tokens);
//            Logger::getLogger(__CLASS__)->debug("AUTOLOADER: searching for $dir");
            Zend_Loader::loadFile($classname, APPLICATION_PATH."/models/", true);   
                      
//            foreach (WeDo_Application::getCurrentRunLevel()->getAutoloadPool() as $dirs)
//            {
//                $classpath = APP_PATH . $dirs . $name . ".class.php";
//
//                Logger::getLogger(__CLASS__)->debug("AUTOLOADER: searching for $classpath");
//                
//                if (file_exists($classpath))
//                {
//                    require_once($classpath);
//                    return;
//                }
//            }
            /* 			
              require_once 'Zend/Loader/Autoloader.php';
              $loader = Zend_Loader_Autoloader::getInstance();
              Zend_Loader::loadClass($name); */
        } catch (Exception $e) {
            print $e->getMessage();
        }
    }

    private function doDefines()
    {
        foreach (WeDo_Application::getCurrentRunLevel()->getDefines() as $node)
        {
            $define = $node["varname"];
            $value = $node;
            define($define, $value);
        }
    }

    private function addIncludePath()
    {
        try {
            $all_include_path = get_include_path();
            foreach (WeDo_Application::getCurrentRunLevel()->getIncludePath() as $node)
            {
                $filepath = APP_PATH . $node;
                if (file_exists($filepath) && is_readable($filepath))
                    $all_include_path .= ":" . $filepath;
                else
                    throw new Exception("$filepath not Found");
            }
            set_include_path($all_include_path);
        } catch (Exception $e) {
            throw $e;
        }
    }

    private function getIncludePath()
    {
        return $this->toSimpleXml()->include_path->dir;
    }

}