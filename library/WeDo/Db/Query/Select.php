<?
class WeDo_Db_Query_Select extends WeDo_Db_Query
{
	private $_select;
	private $_distinct;
	private $_from;
	private $_join;
	private $_where;
	private $_groupBy;
	private $_having;
	private $_orderBy;
	private $_limit;

	public function __construct()
	{
		parent::__construct();
		$this->_select = array();
		$this->_distinct = false;
		$this->_from = array();
		$this->_where = array();
		$this->_groupBy = array();
		$this->_having = array();
		$this->_orderBy = array();
		$this->_join = array();
		$this->_limit = '';

	}

	public function distinct()
	{
		$this->_distinct = true;
	}

	private function quote($field)
	{
		return $field;
	}
	/**
	 *
	 * $q->select(array(campo1 => c1, campo2 =>c2)))
	 * oppure
	 * $q->select(array(campo1, campo2, campo3))
	 * oppure
	 * $q->select(campo1)
	 * @param unknown_type $c
	 */
	public function select($fields = '*')
	{
		if(is_array($fields))
		{
			foreach($fields as $pos => $tok)
			{
				if(is_int($pos)) $this->_select[] = WeDo_Db_Helper::quote($tok);
				else $this->_select[] = sprintf("%s AS %s", WeDo_Db_Helper::quote($pos), WeDo_Db_Helper::quote($tok));
			}
		}
		else
		if(trim($fields)!='*')
			$this->_select[] = WeDo_Db_Helper::quote($fields);
		return $this;
	}
	
	public function selectFunction($function, $field)
	{
		if(!is_array($field))
			$this->_select[] = sprintf(" %s( %s )", $function, WeDo_Db_Helper::quote($field));
		else 
		{
			$field= current(array_keys($field));
			$alias = current(array_values($field));
			$this->_select[] = sprintf(" %s( %s ) AS %s", $function, WeDo_Db_Helper::quote($field), $alias);
		}	
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

	public function rigthJoinOn($table, $options)
	{
		return self::doJoinOn($table, $options, WeDo_Db_Query::RIGHT_JOIN_ON);
	}
	public function leftJoinOn($table, $options)
	{
		return self::doJoinOn($table, $options, WeDo_Db_Query::LEFT_JOIN_ON);
	}
	public function innerJoinOn($table, $options)
	{
		return self::doJoinOn($table, $options, WeDo_Db_Query::INNER_JOIN_ON);
	}

	public function rigthJoinUsing($table, $options)
	{
		return self::doJoinOn($table, $options, WeDo_Db_Query::RIGHT_JOIN_USING);
	}
	public function leftJoinUsing($table, $options)
	{
		return self::doJoinOn($table, $options, WeDo_Db_Query::LEFT_JOIN_USING);
	}
	public function innerJoinUsing($table, $options)
	{
		return self::doJoinOn($table, $options, WeDo_Db_Query::INNER_JOIN_USING);
	}

	private function doJoinOn($table, $options, $join_method)
	{
		$tpl = '';
		switch($join_method)
		{
			case WeDo_Db_Query::RIGHT_JOIN_ON:
				$tpl = (is_array($table)) ? " RIGHT JOIN %s AS %s ON %s " : " RIGHT JOIN %s ON %s";
				break;
			case WeDo_Db_Query::LEFT_JOIN_ON:
				$tpl = (is_array($table)) ? " LEFT JOIN %s AS %s ON %s " : " RIGHT JOIN %s ON %s";
				break;
			case WeDo_Db_Query::INNER_JOIN_ON:
				$tpl = (is_array($table)) ? " INNER JOIN %s AS %s ON %s " : " INNER JOIN %s ON %s";
				break;
			case WeDo_Db_Query::RIGHT_JOIN_USING:
				$tpl = (is_array($table)) ? " RIGHT JOIN %s AS %s USING (%s) " : " RIGHT JOIN %s USING (%s)";
				break;
			case WeDo_Db_Query::LEFT_JOIN_USING:
				$tpl = (is_array($table)) ? " LEFT JOIN %s AS %s USING (%s) " : " RIGHT JOIN %s USING (%s)";
				break;
			case WeDo_Db_Query::INNER_JOIN_USING:
				$tpl = (is_array($table)) ? " INNER JOIN %s AS %s USING (%s) " : " INNER JOIN %s USING (%s)";
				break;
		}

		if(is_array($table))
		{
			$tablename = current(array_keys($table));
			$alias =  current(array_values($table));
			$this->_join[] = sprintf($tpl, $tablename, $alias, $options);
		}
		else $this->_join[] = sprintf($tpl, $tablename, $options);
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

	public function group($fields)
	{
		if(is_array($fields))
		{
			foreach($fields as $f)
			$this->_groupBy[] = $f;
		} else
		$this->_groupBy[] = $fields;
		return $this;
	}

	public function order($fields)
	{
		if(is_array($fields))
		{
			foreach($fields as $f)
			$this->_orderBy[] = $f;
		} else
		$this->_orderBy[] = $fields;
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
		$sql_select = 'SELECT ';
		$sql_select.= ($this->_distinct ) ? ' DISTINCT ' : '';

		$sql_select.= (empty($this->_select)) 	? ' * ' : WeDo_Db_Helper::arrayToSql($this->_select);
			
		$sql_from =   (empty($this->_from)) 		? '   ' : ' FROM '.WeDo_Db_Helper::arrayToSql($this->_from);
                $sql_where = '';
		if(!empty($this->_join))
		{
			foreach($this->_join as $join)
			$sql_from.= $join;
		}
		if(!empty($this->_where))
		{
			$sql_where = ' WHERE ';
			foreach($this->_where as $w)
			$sql_where .= $w;
		}

		$sql_groupBy = (empty($this->_groupBy)) 	? '   ' : ' GROUP BY '.WeDo_Db_Helper::arrayToSql($this->_groupBy);
		$sql_having = (empty($this->_having)) 	? '   ' : ' HAVING '.WeDo_Db_Helper::arrayToSql($this->_having);
		$sql_orderBy = (empty($this->_orderBy)) 	? '   ' : ' ORDER BY '.WeDo_Db_Helper::arrayToSql($this->_orderBy);

		$sql_limit = (empty($this->_limit)) 		? '   ' : $this->_limit;
			
		$this->_sql = $sql_select.$sql_from.$sql_where.$sql_groupBy.$sql_having.$sql_orderBy.$sql_limit;

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