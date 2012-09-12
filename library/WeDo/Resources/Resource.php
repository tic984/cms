<?php

class WeDo_Resources_Resource extends WeDo_Descriptors_Descriptor
{

    private $_uri;
    private $_login;
    private $_password;
    private $_params;

    public function __construct(&$simpleXmlDescriptor)
    {
        parent::fromSimpleXml($simpleXmlDescriptor);
    }

}

?>