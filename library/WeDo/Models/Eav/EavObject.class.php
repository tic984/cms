<?php
class EavObject
{
	/**
	 *
	 * Associative array representing the internal status of the object.
	 * These fields are those wich are shared, and memorized on the _entity table.
	 * @var array
	 */
	protected $_map;
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
	 * Object Uri
	 * @var string
	 */
	protected $_classUri;

	/**
	 *
	 * Whether object has been deleted or not
	 * @var char(1)
	 */
	protected $_deleted;

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
	 * Attributes found. These will not be requested to an external entity,
	 * instead whenever an item will not found on _map, will be written here
	 * @var unknown_type
	 */
	protected $_attributes;

	protected function __construct($classUri)
	{
		$this->_map = WeDo_Application::getSingleton('app/WeDo_ModuleManager')->getMapFor($classUri);

		$this->_lang = 'it';
		$this->_owner = '1';
		$this->_id = '-1';
		$this->_classUri = $classUri;
		$this->_deleted= 'N';
		$this->_ts_insert = date("Y-m-d h:i:s");
		$this->_ts_update = 0;
		$this->_ts_delete = 0;

//		Logger::getLogger(__CLASS__)->info("eavObjectClass/$classUri created");
	}

	public function get($item)
	{
		if(isset($this->_map[$item]))
		return $this->_map[$item];
		else if(isset($this->_attributes[$item]))
		return $this->_attributes[$item];
	}

	public function set($item, $value)
	{
		if(isset($this->_map[$item]))
		$this->_map[$item] = $value;
		$this->_attributes[$item] = $value;
		return $this;
	}

	public function setId($id){
		$this->_id = $id;
		return $this;
	}

	public function getId(){
		return $this->_id;
	}

	public function setLang($lang){
		$this->_lang = $lang;
		return $this;
	}

	public function getLang(){
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

	public function setDeleted($deleted)
	{
		$this->_deleted = $deleted;
		return $this;
	}
	public function getDeleted()
	{
		return $this->_deleted;
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

	public function _toMap()
	{
		$map = $this->_map;
		$map['id'] = $this->getId();
		$map['lang'] = $this->getLang();
		$map['owner'] = $this->getOwner();
		$map['deleted'] = $this->getDeleted();
		$map['ts_insert'] = $this->getTsInsert();
		$map['ts_update'] = $this->getTsUpdate();
		$map['ts_delete'] = $this->getTsDelete();
		return array_merge($map, $this->_attributes);
	}
}