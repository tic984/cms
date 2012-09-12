<?php

//require_once APPLICATION_PATH . "code/core/adapters/Adapter.class.php";

class WeDo_Adapters_Db_PDO_Adapter extends WeDo_Adapters_Adapter
{

    private $_connection;
    private $_is_transaction_started;
    private $_host;
    private $_login;
    private $_password;
    private $_database_name;
    private $_upon_connection;
    private $_dsn;
    
    
    const SINGLETON_NAMESPACE = 'database/';

    const LOG_QUERIES = false;

    public function __construct($conn_name, $dsn, $host, $login, $password, $database_name, $upon_connection)
    {
        try {
            
            parent::__construct(self::SINGLETON_NAMESPACE, $conn_name);
            
            $this->_dsn = $dsn;
            $this->_host = $host;
            $this->_login = $login;
            $this->_password = $password;
            $this->_database_name = $database_name;
            $this->_upon_connection = $upon_connection;
            
            $connectionParams = array(
                PDO::ATTR_PERSISTENT => true,
                PDO::MYSQL_ATTR_INIT_COMMAND => $this->_upon_connection
                );
            
            $this->_connection = new PDO($this->connectionString(), $this->_login, $this->_password, $connectionParams);
            $this->_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->_is_transaction_started = false;

            $this->log('PDO Connection started');

        } catch (Exception $e) {
            throw $e;
        }
    }
    
    private function connectionString()
    {
        try {

            switch ($this->_dsn)
            {
                case 'sqlite':
                    $conn_string = "%s:%s%s";
                    $connectionString = sprintf($conn_string, DB_DSN, BASE_PATH, DB_URI);
                    break;
                default:
                    $conn_string = "%s:host=%s;dbname=%s";
                    $connectionString = sprintf($conn_string, DB_DSN, DB_URI, DB_NAME);
                    break;
            }
            
            return $connectionString;
            
        } catch (PDOException $e) {
            $this->_logError($e->getMessage());
            die();
        }
    }

    public function transactionStart()
    {
        try {

            $this->_log("---------------START TRANSACTION---------------");
            if ($this->isWithinTransaction())
                throw new Exception('transaction already started');
            if (!$this->_connection->beginTransaction())
                throw new Exception("Failed to create transaction");
            $this->_is_transaction_started = true;
        } catch (Exception $e) {
            $this->_logError($e->getMessage());
            throw $e;
        }
    }

    public function transactionCommit()
    {
        try {

            $this->_log("---------------TRANSACTION COMMIT---------------");
            if (!$this->isWithinTransaction())
                throw new Exception('transaction not started');

            if (!$this->_connection->commit())
                throw new Exception("Failed to commit transaction");
            $this->_is_transaction_started = false;
        } catch (Exception $e) {
            $this->_logError($e->getMessage());
            throw $e;
        }
    }

    public function transactionRollback()
    {
        try {

            $this->_log("---------------TRANSACTION ROLLBACK---------------");
            if (!$this->_connection->rollBack())
                throw new Exception("Failed to rollback transaction");
            $this->_is_transaction_started = false;
        } catch (Exception $e) {
            $this->_logError($e->getMessage());
            throw $e;
        }
    }
    
    public function isWithinTransaction()
    {
        return $this->_is_transaction_started;
    }
    
    
    /* ----------------------- PREPARED STATEMENTS ----------------------- */
    
    
    /** 
     *
     * issues a execute command, but returns no PDOStatement object.
     * This can be quite useful with prepared statements on insert, update, delete.
     * Not to be meant to be used a select
     * 
     * @param type $preparedStatement
     * @param type $params
     * @return boolean 
     */
    public function psExecute($preparedStatement, $params)
    {
        try {
            return $this->_psExec($preparedStatement, $params);
        } catch (Exception $e) {
            $this->_logError($e->getMessage() . " ON PREPARED STATEMENT $preparedStatement");
            throw $e;
        }
    }
    
    public function psFetchRow($preparedStatement, $params)
    {
        try {
            $arr = array();

            $content = $this->_psExecute($preparedStatement, $params)->fetch(PDO::FETCH_NUM);
            if (empty($content))
                return $arr;
            return $content;
        } catch (Exception $e) {
            $this->_logError($e->getMessage() . " ON PREPARED STATEMENT $preparedStatement");
            throw $e;
        }
    }
    
    public function psFetchResult($preparedStatement, $params)
    {
        try {
            return $this->_psExecute($preparedStatement, $params)->fetchColumn();
        } catch (Exception $e) {
            $this->_logError($e->getMessage() . " ON PREPARED STATEMENT $preparedStatement");
            throw $e;
        }
    }
    
    public function psFetchObject($preparedStatement, $params)
    {
        try {

            return $this->_psExecute($preparedStatement, $params)->fetch(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            $this->_logError($e->getMessage() . " ON PREPARED STATEMENT $preparedStatement");
            throw $e;
        }
    }
    
    public function psFetchObjects($preparedStatement, $params)
    {
        try {

            return $this->_psExecute($preparedStatement, $params)->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            $this->_logError($e->getMessage() . " ON PREPARED STATEMENT $preparedStatement");
            throw $e;
        }
    }
    
    
    public function psFetchRowsAssociative($preparedStatement, $params)
    {
        try {

            return $this->_psExecute($preparedStatement, $params)->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $this->_logError($e->getMessage() . " ON PREPARED STATEMENT $preparedStatement");
            throw $e;
        }
    }
    
    public function psFetchRowsIndexed($preparedStatement, $params, $field)
    {
        try {
            $arr = array();
            foreach ($this->_psExecute($preparedStatement, $params)->fetchAll(PDO::FETCH_ASSOC) as $row)
                $arr[$row[$field]] = $row;
            return $arr;
        } catch (Exception $e) {
            $this->_logError($e->getMessage() . " ON PREPARED STATEMENT $preparedStatement");
            throw $e;
        }
    }
    
    public function psFetchRowsGroupedBy($preparedStatement, $params, $groupingField)
    {
        try {
            $arr = array();
            foreach ($this->_psExecute($preparedStatement, $params)->fetchAll(PDO::FETCH_ASSOC) as $row)
            {
                $index = $row[$groupingField];
                $arr[$index][] = $row;
            }

            return $arr;
        } catch (Exception $e) {
            $this->_logError($e->getMessage() . " ON PREPARED STATEMENT $preparedStatement");
            throw $e;
        }
    }
    
    
    /**
     * execute the prepared statement, returns PDOStatement for iterations
     * @param type $preparedStatement
     * @param type $params
     * @return PDOStatement 
     */
    private function _psExecute($preparedStatement, $params)
    {
        try {
            $prep_statement = $this->_connection->prepare($preparedStatement);

            if ($prep_statement === false)
                throw new Exception("Error in prepared statement: $preparedStatement con parametri [" . var_export($params, true) . "]");
            $rs = $prep_statement->execute($params);

            if ($rs === false)
            {
                $error = $this->_connection->errorInfo();
                throw new Exception($error[self::SQLSTATE_ERRORMESSAGE]);
            }

            $this->_log($prep_statement->queryString);
            return $prep_statement;
        } catch (Exception $e) {
            $prep_statement->debugDumpParams();
            $this->_logError($e->getMessage() . " ON PREPARED STATEMENT $preparedStatement");
            throw $e;
        }
    }
    
    
    private function _psExec($preparedStatement, $params)
    {
        try {
            $prep_statement = $this->_connection->prepare($preparedStatement);

            if ($prep_statement === false)
                throw new Exception("Error in prepared statement: $preparedStatement con parametri [" . var_export($params, true) . "]");
            $rs = $prep_statement->execute($params);

            if ($rs === false)
            {
                $error = $this->_connection->errorInfo();
                throw new Exception($error[self::SQLSTATE_ERRORMESSAGE]);
            }

            $this->_log($prep_statement->queryString);
            return $rs;
        } catch (Exception $e) {
            $prep_statement->debugDumpParams();
            $this->_logError($e->getMessage() . " ON PREPARED STATEMENT $preparedStatement");
            throw $e;
        }
    }
    
    /* ----------------------- PREPARED STATEMENTS ----------------------- */
    

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
            return mysql_insert_id($this->_connections);
        } catch (Exception $e) {
            $this->log($e->getMessage(), true);
            throw $e;
        }
    }

    

    public function fetchRow($sql)
    {
        try {
            $arr = array();

            $content = $this->_query($sql)->fetch(PDO::FETCH_NUM);
            if (empty($content))
                return $arr;
            return $content;
        } catch (Exception $e) {
            $this->_logError($e->getMessage() . " ON QUERY: $sql");
            throw $e;
        }
    }


    public function fetchAssociative($sql)
    {
        try {
            $arr = array();

            $content = $this->_query($sql)->fetch(PDO::FETCH_ASSOC);
            if (empty($content))
                return $arr;
            return $content;
        } catch (Exception $e) {
            $this->_logError($e->getMessage() . " ON QUERY: $sql");
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

            return $this->_query($sql)->fetchColumn();
        } catch (Exception $e) {
            $this->_logError($e->getMessage() . " ON QUERY: $sql");
            throw $e;
        }
    }

    public function fetchObject($sql)
    {
        try {

            return $this->_query($sql)->fetch(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            $this->_logError($e->getMessage() . " ON QUERY: $sql");
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
            return $this->_query($sql)->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            $this->_logError($e->getMessage() . " ON QUERY: $sql");
            throw $e;
        }
    }

    public function fetchRowsAssociative($sql)
    {
        try {
            return $this->_query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $this->_logError($e->getMessage() . " ON QUERY: $sql");
            throw $e;
        }
    }

    public function fetchRowsIndexed($sql, $field='id')
    {
        try {

            $arr = array();
            foreach ($this->_query($sql)->fetchAll(PDO::FETCH_ASSOC) as $row)
                $arr[$row[$field]] = $row;
            return $arr;
        } catch (Exception $e) {
            $this->_logError($e->getMessage() . " ON QUERY: $sql");
            throw $e;
        }
    }
    
    public function fetchRowsGroupedBy($sql, $groupingField)
    {
        try {

            $arr = array();

            foreach ($this->_query($sql)->fetchAll(PDO::FETCH_ASSOC) as $row)
            {
                $index = $row[$groupingField];
                $arr[$index][] = $row;
            }

            return $arr;
        } catch (Exception $e) {
            $this->_logError($e->getMessage() . " ON QUERY: $sql");
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

            $rs = $this->_exec($sql);

            if ($insertQueryObject->getReturnMethod() == InsertQuery::RETURN_TRUE_FALSE)
                return true;

            return $this->_connection->lastInsertId();
        } catch (Exception $e) {
            $this->_logError($e->getMessage() . " ON QUERY: $sql");
            throw $e;
        }
    }

    public function performUpdateQuery($updateQueryObject)
    {
        try {
            $sql = $updateQueryObject->getQuery();

            $rs = $this->_exec($sql);

            if ($updateQueryObject->getReturnMethod() == UpdateQuery::RETURN_TRUE_FALSE)
                return true;
            return $rs;
        } catch (Exception $e) {
            $this->_logError($e->getMessage() . " ON QUERY: $sql");
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