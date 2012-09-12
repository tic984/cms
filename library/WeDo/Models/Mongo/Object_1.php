<?php

abstract class WeDo_Models_Mongo_Object extends WeDo_Models_Foundation_Object {

    public function _toDb($for_update = false) {
        $map = array();

        if (!$for_update) {
            foreach (get_object_vars($this) as $k => $v)
                $map[$k] = $v;
            return $map;
        } else {
            foreach ($this->_map as $k => $v) {
                $pos = sprintf("_map.%s", $k);
                $map[$pos] = $v;
            }
            foreach (get_object_vars($this) as $k => $v) {
                if ($k == '_map')
                    continue;
                if ($k == '_id')
                    continue;
                if ($k == '_related')
                    continue;
                $map[$k] = $v;
            }
            return array('$set' => $map);
        }
    }

    public function _toMap() {
        $map = $this->_map;
        foreach (get_object_vars($this) as $k => $v) {
            if ($k == "_map")
                continue;
            $map[$k] = $v;
        }
        return $map;
    }

    public function _fromMap(array $map) {
        
        foreach ($map["_map"] as $prop => $val)
            $this->set($prop, $val);

        $map['_status'] = (isset($map['_status'])) ? $map['_status'] : 'active';
        $this->setOwner($map['_owner'])
                ->setStatus($map['_status'])
                ->setId($map['_id']->{'$id'});

        if (isset($map['_ts_insert']))
            $this->setTsInsert($map['_ts_insert']);
        if (isset($map['_ts_update']))
            $this->setTsUpdate($map['_ts_update']);
        if (isset($map['_ts_delete']))
            $this->setTsDelete($map['_ts_delete']);
        return $this;
    }

    public function _fromDb(array $map) {
        try {           
            foreach($map as $k => $v)
            {
                switch($k)
                {
                    case '_map':
                        foreach($map['_map'] as $prop => $val)
                            $this->set($prop, stripslashes($val));
                    break;
                    case '_status':
                        $this->setStatus($map['_status']);
                        break;
                    case '_ts_insert':
                        $this->setTsInsert($map['_ts_insert']);
                        break;
                    case '_ts_update':
                        $this->setTsUpdate($map['_ts_update']);
                        break;
                    case '_ts_delete':
                        $this->setTsDelete($map['_ts_delete']);
                        break;
                    case '_id':
                        $this->setId($map['_id']->{'$id'});
                        break;
                    case '_owner':
                        $this->setOwner($map['_owner']);
                        break;
                    
                }
            }
            return $this;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function _fromRequest($requestType = INPUT_POST) {
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
        if (filter_has_var($requestType, 'id'))
            $this->_id = filter_input($requestType, 'id');
        if (filter_has_var($requestType, 'owner'))
            $this->_owner = filter_input($requestType, 'owner');
    }

    
    //useful for updating fields
    static public function getContentForUpdate($arr)
    {
        $map = array();
        foreach($arr as $k => $v)
        {
            $pos = sprintf("_map.%s", $k);
            $map[$pos] = $v;
        }
        return array('$set' => $map);
    }
    
}