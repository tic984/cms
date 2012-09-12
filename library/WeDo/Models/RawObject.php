<?php
/**
 *
 * Wraps an object as is retrieved from Db.
 * Its purpose is to be filled by dal, return to service
 * and be passed to real object constructor.
 *
 *
 * @author Alessio
 *
 */
class WeDo_Models_RawObject
{
	private $_map;
	private $_relations;

	public function __construct($map, $relations)
	{
		$this->_map = $map;
		$this->_relations = $relations;
	}

	public function getMap()
	{
		return $this->_map;
	}
	public function getRelations()
	{
		return $this->_relations;
	}
}
?>