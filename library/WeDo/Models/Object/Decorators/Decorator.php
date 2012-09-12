<?php
class WeDo_Models_Object_Decorators_Decorator
{
    protected $_object;
     
    protected function getClassUri()
    {
        return $this->_object->getClassUri();
    }
    
    public function getId()
    {
        return $this->_object->getId();
    }
    
    public function get($item)
    {
       return $this->_object->get($item);
    }

    public function set($item, $value)
    {
        $this->_object->set($item, $value);
        return $this;
    }
    
    public function _fromMap($map)
    {
        $this->_object->_fromMap($map);
    }
    
    public function _toMap()
    {
       $map = $this->_object->_toMap();
       return $map;
    }  
    
    //strips all slashes
    public function _fromDb($map)
    {
       $this->_object->_fromDb($map);
    }
    
    public function _toDb()
    {
        $map = $this->_object->_toDb();
        return $map;
    }

    public function _fromRequest($requestType = INPUT_POST) {
       $this->_object->_fromRequest($requestType);
    }
}
?>
