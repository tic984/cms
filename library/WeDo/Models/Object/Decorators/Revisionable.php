<?php

class WeDo_Models_Object_Decorators_Revisionable extends WeDo_Models_Object_Decorators_Decorator {

    private $_revId;

    const PARENT_REVID = 0;

    public function __construct($object, $revId = self::PARENT_REVID) {
        $this->_object = $object;
        $this->_revId = $revId;
    }

    public function getRevId() {
        return $this->_revId;
    }

    public function setRevId($res) {
        $this->_revId = $res;
        return $this;
    }

    public function _fromMap($map) {
        parent::_fromMap($map);
        $this->_revId = $map['rev_id'];
    }

    public function _toMap() {
        $map = parent::_toMap();
        $map['rev_id'] = $this->_revId;
        return $map;
    }

    //strips all slashes
    public function _fromDb($map) {
        parent::_fromDb($map);
        $this->_revId = $map['rev_id'];
    }

    public function _toDb() {
        $map = parent::_toDb();
        $map['rev_id'] = $this->_revId;
        return $map;
    }

    public function _fromRequest($requestType = INPUT_POST) {
        parent::_fromRequest($requestType);
        if (filter_has_var($requestType, 'rev_id'))
            $this->_revId = filter_input($requestType, 'rev_id');
    }

}

?>
