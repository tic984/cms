<?php
class WeDo_Db_Query
{
	
	const RIGHT_JOIN_ON = 1;
	const LEFT_JOIN_ON = 2;
	const INNER_JOIN_ON = 3;

	const RIGHT_JOIN_USING = 4;
	const LEFT_JOIN_USING = 5;
	const INNER_JOIN_USING = 6;
	
	protected $_sql;
	
	public function __construct()
	{
		$this -> _sql = '';
	}
	
	protected function getQuery()
	{
		return $this -> _sql;
	}
}