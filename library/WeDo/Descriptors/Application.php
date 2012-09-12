<?php

class WeDo_Descriptors_Application extends WeDo_Descriptors_Descriptor
{
    /**
     * path for application descriptor
     * @var string
     */
    const APP_DESCRIPTOR_PATH = 'etc/app.xml';

    /**
     *
     * Current Runlevel
     * @var unknown_type
     */
    private $currentRunlevel;

    /**
     *
     * Current Environment
     * @var Environment
     */
    private $environment;

    public function __construct()
    {
        try {
            parent::fromFile(APP_PATH.DIRECTORY_SEPARATOR.self::APP_DESCRIPTOR_PATH);
            $this->loadEnvironment();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function detectEnvironment()
    {
        $query = '//config/environment';
        return WeDo_Helpers_Xml::queryForNodeValue($this->getDescriptor(), $query);
    }

    public function getEnvironment()
    {
        return $this->environment;
    }

    public function getCurrentRunlevel()
    {
        return $this->currentRunlevel;
    }

    public function loadRunLevel($runlevel)
    {
        $this->currentRunlevel = new WeDo_RunLevel($this->toSimpleXml()->config->runlevels->$runlevel);
        return $this->currentRunlevel;
    }

    public function loadModuleManager()
    {
        try {
            return new WeDo_ModuleManager($this->toSimpleXml()->modules);
        } catch (Exception $e) {
            throw $e;
        }
    }

    private function loadEnvironment()
    {
        try {
            $env_name = $this->detectEnvironment();
            $this->environment = new WeDo_Environment($this->toSimpleXml()->environments->$env_name);
        } catch (Exception $e) {
            throw $e;
        }
    }

}