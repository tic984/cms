<?php

class WeDo_Application
{

    /**
     * All Singletons Enlisted in the application
     * @var array
     */
    private static $_singletons_pool = null;
    private static $_obj_registry = null;
    private static $_srv_registry = null;
    private static $_mapper_registry = null;

    /**
     * returns Singleton by name.
     * Every object is created by mean of its constructor.
     * @param string $item
     */

    /**
     * Is the main entry point of the whole app.
     * Uses ApplicationDescriptor to detect and load the environment and the current runlevel.
     * Actually, it
     * 		-	Enable Logs.
     * 		-	Performs all necessary includes / requires
     * 		- 	Enables __autoload
     * 		-	Makes all Params available to request.
     * 		-	Enrolls to the singleton pools the email and database resources
     *
     * @param $runlevelname
     */
    public static function runFor($runlevelname)
    {
        try {
//            self::enableLogging();

            $appDescriptor = new WeDo_Descriptors_Application();
            
            $env = $appDescriptor->getEnvironment();
            $runlevel = $appDescriptor->loadRunLevel($runlevelname);

            WeDo_Application::enrollSingleton($appDescriptor, WeDo_ClassURI::fromString('app/WeDo_Descriptors_Application'));
            WeDo_Application::enrollSingleton($env, WeDo_ClassURI::fromString('app/WeDo_Environment'));
            WeDo_Application::enrollSingleton($runlevel, WeDo_ClassURI::fromString('app/WeDo_CurrentRunlevel'));
            WeDo_Application::enrollSingleton($appDescriptor->loadModuleManager(), WeDo_ClassURI::fromString('app/WeDo_ModuleManager'));

            //Starts the application

            $env->run();
            $runlevel->run();

 //           Logger::getLogger(__CLASS__)->info("WeDo_Application::run() ended correctly");
        } catch (Exception $e) {
 //           Logger::getLogger(__CLASS__)->error($e->getMessage());
            throw $e;
        }
    }

    public static function getSingleton($classuri)
    {
        try {
            if (!$classuri instanceof WeDo_ClassURI)
                $classuri = WeDo_ClassURI::fromString($classuri);

            $prefix = $classuri->getPrefix();
            $classname = $classuri->getClassName();
            if (!isset(self::$_singletons_pool[$prefix][$classname]) || empty(self::$_singletons_pool[$prefix][$classname]))
            {
                if (class_exists($classname))
                    self::$_singletons_pool[$prefix][$classname] = new $classname();
                else
                    throw new Exception("Class '$classname' not found for '" . $classuri->toString()."'");
            }
            return self::$_singletons_pool[$prefix][$classname];
        } catch (Exception $e) {
 //           Logger::getLogger(__CLASS__)->error($e->getMessage());
            throw $e;
        }
    }

    /**
     *
     * Can be used to register Singletons in the singletons pool.
     * Item must be in the form $namespace / $classname
     * @param object $item
     * @param string $location
     */
    public static function enrollSingleton($item, WeDo_ClassURI $classuri)
    {
        try {
            if (empty($item))
                throw new Exception("No item to enroll in $location");
            $prefix = $classuri->getPrefix();
            $classname = $classuri->getClassName();
            if (!isset(self::$_singletons_pool[$prefix][$classname]) || self::$_singletons_pool[$prefix][$classname] == null)
                self::$_singletons_pool[$prefix][$classname] = $item;
        } catch (Exception $e) {
 //           Logger::getLogger(__CLASS__)->error("Error enrolling in " . $classuri->toString());
            throw $e;
        }
    }

    /**
     * returns path to /app directory
     * Enter description here ...
     */
    public static function getRootPath()
    {
        return APP_PATH . DIRECTORY_SEPARATOR;
    }

    public static function getCurrentRunlevel()
    {
        return WeDo_Application::getSingleton(WeDo_ClassURI::fromString("app/WeDo_CurrentRunlevel"));
    }

//    private static function enableLogging()
//    {
//        require_once APP_PATH . 'libs/log4php/Logger.php';
//        Logger::configure(APP_PATH . 'libs/log4php/xml/log4php.properties.xml');
//    }

    public static function dumpSingletonsPool()
    {
        $res = array();
        foreach (self::$_singletons_pool as $namespace => $classes)
        {
            foreach ($classes as $classname => $object)
                $res[] = " $namespace / $classname ";
        }
        return $res;
    }

    public static function getService($className)
    {

        try {

            if ($className instanceof WeDo_ClassURI)
                $classUri = $className;
            else
                $classUri = WeDo_ClassURI::fromString($className);
            if ($classUri->is_singleton())
                throw new Exception("Cannot use __new for instantiating singletons, use enroll instead");

            $cname = $classUri->projectToZendClassName();
            $cmodule = $classUri->getPrefix();

            if (!isset(self::$_srv_registry[$cmodule][$cname]) || empty(self::$_srv_registry[$cmodule][$cname]))
            {
                $classModel = WeDo_Application::getSingleton("app/WeDo_ModuleManager")->getModuleDescriptor($cmodule)->getClassModel($cname);
                $classServiceName = WeDo_Application::getSingleton("app/WeDo_ModuleManager")->getModuleDescriptor($cmodule)->getClassService($cname);
                $classHelper = WeDo_Application::getSingleton("app/WeDo_ModuleManager")->getModuleDescriptor($cmodule)->getClassHelper($cname);

                if (empty($classModel))
                    throw new Exception("No model defined for class" . $className->toString());
                if (empty($classServiceName))
                    throw new Exception("No service defined for class" . $className->toString());


                switch ($classModel)
                {
                    case 'foundation':
                        if ($classHelper != '')
                            self::$_srv_registry[$cmodule][$cname] = new WeDo_Models_Foundation_Service($classUri, new $classHelper());
                        else
                            self::$_srv_registry[$cmodule][$cname] = new WeDo_Models_Foundation_Service($classUri);
                        break;
                    case 'eav':
                        self::$_srv_registry[$cmodule][$cname] = new WeDo_Models_Eav_Service($classUri);
                        break;
                    case 'mongo':
                        self::$_srv_registry[$cmodule][$cname] = new WeDo_Models_Mongo_Service($classUri);
                        break;
                }
            }

            return self::$_srv_registry[$cmodule][$cname];
        } catch (Exception $e) {
            throw $e;
        }
    }

    public static function __new($className)
    {
        try {
            if ($className instanceof WeDo_ClassURI)
                $classUri = $className;
            else
                $classUri = WeDo_ClassURI::fromString($className);

            if ($classUri->is_singleton())
                throw new Exception("Cannot use __new for instantiating singletons, use enroll instead");
            $cname = $classUri->getClassName();
            $cmodule = $classUri->getPrefix();

            if (!isset(self::$_obj_registry[$cmodule][$cname]) || empty(self::$_obj_registry[$cmodule][$cname]))
            {
                $realclassname = $classUri->projectToZendClassName($classUri);
                self::$_obj_registry[$cmodule][$cname] = new $realclassname($classUri);
            }
            return clone self::$_obj_registry[$cmodule][$cname];
        } catch (Exception $e) {
            throw $e;
        }
    }

    public static function __mapper($className)
    {
        try {
            if ($className instanceof WeDo_ClassURI)
                $classUri = $className;
            else
                $classUri = WeDo_ClassURI::fromString($className);

            if ($classUri->is_singleton())
                throw new Exception("Cannot use __new for instantiating singletons, use enroll instead");
            $cname = $classUri->getClassName();
            $cmodule = $classUri->getPrefix();
            
            if (!isset(self::$_mapper_registry[$cmodule][$cname]) || empty(self::$_mapper_registry[$cmodule][$cname]))
            {
                $realclassname = $classUri->projectToZendMapperName($classUri);
                $serviceHandler = WeDo_Application::getService($classUri);

                self::$_mapper_registry[$cmodule][$cname] = new $realclassname($serviceHandler, $classUri);
               
            }
            return clone self::$_mapper_registry[$cmodule][$cname];
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    public static function getTime()
    {
        return time();
    }

}

?>