<?php
/**
 * Description of ClassURI
 *
 * @author Alessio
 */
class WeDo_ClassURI
{

    public $_className;
    public $_prefix;

    public function getClassName()
    {
        return $this->_className;
    }

    public function setClassName($_className)
    {
        $this->_className = $_className;
        return $this;
    }

    public function getPrefix()
    {
        return $this->_prefix;
    }

    public function setPrefix($_prefix)
    {
        $this->_prefix = $_prefix;
        return $this;
    }

    public function __construct($_prefix, $_className)
    {
        $this->_className = trim(ucfirst($_className));
        $this->_prefix = trim(ucfirst($_prefix));
    }

    public static function fromString($string)
    {
        try {
            if (strpos($string, "/") === false)
                throw new Exception("string '$string' is not a valid string for classUri");
            list($prefix, $cname) = explode("/", $string);
            return new WeDo_ClassURI(trim($prefix), trim($cname));
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    public function toString()
    {
        return sprintf("%s/%s", $this->_prefix, $this->_className);
    }
    
    public function is_singleton()
    {
        if($this->_prefix == 'app' || $this->_prefix=='database' || $this->_prefix=='defs')
            return true;
        return false;
    }
    
    public function projectToZendClassName()
    {
        return sprintf("Project_%s_Model_%s", $this->getPrefix(), $this->getClassName());
    }
    
    public function projectToZendMapperName(&$classUri)
    {
        return sprintf("Project_%s_Mapper_%s", $this->getPrefix(), $this->getClassName());
    }

    public function encodeForUrl()
    {
        return strtolower(sprintf("%s/%s", $this->getPrefix(), $this->getClassName()));
    }
    
    public static function fromUrl()
    {
        if(filter_has_var(INPUT_GET, 'mod'))
                $mod = filter_input(INPUT_GET, 'mod', FILTER_SANITIZE_STRING);
        if(filter_has_var(INPUT_GET, 'obj'))
                $obj = filter_input(INPUT_GET, 'obj', FILTER_SANITIZE_STRING);
        return new WeDo_ClassURI($mod, $obj);
    }
    
    public static function fromRequest(Zend_Controller_Request_Http &$request, $default='')
    {
        if($request->has('mod') && $request->has('obj'))
        {
            $mod = $request->getParam('mod');
            $obj = $request->getParam('obj');
            return new WeDo_ClassURI($mod, $obj);
        } else return self::fromString ($default);
    }
}

?>
