<?php

class WeDo_Adapters_Db_Mongo_Adapter extends WeDo_Adapters_Adapter
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
            $db = new Mongo($this->_getConnectionString(), array("db" => $this->_database_name));
            $this->_connection = $db->$database_name;
            $this->_log_queries = false;
            $this->_is_transaction_started = false;
            
            $this->log('Mongo Connection started');
        } catch (Exception $e) {
            print "connessione ".$this->_getConnectionString()." non riuscita.. con database ".$this->_database_name ." :(";
            throw $e;
        }
    }
    
    private function _getConnectionString()
    {
        if(($this->_login!='') && ($this->_password!=''))
            return sprintf("mongodb://%s:%s@%s/%s",$this->_login, $this->_password, $this->_host, $this->_database_name);
        return sprintf("mongodb://%s",  $this->_host);
    }

    public function log($query, $is_error=false)
    {
        if (self::LOG_QUERIES)
            if (!$is_error)
                Logger::getLogger(__CLASS__)->info($query);
            else
                Logger::getLogger(__CLASS__)->error($query);
    }
    
    
    
    public function getConnection()
    {
        return $this->_connection;
    }

    
    public function insert($collection, $contentAsArray, $params=array())
    {
        try {
           unset($contentAsArray['_id']);
           $result = $this->getConnection()->$collection->insert($contentAsArray, array("safe" => true));
           return $contentAsArray['_id'];
        } catch (Exception $e) {
            $this->log($e->getMessage(), true);
            throw $e;
        }
    }
    
    public function update($collection, &$contentAsArray, &$criteriaAsArray, $params)
    {
        try {
           unset($contentAsArray['_id']);
           $params = array_merge(array("safe" => true), $params);
           $result = $this->getConnection()->$collection->update($criteriaAsArray, $contentAsArray, $params);
        } catch (Exception $e) {
            $this->log($e->getMessage(), true);
            throw $e;
        }
    }
    /**
     *
     * @param type $collection
     * @param type $params
     * @return MongoCursor 
     * @throws Exception 
     */
    public function find($collection, $params)
    {
        try {
           return $this->getConnection()->$collection->find();
        } catch (Exception $e) {
            $this->log($e->getMessage(), true);
            throw $e;
        }
    }
    
    public function findOne($collection, $params)
    {
        try {
           return $this->getConnection()->$collection->findOne($params);
        } catch (Exception $e) {
            $this->log($e->getMessage(), true);
            throw $e;
        }
    }
}

?>