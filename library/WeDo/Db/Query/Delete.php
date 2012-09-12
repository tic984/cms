<?
class WeDo_Db_Query_Delete extends WeDo_Db_Query
{
	private $_delete;
	
	private $_from;
	
	private $_where;
	
	private $_having;
	
	private $_limit;

	public function __construct()
	{
		parent::__construct();
		$this->_delete = '';
		
		$this->_from = array();
		$this->_where = array();
		
		$this->_having = array();
		
		$this->_limit = '';

	}
	/*
	 * improves readability
	 */
	public function delete()
	{
		return $this;	
	}

	public function from($table)
	{
		if(is_array($table))
		{
			foreach($table as $pos => $tok)
			{
				if(is_int($pos)) $this->_from[] = $tok;
				else $this->_from[] = sprintf("%s AS %s", $pos, $tok);
			}
		}
		else $this->_from[] = "$table";
		return $this;
	}

	public function where($fields)
	{
		$and = (count($this->_where) != 0) ? ' AND ' : '';

		if(is_array($fields))
		{
			foreach($fields as $pos => $fields)
			{
				if(is_int($pos))
					$this->_where[] = $and . $fields;

				else $this->_where[] = $and . str_replace('?', $fields, $pos);
			}
		}
		else $this->_where[] = $and . $fields;
		return $this;
	}

	public function orWhere($fields)
	{
		if(is_array($fields))
		{
			foreach($fields as $pos => $fields)
			{
				if(is_int($pos)) $this->_where[] = ' OR '.$fields;
				else $this->_where[] = ' OR '.str_replace('?', $fields, $pos);
			}
		}
		else $this->_where[] = ' OR '.$fields;
		return $this;
	}

	public function limit($start, $len)
	{
		$this->_limit = sprintf(" LIMIT %s, %s ", $start, $len);
		return $this;
	}

	public function having($conditions)
	{
		if(is_array($conditions))
		{
			foreach($conditions as $c)
			$this->_having[] = $c;
		} else
		$this->_having[] = $conditions;
		return $this;
	}


	public function getQuery()
	{
		$sql_delete = 'DELETE ';
		
			
		$sql_from =   (empty($this->_from)) 		? '   ' : ' FROM '.DbHelper::arrayToSql($this->_from);

		
		if(!empty($this->_where))
		{
			$sql_where = ' WHERE ';
			foreach($this->_where as $w)
			$sql_where .= $w;
		}

		
		$sql_having = (empty($this->_having)) 	? '   ' : ' HAVING '.DbHelper::arrayToSql($this->_having);	

		$sql_limit = (empty($this->_limit)) 		? '   ' : $this->_limit;
			
		$this->_sql = $sql_delete.$sql_from.$sql_where.$sql_having.$sql_limit;

		return parent::getQuery();
	}

	public function toCountQuery()
	{
		$q = new WeDo_Db_Query_Select();
		$q->addSelect('COUNT(catalogo.id) AS howmany');
		foreach($this->getFrom() as $from)
		$q->addFrom($from);
		foreach($this->getWhere()->getAnd() as $w_and)
		$q->addWhereAnd($w_and);
		foreach($this->getWhere()->getOr() as $w_or)
		$q->addWhereOr($w_or);
		foreach($this->getJoin() as $ob)
		$q->addJoin($ob);
		return $q->getQuery();
	}

}
?>