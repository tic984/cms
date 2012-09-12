<?php

class WeDo_Models_Object_Decorators_Translatable extends WeDo_Models_Object_Decorators_Decorator
{
    
    private $_map;
    private $_lang;
    
    public function __construct($object, $lang='it') {
        $this->_object = $object;
        foreach(WeDo_Application::getSingleton('app/WeDo_ModuleManager')->getClassDescriptor($this->_object->getClassUri())->getTranslatedFields() as $fieldName)
            $this->_map[$fieldName] = '';
        $this->_lang = $lang;
    }
    
    public function getLang()
    {
        return $this->_lang;
    }
     
    public function setLang($lang)
    {
        $this->_lang = $lang;
        return $this;
    }
    
    
    public function get($item)
    {
       if (array_key_exists($item, $this->_map))
            return $this->_map[$item];
       return parent::get($item);
    }

    public function set($item, $value)
    {
        if (array_key_exists($item, $this->_map))
            $this->_map[$item] = $value;
        else parent::set($item, $value);
        return $this;
    }
    
    public function _fromMap($map)
    {
        parent::_fromMap($map);
        foreach ($map as $prop => $val)
            $this->set($prop, $val);
        $this->_lang = $map['lang'];
    }
    
    public function _toMap()
    {
        $map = parent::_toMap();
        $map = array_merge($map, $this->_map);
        $map['lang'] = $this->_lang;
        return $map;
    }   
    
    //strips all slashes
    public function _fromDb($map)
    {
       parent::_fromDb($map);
       foreach ($map as $prop => $val)
            $this->set($prop, stripslashes($val));
       $this->_lang = $map['lang'];
    }
    
    public function _toDb()
    {
        $map = parent::_toDb();
        $map = array_merge($map, $this->_map);
        $map['lang'] = $this->_lang;
        return $map;
    }

    public function _fromRequest($requestType = INPUT_POST) {
       parent::_fromRequest($requestType);
       switch ($requestType) {
            case INPUT_POST:
            case INPUT_GET:
                if (get_magic_quotes_gpc()) {
                    foreach ($this->_map as $k => $v) {
                        if (filter_has_var($requestType, $k)) {
                            $this->_map[$k] = filter_input($requestType, $k);
                        }
                    }
                } else {

                    foreach ($this->_map as $k => $v) {
                        if (filter_has_var($requestType, $k)) {
                            $v = addslashes(filter_input($requestType, $k));
                            $this->_map[$k] = $v;
                        }
                    }
                }
                break;
            default:
                break;
        }
        if(filter_has_var($requestType, 'lang'))
                $this->_lang = filter_input($requestType, 'lang');
        
    }
    
    
}
?>
