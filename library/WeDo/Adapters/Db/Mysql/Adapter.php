<?php

//require_once APPLICATION_PATH . "code/core/adapters/Adapter.class.php";

class WeDo_Adapters_Db_Mysql_Adapter extends WeDo_Adapters_Adapter
{

    private $_connection;
    private $_is_transaction_started;
    private $_host;
    private $_login;
    private $_password;
    private $_database_name;
    private $_upon_connection;
    
    
    const SINGLETON_NAMESPACE = 'database/';

    const LOG_QUERIES = false;

    public function __construct($conn_name, $host, $login, $password, $database_name, $upon_connection)
    {
        try {
            parent::__construct(self::SINGLETON_NAMESPACE, $conn_name);
            
            $this->_host = $host;
            $this->_login = $login;
            $this->_password = $password;
            $this->_database_name = $database_name;
            $this->_upon_connection = $upon_connection;
            
            $link = mysql_connect($host, $login, $password);
            if (!$link)
            {
                throw new Exception('db "'.$this->_database_name .'" not found exception at location "' . $this->_host .'"');
            }

            $db_selected = mysql_select_db($database_name, $link);

            if (!$db_selected)
            {
                throw new Exception("No Db Selected");
            }

            $this->_connection = $link;
            $this->_log_queries = false;
            $this->_is_transaction_started = false;

            $this->log('Mysql Connection started');

            if ($upon_connection != '')
                $this->execute($upon_connection);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function transactionStart()
    {
        $sql = "BEGIN";
        if ($this->_is_transaction_started)
            throw new Exception('transaction already started');
        $this->log($sql);
        mysql_query($sql, $this->_connection);
        $this->_is_transaction_started = true;
    }

    public function transactionCommit()
    {
        $sql = "COMMIT";
        if (!$this->_is_transaction_started)
            throw new Exception('transaction not started');
        $this->log($sql);
        mysql_query($sql, $this->_connection);
        $this->_is_transaction_started = false;
    }

    public function transactionRollback()
    {
        $sql = "ROLLBACK";
        if (!$this->_is_transaction_started)
            throw new Exception('transaction not started');
        $this->log($sql);
        mysql_query($sql, $this->_connection);
        $this->_is_transaction_started = false;
    }

    public function execute($sql)
    {
        try {
            $this->log($sql);
            $rs = mysql_query($sql, $this->_connection);
            if (!$rs)
            {
                throw new Exception(mysql_error(), mysql_errno());
            }
            return mysql_affected_rows();
        } catch (Exception $e) {
            $this->log($e->getMessage(), true);
            throw $e;
        }
    }
    
    public function insert($sql)
    {
        try {
            $this->log($sql);
            $rs = mysql_query($sql, $this->_connection);
            if (!$rs)
            {
                throw new Exception(mysql_error(), mysql_errno());
            }
            return mysql_insert_id($this->_connection);
        } catch (Exception $e) {
            $this->log($e->getMessage(), true);
            throw $e;
        }
    }

    public function isWithinTransaction()
    {
        return $this->_is_transaction_started;
    }

    public function fetchRow($sql)
    {
        try {
            $this->log($sql);
            $arr = array();
            $rs = mysql_query($sql, $this->_connection);
            if (!$rs)
            {
                throw new Exception(mysql_error(), mysql_errno());
            }

            if (mysql_num_rows($rs) == 0)
                return $arr;
            return mysql_fetch_row($rs);
        } catch (Exception $e) {
            $this->log($e->getMessage(), true);
            throw $e;
        }
    }

    public function fetchAssociative($sql)
    {
        try {
            $this->log($sql);
            $arr = array();
            $rs = mysql_query($sql, $this->_connection);
            if (!$rs)
            {
                throw new Exception(mysql_error(), mysql_errno());
            }

            if (mysql_num_rows($rs) == 0)
                return $arr;
            return mysql_fetch_assoc($rs);
        } catch (Exception $e) {
            $this->log($e->getMessage(), true);
            throw $e;
        }
    }

    /**
     *
     * Returns the value of a single field.
     * @param string $sql
     * @throws Exception
     */
    public function fetchResult($sql)
    {
        try {
            $this->log($sql);
            $arr = array();
            $rs = mysql_query($sql, $this->_connection);
            if (!$rs)
            {
                throw new Exception(mysql_error(), mysql_errno());
            }

            if (mysql_num_rows($rs) == 0)
                return $arr;
            return current(mysql_fetch_row($rs));
        } catch (Exception $e) {
            $this->log($e->getMessage(), true);
            throw $e;
        }
    }

    public function fetchObject($sql)
    {
        try {
            $this->log($sql);
            $arr = array();
            $rs = mysql_query($sql, $this->_connection);
            if (!$rs)
            {
                throw new Exception(mysql_error(), mysql_errno());
            }

            if (mysql_num_rows($rs) == 0)
                return $arr;
            return mysql_fetch_object($rs);
        } catch (Exception $e) {
            $this->log($e->getMessage(), true);
            throw $e;
        }
    }

    function fetchEnumValues($table, $field)
    {
        try {
            $query = sprintf("SHOW COLUMNS FROM `%s` LIKE '%s'", $table, $field);
            $this->log($query);

            $rs = mysql_query($query, $this->_connection);
            if (!$rs)
            {
                throw new Exception(mysql_error(), mysql_errno());
            }

            $row = mysql_fetch_array($rs, MYSQL_NUM);
            $regex = "/'(.*?)'/";
            if (preg_match_all($regex, $row[1], $enum_array) == false)
                throw new Exception("No enum values found for field $field in table $table");
            return $enum_array[1];
        } catch (Exception $e) {
            $this->log($e->getMessage(), true);
            throw $e;
        }
    }

    public function fetchObjects($sql)
    {
        try {
            $this->log($sql);
            $arr = array();
            $rs = mysql_query($sql, $this->_connection);
            if (!$rs)
            {
                throw new Exception(mysql_error(), mysql_errno());
            }

            if (mysql_num_rows($rs) == 0)
                return $arr;
            while ($ob = mysql_fetch_object($rs))
                $arr[] = $ob;
        } catch (Exception $e) {
            $this->log($e->getMessage(), true);
            throw $e;
        }
    }

    public function fetchRowsAssociative($sql)
    {
        try {
            $this->log($sql);
            $arr = array();
            $rs = mysql_query($sql, $this->_connection);
            if (!$rs)
            {
                throw new Exception(mysql_error() . "\nquery: $sql", mysql_errno());
            }

            while ($row = mysql_fetch_assoc($rs))
                $arr[] = $row;

            mysql_free_result($rs);
            return $arr;
        } catch (Exception $e) {
            $this->log($e->getMessage(), true);
            throw $e;
        }
    }

    public function fetchRowsIndexed($sql, $field='id')
    {
        try {
            $this->log($sql);
            $arr = array();
            $rs = mysql_query($sql, $this->_connection);
            if (!$rs)
            {
                throw new Exception(mysql_error(), mysql_errno());
            }

            while ($row = mysql_fetch_assoc($rs))
                $arr[$row[$field]] = $row;

            mysql_free_result($rs);
            return $arr;
        } catch (Exception $e) {
            $this->log($e->getMessage(), true);
            throw $e;
        }
    }

    public function fetchFieldList($sql, $enable_debug=false)
    {
        try {
            $this->log($sql);
            $arr = array();

            $rs = mysql_query($sql, $this->_connection);
            if (!$rs)
            {
                throw new Exception(mysql_error(), mysql_errno());
            }

            $i = 0;
            while ($a = mysql_fetch_assoc($rs))
                $arr[] = current($a);
            mysql_free_result($rs);
            return $arr;
        } catch (Exception $e) {
            $this->log($e->getMessage(), true);
            throw $e;
        }
    }

    public function performInsertQuery($insertQueryObject)
    {
        try {
            $sql = $insertQueryObject->getQuery();
            $this->log($sql);
            $rs = mysql_query($sql, $this->_connection);

            if (!$rs)
            {
                throw new Exception(mysql_error(), mysql_errno());
            }

            if ($insertQueryObject->getReturnMethod() == WeDo_Db_Query_Insert::RETURN_TRUE_FALSE)
                return true;

            return mysql_insert_id();
        } catch (Exception $e) {
            $this->log($e->getMessage(), true);
            throw $e;
        }
    }

    public function performUpdateQuery($updateQueryObject)
    {
        try {
            $sql = $updateQueryObject->getQuery();
            $this->log($sql);
            $rs = mysql_query($sql, $this->_connection);

            if (!$rs)
            {
                throw new Exception(mysql_error(), mysql_errno());
            }

            if ($updateQueryObject->getReturnMethod() == WeDo_Db_Query_Update::RETURN_TRUE_FALSE)
                return true;

            return mysql_affected_rows();
        } catch (Exception $e) {
            $this->log($e->getMessage(), true);
            throw $e;
        }
    }

    public function log($query, $is_error=false)
    {
        if (self::LOG_QUERIES)
            if (!$is_error)
                Logger::getLogger(__CLASS__)->info($query);
            else
                Logger::getLogger(__CLASS__)->error($query);
    }
    
    public function toZendDbAdapter()
    {
        return new Zend_Db_Adapter_Pdo_Mysql(array('host' => $this->_host, 'username' => $this->_login, 'password' => $this->_password, 'dbname' => $this->_database_name));
    }

}

?>