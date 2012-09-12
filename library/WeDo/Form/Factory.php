<?php
class WeDo_Form_Factory
{
    public static function getForm(WeDo_ClassURI &$classURI, $options=null)
    {
        $classmodel = WeDo_Application::getSingleton("app/WeDo_ModuleManager")->getModuleDescriptor($classURI->getPrefix())->getClassModel($classURI->projectToZendClassName());
        switch($classmodel)
        {
            default:
            case 'foundation':
                return new WeDo_Form_Form($classURI, $options);
            case 'mongo':
                return new WeDo_Form_MongoForm($classURI, $options);
        }
    }
}
?>
