<?php
class SessionManager
{
	private $_session;
	
	public function __construct()
	{
		$this->_session = new Zend_Session_Namespace();
	}

	public function save($obj, $name)
	{
		$this->_session->$name = $obj;
//		Logger::getLogger(__CLASS__)->info("Ho salvato in sessione con il nome $name 'sta roba: $obj");
	}
	
	public function get($name)
	{
		if($this->isIn($name))
			return $this->_session->$name;
	}
	public function remove($name)
	{
		if($this->isIn($name))
			unset($this->_session->$name);
	}
	
	public function isIn($name)
	{
		return isset($this->_session->$name);
	}
	
}
?>