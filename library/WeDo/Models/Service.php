<?php

abstract class WeDo_Models_Service
{

    /**
     *
     * Enter description here ...
     * @var unknown_type
     */
    protected $_helper;
    protected $_connections;
    protected $_moduleDescriptor;
    protected $_classDescriptor;
    protected $_classUri;

    /**
     *
     * Records whether a connection is available
     * @var array
     */
    protected $_aliveConnections;

    /**
     * Keeps track whether connections should be performed within transactions.
     * It helps in setting the connection transaction, not overwrites it!
     *
     * It is used infact, by the executewithintransaction :)
     * @var boolean
     */
    protected $_withinTransaction = false;

    public function __construct(WeDo_ClassURI &$classUri, $helper=null)
    {
        if (!empty($helper))
            $this->_helper = $helper;
        
        $this->_classUri = $classUri;
        $this->_moduleDescriptor = WeDo_Application::getSingleton("app/WeDo_ModuleManager")->getModuleDescriptor($this->_classUri->getPrefix());
        
        $this->_classDescriptor = WeDo_Application::getSingleton('app/WeDo_ModuleManager')->getClassDescriptor($this->_classUri);
        $this->_aliveConnections = array();
    }

    abstract public function loadBy($params);

    abstract public function all($params, $displayContext);

    abstract public function count($params);

    abstract public function countList($params);

    abstract public function insert(&$object);

    /**
     *performs updates on whole single item 
     */
    abstract public function updateItem(&$object);
    
    
    abstract public function update(&$criteriaAsArray, &$contentAsArray);

    public function delete($objectId)
    {
        $handlersBefore = $this->_moduleDescriptor->getClassCallbacksFor($this->_classUri->getClassName(), 'beforedelete');
        $handlersAfter = $this->_moduleDescriptor->getClassCallbacksFor($this->_classUri->getClassName(), 'afterdelete');

        try {
            $this->executeWithinTransactions();
            $this->executeHandler($handlersBefore, $objectId);
            $this->performDelete($objectId);
            $this->executeHandler($handlersAfter, $objectId);
            $this->commitTransactions();
        } catch (Exception $e) {
            $this->transactionRollback();
            throw $e;
        }
    }

    private function performDelete($objectId)
    {
        try {
            return WeDo_Models_Foundation_Dal::deleteObject($objectId, $this->_moduleDescriptor, $this->_classDescriptor, $this->getConnection());
        } catch (Exception $e) {
            throw $e;
        }
    }

    abstract public function getFieldValue();

    abstract public function import();

    abstract public function export();

    abstract public function translate();

    /**
     *
     * Dispatcher for Handlers. Object is still defined as an Object, not a map.
     * @param unknown_type $handler
     * @param unknown_type $object
     */
    protected function executeHandler($handler, $object)
    {
        //if no handler is found, return.
        if (empty($handler))
            return true;

        $funcName = $handler['action'];
        $handlerClass = ($handler['handler'] == "self") ? call_user_func(array($this->_classUri->getPrefix(), 'getServiceFor'), $this->_classUri->getClassName()) : $handler['handler'];
        $params = explode(",", $handler['params']);

        $object_as_map = $object->_toMap();

        foreach ($params as $param)
            $object_fields[] = $object_as_map[$param];

        if (method_exists($handlerClass, $funcName))
            return call_user_func_array(array($handlerClass, $funcName), $object_fields);
    }

    protected function getConnection($connectionName='default')
    {
        //si può fare meglio, credo.. :)
        if (empty($this->_connections[$connectionName]))
            $this->_connections[$connectionName] = WeDo_Application::getSingleton("app/WeDo_ModuleManager")
                                                        ->getModuleDescriptor($this->_classUri->getPrefix())
                                                        ->getClassConnectionByName($this->_classUri->projectToZendClassName(), $connectionName);

        $this->_aliveConnections[$connectionName] = true;

        $connection = WeDo_Application::getSingleton($this->_connections[$connectionName]);
        if ($this->_withinTransaction && !$connection->isWithinTransaction())
            $connection->transactionStart();
        return $connection;
    }

    protected function releaseConnection($connectionName)
    {
        $this->_connectionsAlive[$connectionName] = false;
    }

    protected function callbackForSearch()
    {
        print_r(func_get_args());
    }

    protected function executeWithinTransactions()
    {
        $this->_withinTransaction = true;
    }

    protected function commitTransactions()
    {
        foreach ($this->_aliveConnections as $name => $status)
        {
            if ($status)
            {
                $this->getConnection($name)->transactionCommit();
//                Logger::getLogger(__CLASS__)->info("faccio commit sulla transazione $name");
            }
        }

        $this->_withinTransaction = false;
    }

    protected function transactionRollback()
    {
        foreach ($this->_aliveConnections as $name => $status)
        {
            if ($status)
            {
                $this->getConnection($name)->transactionRollback();
//                Logger::getLogger(__CLASS__)->info("faccio rollback sulla transazione $name");
            }
        }
        $this->_withinTransaction = false;
    }

}