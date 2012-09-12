<?php

class WeDo_Environment extends WeDo_Descriptors_Descriptor
{

    public function __construct($simplexml)
    {
        try {
            parent::fromSimpleXml($simplexml);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getProperty($nodePath)
    {
        return $this->environmentDescriptor->$nodePath . "";
    }

    /**
     *
     * Loads resources by name
     * @param $resource
     * @param $name
     */
    public function getResourceByName($resource, $name)
    {
        $query = sprintf('connections/connection[@name="%s"][@connectionType="%s"]', $resource, $name);
        $r = new WeDo_Resources_Resource($this->toSimpleXml()->xpath($query));
        return $r;
    }

    /**
     *
     * Loads default resource
     * @param unknown_type $resource
     */
    public function getDefaultResource($resource)
    {
        $query = sprintf('connections/connection[@default="Y"][@connectionType="%s"]', $resource);
        $r = new WeDo_Resources_Resource($this->toSimpleXml()->xpath($query));
        return $r;
    }

    /**
     * returns all connections / resources that needs to be set up from start
     * Enter description here ...
     */
    public function getPersistentConnectionsDescriptor()
    {
        $query = sprintf('connections/connection[@persistent="Y"]');
        return $this->toSimpleXml()->xpath($query);
    }

    public function getAdminUrl()
    {
        $query = sprintf('backend_url');
        return current($this->toSimpleXml()->xpath($query));
    }

    public function getAppUrl()
    {
        $query = sprintf('frontend_url');
        return current($this->toSimpleXml()->xpath($query));
    }

    /**
     * run() loads the Environment, that is mainly the resources. (Db, Emails)
     * Actually, it makes them available for loading, I'm still not sure where to make them load.
     * As they are available for the whole app, I think it doesn't matter too much, so for the moment I
     * load them here. ACTUALLY IT WOULD BE BETTER IF:
     * 	-	This could be open for adding extra - resource without modify (Extension of Environment?)
     *
     * Enter description here ...
     */
    public function run()
    {
        try {
            foreach ($this->getPersistentConnectionsDescriptor() as $connectionDescriptor)
                WeDo_Adapters_AdapterFactory::getAdapter($connectionDescriptor)->enrollSingleton();
        } catch (Exception $e) {
            throw $e;
        }
    }

}