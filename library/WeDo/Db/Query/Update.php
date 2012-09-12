<?php

class WeDo_Db_Query_Update extends WeDo_Db_Query {

    private $table;
    private $arr_items;
    private $return_method;
    private $_where;

    const RETURN_NUM_ROWS_MODIFIED = 1;
    const RETURN_TRUE_FALSE = 2;

    public function __construct($table, $returnMethod = self::RETURN_TRUE_FALSE) {
        parent::__construct();
        $this->table = $table;
        $this->arr_items = array();
        $this->return_method = $returnMethod;
        $this->_where = array();
    }

    /**
     *
     * May receive:
     * 	-	array($cond1, cond2, cond3 )
     *  -	array($cond => $replace)
     *  -	$cond;
     * @param $fields
     */
    public function where($fields) {
        $prepend_and = (count($this->_where) != 0);
        $token = '';
        if (is_array($fields)) {
            $first_item = true;
            foreach ($fields as $pos => $fields) {
                $token = ($prepend_and || !$first_item) ? ' AND ' : '';
                $first_item = false;
                if (is_int($pos))
                    $token .= $fields;

                else
                    $token .= str_replace('?', $fields, $pos);
                $this->_where[] = $token;
                $first_item = false;
            }
        }
        else
            $this->_where[] = $and . $fields;
        return $this;
    }

    public function orWhere($fields) {
        if (is_array($fields)) {
            foreach ($fields as $pos => $fields) {
                if (is_int($pos))
                    $this->_where[] = ' OR ' . $fields;
                else
                    $this->_where[] = ' OR ' . str_replace('?', $fields, $pos);
            }
        }
        else
            $this->_where[] = ' OR ' . $fields;
        return $this;
    }

    public function add($items) {
        foreach ($items as $field => $value)
            $this->arr_items[] = sprintf(" %s = '%s' ", WeDo_Db_Helper::quote($field), mysql_real_escape_string($value));
        return $this;
    }

    public function getReturnMethod() {
        return $this->return_method;
    }

    public function getQuery() {
        $fields = array(WeDo_Db_Helper::quote($this->table));

        $sql_update = implode(",", $this->arr_items);

        $sql_where = '';

        if (!empty($this->_where)) {
            $sql_where = ' WHERE ';
            foreach ($this->_where as $w)
                $sql_where .= $w;
        }

        $query = "UPDATE %s SET $sql_update $sql_where";
        $this->_sql = vsprintf($query, $fields);
        return parent::getQuery();
    }

}

?>