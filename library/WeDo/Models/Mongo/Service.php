<?php

class WeDo_Models_Mongo_Service extends WeDo_Models_Service
{

    
    public function loadBy($params)
    {
        try {
            return WeDo_Models_Mongo_Dal::loadById($params, $this->_classUri, $this->_moduleDescriptor, $this->_classDescriptor, $this->getConnection());
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function all($params, $displayContext=null)
    {
        try {
            return WeDo_Models_Mongo_Dal::all($params, $this->_classUri, $this->_moduleDescriptor, $this->_classDescriptor, $this->getConnection());
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
            
            return intval(WeDo_Models_Foundation_Dal::countList($this->_classUri, $this->_moduleDescriptor, $this->_classDescriptor, $this->getConnection()));
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
           
            $this->executeHandler($handlersBefore, $object);
            $this->_save($object);
            $this->executeHandler($handlersAfter, $object);
           
        } catch (Exception $e) {
            
        }
    }

    private function _save(&$object)
    {
        try {
            WeDo_Models_Mongo_Dal::insert($object, $this->_moduleDescriptor, $this->_classDescriptor, $this->getConnection());
        } catch (Exception $e) {

            throw $e;
        }
    }

    /**
     * updates the whole item, using _id as pkey
     * @param type $object
     * @throws Exception 
     */
    public function updateItem(&$object)
    {
        $handlersBefore = $this->_moduleDescriptor->getClassCallbacksFor($this->_classUri->getClassName(), 'beforeupdate');
        $handlersAfter = $this->_moduleDescriptor->getClassCallbacksFor($this->_classUri->getClassName(), 'afterupdate');

        try {
            
            $this->executeHandler($handlersBefore, $object);
            $criteria = array("_id" => new MongoId($object->getId()));
            $contentAsArray = $object->_toDb(true);
            $this->update($criteria, $contentAsArray);
            $this->executeHandler($handlersAfter, $object);
            
        } catch (Exception $e) {
            throw $e;
        }
    }
    /**
     * 
     * performs updates on the tables, more than on the item.
     */
    public function update(&$criteriaAsArray, &$contentAsArray)
    {
        try {
            $contents = WeDo_Models_Mongo_Object::getContentForUpdate($contentAsArray);
            if(isset($criteriaAsArray['id']))
            {
                $criteriaAsArray['_id'] = new MongoId($criteriaAsArray['id']);
                unset($criteriaAsArray['id']);
            }
            WeDo_Models_Mongo_Dal::update($criteriaAsArray, $contents, $this->_classUri, $this->_moduleDescriptor, $this->_classDescriptor, $this->getConnection());
        } catch (Exception $e) {
            throw $e;
        }
    }

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