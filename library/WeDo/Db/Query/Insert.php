<?php

class WeDo_Db_Query_Insert extends WeDo_Db_Query
{
	private $table;
	private $arr_items;
	private $return_method;

	const RETURN_ID = 1;
	const RETURN_TRUE_FALSE = 2;

	public function __construct($table, $returnMethod=self::RETURN_TRUE_FALSE)
	{
		parent::__construct();
		$this->table = $table;
		$this->arr_items = array();
		$this->return_method = $returnMethod;
	}

	public function addItemsArray($items)
	{
		try
		{ 
		if(empty($items)  || !is_array($items))
			throw new Exception("No array provided");
			foreach($items as $k => $v)
				$this->addItem($k, $v);
			return $this;
		} catch(Exception $e) {
			throw $e;
		}
	}
	public function addItem($k, $v)
	{
		$this->arr_items[$k] = $v;
		return $this;
	}

	public function getReturnMethod()
	{
		return $this->return_method;
	}

	public function getQuery()
	{
		$fields = array(WeDo_Db_Helper::quote($this->table));
		$query_fields = '';
		$query_values = '';
		$pos = 1;
		$len = count($this->arr_items)+1;
		foreach($this->arr_items as $k=>$v)
		{
			$query_fields.= " %s, ";
			$query_values.= " '%s', ";
			$fields[$pos] = WeDo_Db_Helper::quote($k);
			$fields[$pos+$len] = mysql_real_escape_string($v);
			$pos++;
		}
		ksort($fields);
		$query = "INSERT INTO %s ( ".substr($query_fields, 0, -2)." ) VALUES ( ".substr($query_values, 0, -2)." )";
		$this->_sql = vsprintf($query, $fields);
		return parent::getQuery();
	}
	
	
}
?>