<?php
class EavObjectService extends ObjectService
{

	public function loadById($params)
	{
		try {
                    $id = $params['id'];
                    $lang = $params['lang'];
			return EavObjectDal::loadById($id, $lang, $this->_classUri, $this->_moduleDescriptor, $this->_classDescriptor, $this->getConnection());
		} catch (Exception $e) { throw $e; }
	}

	public function getList($lang, $displayContext)
	{
		try {
			return EavObjectDal::getList($lang, $this->_classUri, $this->_moduleDescriptor, $this->_classDescriptor, $displayContext, $this->getConnection());
		} catch (Exception $e) { throw $e; }
	}

	public function count($params){}

	public function countList($params) {}

	public function insert(&$object){}
	public function save(&$object) {}
	public function update(&$object){}
	public function toggleField(){}
	public function getFieldValue(){}
	public function import(){}
	public function export(){}
	public function translate(){}

	public function loadBy($id, $lang, $field) {}
	public function all($params, $displayContext) {}

}