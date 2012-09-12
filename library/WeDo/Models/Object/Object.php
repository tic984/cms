<?php

class WeDo_Models_Object_Object
{
    /**
     *
     * Associative array representing the internal status of the object.
     * @var array
     */
    protected $_map;

    /**
     *
     * Item id
     * @var int
     */
    protected $_id;

    /**
     *
     * Object status.
     * Avaialable values are 'active', 'draft', 'revision', 'deleted'
     * @var string
     */
    protected $_status;

    /**
     *
     * Creation Timestamp (string, formatted for mysql insertion)
     * @var string
     */
    protected $_ts_insert;

    /**
     *
     * Update Timestamp (string, formatted for mysql insertion)
     * @var string
     */
    protected $_ts_update;

    /**
     *
     * Deleted Timestamp (string, formatted for mysql insertion)
     * @var string
     */
    protected $_ts_delete;
    
     /**
     *
     * ClassUri
     * @var string
     */
    protected $_classUri;

    const STATUS_DRAFT = 'draft';
    const STATUS_ACTIVE = 'active';
    const STATUS_DELETED = 'deleted';

    public function __construct($classUri)
    {
        try {
            if($classUri instanceof WeDo_ClassURI)
                $this->_classUri = $classUri;
            else
                $this->_classUri = WeDo_ClassURI::fromString ($classUri);
            WeDo_Application::getSingleton('app/WeDo_ModuleManager')->getMapFor($this->_classUri);
            foreach(WeDo_Application::getSingleton('app/WeDo_ModuleManager')->getClassDescriptor($this->_classUri)->getUntranslatedFields() as $fieldName)
                $this->_map[$fieldName] = '';
            $this->_id = '-1';
            $this->_status = WeDo_Application::getSingleton('app/WeDo_ModuleManager')->getModuleDescriptor($this->_classUri->getPrefix())->getClassDefaultStatus($this->_classUri->getClassName());
            $this->_ts_insert = date("Y-m-d h:i:s");
            $this->_ts_update = date("Y-m-d h:i:s", 0);
            $this->_ts_delete = date("Y-m-d h:i:s", 0);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function get($item)
    {
       if (array_key_exists($item, $this->_map))
            return $this->_map[$item];
    }

    public function set($item, $value)
    {
        if (array_key_exists($item, $this->_map))
            $this->_map[$item] = $value;
        return $this;
    }

    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function setStatus($status)
    {
        $this->_status = $status;
        return $this;
    }

    public function getStatus()
    {
        return $this->_status;
    }

    public function setTsInsert($ts)
    {
        $this->_ts_insert = $ts;
        return $this;
    }

    public function getTsInsert()
    {
        return $this->_ts_insert;
    }

    public function setTsUpdate($ts)
    {
        $this->_ts_update = $ts;
        return $this;
    }

    public function getTsUpdate()
    {
        return $this->_ts_update;
    }

    public function setTsDelete($ts)
    {
        $this->_ts_delete = $ts;
        return $this;
    }

    public function getTsDelete()
    {
        return $this->_ts_delete;
    }

    public function _fromMap($map)
    {
        foreach ($map as $prop => $val)
            $this->set($prop, $val);
       
        $map['status'] = (isset($map['status'])) ? $map['status'] : 'active';
        $this->setStatus($map['status'])
                ->setId($map['id']);

        if (isset($map['ts_insert']))
            $this->setTsInsert($map['ts_insert']);
        if (isset($map['ts_update']))
            $this->setTsUpdate($map['ts_update']);
        if (isset($map['ts_delete']))
            $this->setTsDelete($map['ts_delete']);
    }
    
    public function _toMap()
    {
        $map = $this->_map;
        $map['status'] = $this->getStatus();
        $map['id'] = $this->getId();
        $map['ts_insert'] = $this->getTsInsert();
        $map['ts_update'] = $this->getTsUpdate();
        $map['ts_delete'] = $this->getTsDelete();
        return $map;
    }
    
    //strips all slashes
    public function _fromDb($map)
    {
        foreach ($map as $prop => $val)
            $this->set($prop, stripslashes($val));
       
        $map['status'] = (isset($map['status'])) ? $map['status'] : 'active';
        $this->setStatus($map['status'])
                ->setId($map['id']);

        if (isset($map['ts_insert']))
            $this->setTsInsert($map['ts_insert']);
        if (isset($map['ts_update']))
            $this->setTsUpdate($map['ts_update']);
        if (isset($map['ts_delete']))
            $this->setTsDelete($map['ts_delete']);
    }
    
    public function _toDb()
    {
        return $this->_toMap();
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
        if(filter_has_var($requestType, 'id'))
                $this->_id = filter_input($requestType, 'id');
        
    }
    
    public function getClassUri()
    {
        return $this->_classUri;
    }

}

//
//abstract class WeDo_Models_Object_ObjectMapper
//{
//    private $connection;
//    
//    public function byId($paramId)
//    {
//        try {
//            
//            $moduleName = $classUri->getPrefix();
//            $className = $classUri->getClassName();
//            
//            $query = WeDo_Models_Foundation_QueryHelper::loadById($params, $classUri, $moduleDescriptor, $classDescriptor);
//            
//            if ($moduleDescriptor->classHasTranslatedFields($className))
//                $query = WeDo_Models_Foundation_Translated_QueryHelper::loadById($query, $params, $className, $moduleDescriptor, $classDescriptor);
//            /* qui potrei aggiungere tutte le altre 'implementazioni' ad esempio se � multisite, etc etc */
//            $relations = array();
//
//            if ($moduleDescriptor->classHasRelations($className))
//            {
//                $reldefsdescriptor = WeDo_Application::getSingleton('defs/WeDo_Defs_Relations');
//
//                require_once APPLICATION_PATH . "/code/WeDo_Relations_Helper.class.php";
//
//                foreach ($classDescriptor->getRelations() as $relationFieldName)
//                {
//                    $relname = $classDescriptor->getRelDef($relationFieldName);
//                    $model = $reldefsdescriptor->getRelationModel($relname); //intable or centralized
//                    $reltype = $reldefsdescriptor->getRelationType($relname); //1,n or n,1 or n,m
//                    $relating = $reldefsdescriptor->getRelating($relname); //who is relating
//                    $related = $reldefsdescriptor->getRelated($relname); //who is related
//                    $myrole = ($relating == $classUri) ? Reldefs::ROLE_RELATING : Reldefs::ROLE_RELATED;
//                    $other_role = ($myrole == Reldefs::ROLE_RELATING) ? Reldefs::ROLE_RELATED : Reldefs::ROLE_RELATING;
//
//                    if (empty($relname))
//                        throw new Exception("Relation '$relationFieldName' has no valid reldef!");
//                    //Extract foreign keys based on other actor's role
//                    //These are fields whose value will be extracted in the query.
//                    //I extract in the main query all fields that can be extracted (1n).
//                    //actually, it might be better to pick them in a different query, but i'm not sure about.
//
//                    $fkeys = $reldefsdescriptor->getForeignKeys($relname, $other_role);
//
//                    switch ($reltype)
//                    {
//                        case '1,n':
//                            if ($myrole == Reldefs::ROLE_RELATING)
//                                WeDo_Relations_Helper::fetch1nStraight($query, $model, $relname, $relationFieldName, $related, $lang, $fkeys);
//                            else
//                                WeDo_Relations_Helper::fetch1nInverse($query, $model);
//                            break;
//                        case 'n,1':
//                            if ($myrole == Reldefs::ROLE_RELATING)
//                                $relations[$relationFieldName] = WeDo_Relations_Helper::fetchn1Straight($id, $model, $relname, $relationFieldName, $related, $lang, $fkeys, $connection);
//                            else
//                                ; //$arr_n1[$relationFieldName] = WeDo_Relations_Helper::fetchn1Reverse();
//                            break;
//                        case 'n,m':
//                            if ($myrole == Reldefs::ROLE_RELATING)
//                                $relations[$relationFieldName] = WeDo_Relations_Helper::fetchnmStraight($id, $model, $relname, $related, $lang, $fkeys, $connection);
//                            else
//                                $relations[$relationFieldName] = WeDo_Relations_Helper::fetchnmInverse($query, $relationFieldName, $related, $lang, $fkeys);
//                            break;
//                    }
//                }
//            }
//
//            $map = $connection->fetchAssociative($query->getQuery());
//           
//            return new WeDo_Models_RawObject($map, $relations);
//        } catch (Exception $e) {
//            throw $e;
//        }
//    }
//    
//    public function allAsObjects($criteriaAsArray, $displayContext)
//    {
//        
//    }
//    
//    public function allAsList($criteriaAsArray, $displayContext)
//    {
//        
//    }
//    
//    public function fields($criteriaAsArray, $displayContext)
//    {
//        
//    }
//    
//    public function count($criteriaAsArray)
//    {
//        
//    }
//    
//    public function save(WeDo_Models_Object_Object $object)
//    {
//        
//    }
//    
//    public function update(WeDo_Models_Object_Object $object)
//    {
//        
//    }
//    
//    public function updateFields(&$criteriaAsArray, &$contentAsArray)
//    {
//        
//    }
//    
//    public function delete(&$criteriaAsArray)
//    {
//        
//    }
//    
//}

?>