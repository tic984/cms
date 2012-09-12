<?php

class WeDo_Helpers_Application
{   

    public static function getClassUri($module, $class)
    {
        return new WeDo_ClassURI($module, $class);
    }

    

    public static function getModuleDescriptor($classUri)
    {
        list($moduleName, $className) = self::explodeClassUri($classUri);
        return WeDo_Application::getSingleton('app/WeDo_ModuleManager')->getModuleDescriptor($moduleName);
    }


}

?>