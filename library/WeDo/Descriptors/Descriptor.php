<?php

class WeDo_Descriptors_Descriptor
{

    protected $_xmlDescriptor;
    private $_xmlDescriptor_as_simplexml;

    public function fromFile($descriptor_path)
    {
        try {

            if (file_exists($descriptor_path) && is_readable($descriptor_path))
            {
                $this->_xmlDescriptor = DOMDocument::load($descriptor_path);
                //if(!$app_xml_descriptor->validate())
                //	throw new Exception(self::DESCRIPTOR_INVALID_EXCEPTION_ERRORMESSAGE, self::DESCRIPTOR_INVALID_EXCEPTION_ERRORCODE);
            } else
                throw new Exception("-->Descriptor at $descriptor_path not found");
        } catch (Exception $e) {
            print $e->getMessage();
        }
    }

    public function getDescriptor()
    {
        return $this->_xmlDescriptor;
    }

    public function toSimpleXml()
    {
        try {
            if ($this->_xmlDescriptor_as_simplexml != null)
                return $this->_xmlDescriptor_as_simplexml;

            if ($this->_xmlDescriptor != null)
            {
                $this->_xmlDescriptor_as_simplexml = simplexml_import_dom($this->_xmlDescriptor);
                return $this->_xmlDescriptor_as_simplexml;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function fromSimpleXml($simplexml)
    {
        try {
            $xmlstring = $simplexml->asXML();
            if($xmlstring===false) throw new Exception("Invalid Xml specified");
            $this->_xmlDescriptor = DOMDocument::loadXML($xmlstring);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function fromDOM($node)
    {
        try {

            $this->_xmlDescriptor = $node;
        } catch (Exception $e) {
            print $e->getMessage();
        }
    }

    public function dump()
    {
        return $this->_xmlDescriptor->saveXML();
    }

}