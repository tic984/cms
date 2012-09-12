<?php

class WeDo_ModuleManager extends WeDo_Descriptors_Descriptor
{

    private $_module_pool;
    private $_modules_enabled;
    private $_modules_codepools;
    private $_classes_descriptors_pools;
    private $_module_handlers;

    const MODULE_DESCRIPTOR_FILENAME = 'module.xml';

    /**
     *
     * Activation of the ModuleManager is made by the ApplicationDescriptor.
     * Activation implies the registration of the autoloads
     * @param simplexmlobject $descriptor
     */
    public function __construct($descriptor)
    {
        try {
            if (empty($descriptor))
                throw new Exception("No Descriptor specified");
            parent::fromSimpleXml($descriptor);

            $this->_module_pool = array();
            $this->_classes_pool = array();
            $this->_module_handlers = array();
            $this->_modules_codepools = array();
            $this->_modules_enabled = array();

            $this->activateModules();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getMapFor(WeDo_ClassURI $classuri, $arr_fieldProperties=array())
    {
        return $this->getClassDescriptor($classuri)->getMap($arr_fieldProperties);
    }

    public function getRelationsFor(WeDo_ClassURI $classuri)
    {
        return $this->getClassDescriptor($classuri)->getRelationsName();
    }

    /**
     *
     * Loads class Descriptor. Behaves as a singleton.
     * @param string $classuri
     * @throws Exception
     * @return WeDo_Descriptors_Object
     */
    public function getClassDescriptor(WeDo_ClassURI $classuri)
    {
        $module = $classuri->getPrefix();
        $class = $classuri->getClassName();
        if ($this->isModuleEnabled($module))
        {
            if (!isset($this->_classes_pool[$module]) || !isset($this->_classes_pool[$module][$class]) || empty($this->_classes_pool[$module][$class]))
                $this->_classes_pool[$module][$class] = $this->loadClassDescriptor($classuri);
            return $this->_classes_pool[$module][$class];
        } else
            throw new Exception("Module $module not enabled, therefore $class cannot get its classDescriptor");
    }

    /**
     *
     * Loads module.xml
     * @param unknown_type $moduleName
     * @throws Exception
     */
    public function getModuleDescriptor($moduleName)
    {
        try {
            if (trim($moduleName) == '')
                throw new Exception("Empty moduleName");
            if ($this->isModuleEnabled($moduleName))
            {
                if (!isset($this->_module_pool[$moduleName]) || empty($this->_classes_pool[$moduleName]))
                    $this->_module_pool[$moduleName] = $this->loadModuleDescriptor($moduleName);
                return $this->_module_pool[$moduleName];
            } else
                throw new Exception("Module '$moduleName' not enabled");
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     *
     * Loads class.xml within its module
     * @param unknown_type $moduleName
     * @param unknown_type $classname
     * @throws Exception
     * @return WeDo_Descriptors_Object
     */
    private function loadClassDescriptor(WeDo_ClassURI $classuri)
    {
        try {
            $moduleName = $classuri->getPrefix();
            $classname = $classuri->getClassName();

            $codepool = $this->detectCodePool($moduleName);
            $path = '';

            switch ($codepool)
            {
                default:
                case 'default':
                    $path = $this->getCodePoolPath($codepool, $moduleName) . $this->getClassDescriptorFilename($classname);
                    break;
            }
            if (file_exists($path) && is_readable($path))
                return new WeDo_Descriptors_Object(simplexml_load_file($path));
            else
                throw new Exception("'$path' of ClassDescripor for $moduleName/$classname  not found");
        } catch (Exception $e) {
            throw $e;
        }
    }

    private function loadModuleDescriptor($moduleName)
    {
        try {
            $codepool = $this->detectCodePool($moduleName);
            $path = $this->getCodePoolPath($codepool, $moduleName) . self::MODULE_DESCRIPTOR_FILENAME;

            return new WeDo_Descriptors_Module($path);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function detectCodePool($moduleName)
    {
        return $this->_modules_codepools[$moduleName];
    }

    

    public function isModuleEnabled($moduleName)
    {
        try {
            if (trim($moduleName) == '')
                throw new Exception("Empty moduleName");
            if (!isset($this->_modules_enabled[$moduleName]))
                throw new Exception("$moduleName not registered");
            return $this->_modules_enabled[$moduleName];
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function activateModules()
    {
        foreach ($this->toSimpleXml()->xpath("//modules/module[@active='Y']") as $mod)
        {
            $codepool = strval($mod['codepool']);
            $modulename = strval($mod['name']);
            $classNameForAutoload = $modulename;

            $classPathForAutoload = $this->getModuleAutoloaderClassPath($codepool, $modulename);

            $this->_modules_enabled[$modulename] = true;
            $this->_modules_codepools[$modulename] = $codepool;
            //if i want to use default module, i have no class for referencing, instantiating objects, etc..
            if ($modulename != 'Default')
            {
                if (file_exists($classPathForAutoload))
                {
                    require_once($classPathForAutoload);
                    //spl_autoload_register("$classNameForAutoload::autoload");
                    //Logger::getLogger(__CLASS__)->info("registrato autoload: $classNameForAutoload::autoload");
                } else
                {
                    throw new Exception("Cannot activate module $modulename, autoloadpath ('$classPathForAutoload') not found");
                }
            }
        }
    }

    private function getModuleAutoloaderClassPath($codepool, $modulename)
    {
        switch ($codepool)
        {
            case 'core':
                return APPLICATION_PATH . 'code/core/modules/' . $modulename . '/' . $modulename . '.class.php';
            case 'local':
                return APPLICATION_PATH . 'code/local/' . $modulename . '/' . $modulename . '.class.php';
            default:
            case 'default':
                return APP_PATH . 'library/Project/' . $modulename . '/Module.php';
        }
    }

    /**
     * 
     * returns all modulenames along their activation status
     */
    public function listModules()
    {
        return $this->_modules_enabled;
    }
    
    private function getCodePoolPath($codepool, $moduleName)
    {
        $path = '';
        switch ($codepool)
        {

            default:
            case 'Default':
                $path = APP_PATH . '/library/Project/' . $moduleName . '/etc/';
                break;
        }
        return $path;
    }

    private function getClassDescriptorFilename($classname)
    {
        return $classname . '.xml';
    }
}

?>