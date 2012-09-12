<?php
require_once(APPLICATION_PATH."/code/core/db/WeDo_Db_Query_Select.class.php");
require_once(APPLICATION_PATH."/code/core/db/InsertQuery.class.php");
require_once(APPLICATION_PATH."/code/core/db/UpdateQuery.class.php");
require_once(APPLICATION_PATH."/code/core/db/DeleteQuery.class.php");

class DatabaseConnection extends Connection
{
	private $_adapter;

	public function __construct($resource)
	{
		parent::__construct($resource);
		switch($this->getResource()->getProperty('db_type'))
		{
			case 'mysql':
				{
					require_once APPLICATION_PATH.'/code/core/adapters/MysqlAdapter.class.php';
					$this->_adapter =  new MysqlAdapter($resource);
					break;
				}
			case 'mysqli':
				{
					require_once APPLICATION_PATH.'/code/core/adapters/MysqliAdapter.class.php';
					$this->_adapter =  new MysqliAdapter($resource);
					break;
				}
			case 'pdo':
				{
					break;
				}
		}
	}
	public function getConnection()
	{
		return $this->_adapter;
	}

}
?>