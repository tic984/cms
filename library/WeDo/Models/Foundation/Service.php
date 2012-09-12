<?php

class WeDo_Models_Foundation_Service extends WeDo_Models_Service
{

    /**
     * load an Item by its id.
     * All 1n, n1, nm relation are filled up: i pick the foreign keys for each relation.
     * Returns a WeDo_Models_RawObject
     * @see app/code/core/ObjectService::loadBy()
     */
    public function loadBy($params)
    {
        try {
            return WeDo_Models_Foundation_Dal::loadBy($params, $this->_classUri, $this->_moduleDescriptor, $this->_classDescriptor, $this->getConnection());
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function all($params, $displayContext)
    {
        try {
            $lang = $params['lang'];
            return WeDo_Models_Foundation_Dal::all($lang, $this->_classUri, $this->_moduleDescriptor, $this->_classDescriptor, $displayContext, $this->getConnection());
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function count($params)
    {
        
    }

    public function countList($params)
    {
        try {
            $lang = $params['lang'];
            return intval(WeDo_Models_Foundation_Dal::countList($lang, $this->_classUri, $this->_moduleDescriptor, $this->_classDescriptor, $this->getConnection()));
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     *
     * Perform insertion of the object.
     * There are two handlers defined, 'beforesave' and 'aftersave'.
     * @param FoundationObject $object
     */
    public function insert(&$object)
    {
        $handlersBefore = $this->_moduleDescriptor->getClassCallbacksFor($this->_classUri->getClassName(), 'beforesave');
        $handlersAfter = $this->_moduleDescriptor->getClassCallbacksFor($this->_classUri->getClassName(), 'aftersave');

        try {
            $this->executeWithinTransactions();
            $this->executeHandler($handlersBefore, $object);
            $this->save($object);
            $this->executeHandler($handlersAfter, $object);
            $this->commitTransactions();
        } catch (Exception $e) {
            $this->transactionRollback();
        }
    }

    private function save(&$object)
    {
        try {
            //inserisco nel catalogo
            
            $id = WeDo_Models_Foundation_Dal::insertToCatalog($object, $this->getConnection());
            
            $object->setId($id);
            //inserisco nella tabella non tradotti:
            WeDo_Models_Foundation_Dal::insertToTables($id, $object, $this->_moduleDescriptor, $this->_classDescriptor, $this->getConnection());
            //se ho delle relazioni, le salvo
            //WeDo_Models_Foundation_Dal::updateRelations($id, $object);
            //se ho dei files, pure
            //FilesDal::save();
        } catch (Exception $e) {

            throw $e;
        }
    }
    
    public function updateItem(&$object) {
        $handlersBefore = $this->_moduleDescriptor->getClassCallbacksFor($this->_classUri->getClassName(), 'beforeupdate');
        $handlersAfter = $this->_moduleDescriptor->getClassCallbacksFor($this->_classUri->getClassName(), 'afterupdate');

        try {
            $this->executeWithinTransactions();
            $this->executeHandler($handlersBefore, $object);
            $this->performUpdate($object);
            $this->executeHandler($handlersAfter, $object);
            $this->commitTransactions();
        } catch (Exception $e) {
            $this->transactionRollback();
        }
    }

    public function update(&$criteriaAsArray, &$contentAsArray)
    {
        ;
    }

    private function performUpdate(&$object)
    {
        try {
            $id = $object->getId();
            WeDo_Models_Foundation_Dal::updateTables($id, $object, $this->_moduleDescriptor, $this->_classDescriptor, $this->getConnection());
            //se ho delle relazioni, le aggiorno
            //WeDo_Models_Foundation_Dal::updateRelations($id, $object);
            //se ho dei files, pure
            //FilesDal::save();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function toggleField()
    {
        
    }
    
//    public function updateFields($arr_fields, $arr_where_clauses)
//    {
//        try {
//            WeDo_Models_Foundation_Dal::updateFields($this->_classUri, $arr_fields, $arr_where_clauses, $this->_moduleDescriptor, $this->_classDescriptor, $this->getConnection());
//            } catch (Exception $e) {
//            throw $e;
//        }
//    }

    public function getFieldValue()
    {
        
    }

    public function import()
    {
        
    }

    public function export()
    {
        
    }

    public function translate()
    {
        
    }

}