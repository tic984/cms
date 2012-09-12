<?php

class WeDo_Adapters_AdapterFactory
{

    /**
     * returns adapter based on 'type' attribute
     * @param unknown_type $simplexmlDescriptor
     */
    public static function getAdapter(&$simplexmlDescriptor)
    {
        try {
            $adapterType = $simplexmlDescriptor['type'];

            switch ($adapterType)
            {
                case 'database':
                    //require_once APP_PATH . "code/core/adapters/DatabaseAdapterFactory.class.php";
                    return WeDo_Adapters_Db_Factory::getAdapter($simplexmlDescriptor);
                    break;
                case 'email':
                    //require_once APP_PATH . "code/core/adapters/EmailAdapterFactory.class.php";
                    return WeDo_Adapters_Db_Factory::getAdapter($simplexmlDescriptor);
                    break;
                default:
                    throw new Exception("Adapter Type not found");
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

}