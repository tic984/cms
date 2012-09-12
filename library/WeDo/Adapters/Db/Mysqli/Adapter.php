<?php
class WeDo_Adapters_Db_Mysqli_Adapter extends WeDo_Adapters_Adapter
{
	private $_connection;

	public function __construct($resource)
	{
		try
		{
			$link = new mysqli
			(
			$resource->getProperty('host'),
			$resource->getProperty('login'),
			$resource->getProperty('password'),
			$resource->getProperty('dbname')
			);

			if ($link->connect_error) { throw new Exception($link->connect_errno, $link->connect_error); }
			$this->_connection = $link;
		} catch (Exception $e)
		{
			throw $e;
		}
	}
}
?>