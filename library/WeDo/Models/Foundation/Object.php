<?php

abstract class WeDo_Models_Foundation_Object
{

    /**
     *
     * Associative array representing the internal status of the object.
     * @var array
     */
    protected $_map;

    /**
     *
     * Associative array representing relations of the object.
     * @var unknown_type
     */
    protected $_related;

    /**
     *
     * Lang in which content is stored.
     * @var char(2)
     */
    protected $_lang;

    /**
     *
     * Owner's Id
     * @var int
     */
    protected $_owner;

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

    const STATUS_DRAFT = 'draft';
    const STATUS_REVISION = 'revision';
    const STATUS_ACTIVE = 'active';
    const STATUS_DELETED = 'deleted';

    protected function __construct(WeDo_ClassURI $classUri)
    {
        try {
            static::$_classUri = $classUri;
            $this->_map = WeDo_Application::getSingleton('app/WeDo_ModuleManager')->getMapFor($classUri);
            $this->_related = WeDo_Application::getSingleton('app/WeDo_ModuleManager')->getRelationsFor($classUri);
            $this->_lang = 'it';
            $this->_owner = '1';
            $this->_id = '-1';
            $this->_status = WeDo_Application::getSingleton('app/WeDo_ModuleManager')->getModuleDescriptor($classUri->getPrefix())->getClassDefaultStatus($classUri->getClassName());
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

    public function setLang($lang)
    {
        $this->_lang = $lang;
        return $this;
    }

    public function getLang()
    {
        return $this->_lang;
    }

    public function setOwner($owner)
    {
        $this->_owner = $owner;
        return $this;
    }

    public function getOwner()
    {
        return $this->_owner;
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
    
    

    protected function setRelationValue($relationName, $fields)
    {
        if (isset($this->_related[$relationName]))
        {
            foreach ($fields as $row)
            {
                $id = $row['id'];
                unset($row['id']);
                $this->_related[$relationName][$id] = $row;
            }
        }
        return $this;
    }

    public function _export($format)
    {
        switch ($format)
        {
            case 'json':
                if (function_exists('json_encode'))
                    return json_encode(get_object_vars($this));
                break;
            case 'array':
                return get_object_vars($this);
                break;
            case 'xml':

                break;
            case 'string':
                return serialize($this);
                break;
        }
    }

    static public function getClassName()
    {
        return __CLASS__;
    }

    static public function getClassUri()
    {
        if (static::$_classUri == null)
            static::$_classUri = WeDo_Helpers_Application::getClassUri(static::CLASS_MODULE, __CLASS__);
        return static::$_classUri;
    }

    public function _fromMap($map)
    {
        
        foreach ($map as $prop => $val)
            $this->set($prop, $val);

        if ($this->_hasTranslatedFields())
            $this->setLang($map['lang']);
       
        $map['status'] = (isset($map['status'])) ? $map['status'] : 'active';
        $this->setOwner($map['owner'])
                ->setStatus($map['status'])
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
        foreach (get_class_vars(__CLASS__) as $k => $v)
            $map[$k] = $v;
        return $map;
    }
    
    //strips all slashes
    public function _fromDb($map)
    {
        
        foreach ($map as $prop => $val)
            $this->set($prop, stripslashes($val));

        if ($this->_hasTranslatedFields())
            $this->setLang($map['lang']);
       
        $map['status'] = (isset($map['status'])) ? $map['status'] : 'active';
        $this->setOwner($map['owner'])
                ->setStatus($map['status'])
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
        $map = array();
        foreach (get_object_vars($this) as $k => $v)
            $map[$k] = $v;
        return $map;
    }

    public static function _fromRawObject($ro)
    {
        $map = $ro->getMap();
        $obj = self::_fromMap($map);
        return $obj;
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
        if(filter_has_var($requestType, 'owner'))
                $this->_owner = filter_input($requestType, 'owner');
        
    }

    private function _hasTranslatedFields()
    {
        return WeDo_Application::getSingleton('app/WeDo_ModuleManager')
                        ->getModuleDescriptor($this->getClassUri()->getPrefix())
                                ->classHasTranslatedFields($this->getClassUri()->projectToZendClassName());
    }
}