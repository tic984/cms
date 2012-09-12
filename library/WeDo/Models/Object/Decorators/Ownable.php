<?php

class WeDo_Models_Object_Decorators_Ownable extends WeDo_Models_Object_Decorators_Decorator
{
    private $_owner;
    

    public function __construct($object, $revId = self::PARENT_REVID) {
        $this->_object = $object;
        $this->_owner = $revId;
    }

    public function getOwner() {
        return $this->_owner;
    }

    public function setOwner($res) {
        $this->_owner = $res;
        return $this;
    }

    public function _fromMap($map) {
        parent::_fromMap($map);
        $this->_owner = $map['owner'];
    }

    public function _toMap() {
        $map = parent::_toMap();
        $map['owner'] = $this->_owner;
        return $map;
    }

    //strips all slashes
    public function _fromDb($map) {
        parent::_fromDb($map);
        $this->_owner = $map['owner'];
    }

    public function _toDb() {
        $map = parent::_toDb();
        $map['owner'] = $this->_owner;
        return $map;
    }

    public function _fromRequest($requestType = INPUT_POST) {
        parent::_fromRequest($requestType);
        if (filter_has_var($requestType, 'owner'))
            $this->_owner = filter_input($requestType, 'owner');
    }
}
?>
