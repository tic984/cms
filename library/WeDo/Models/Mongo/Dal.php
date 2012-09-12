<?php

//require_once APPLICATION_PATH . 'code/core/helpers/TranslatedFoundationQueryDecorator.class.php';

class WeDo_Models_Mongo_Dal {

    /**
     *
     * Retrieves an element from Database by its Id.
     * Does not use 'views' for fetching, it simply returns the FULL object.
     * A note on relations: extract fields that are specified on reldefs.xml, according to the role.
     *
     * returns a WeDo_Models_RawObject
     *
     * @param int $id
     * @param string $lang
     * @param string $classUri
     * @param ModuleDescriptor $moduleDescriptor
     * @param ObjectDescriptor $classDescriptor
     * @param Connection $connection
     */
    public static function loadById($params, WeDo_ClassURI &$classUri, &$moduleDescriptor, &$classDescriptor, &$connection) {
        try {
            $collection = self::_getCollection($classUri, $moduleDescriptor);
            return (array) $connection->findOne($collection, $params);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public static function getMaxOrderPosition(WeDo_ClassURI &$classUri, &$connection) {
        try {
            
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     *
     * Returns a list of arrays, made of fields specified in the view.
     * Relations: it's actually a concat() of the primary keys, as reported on module descriptor of the related object.
     * Actually I fetch just 1n relations (CHECK!), what if I'd like to fetch n1 or nm? I should:
     * 	-	first get all (distinct?) ids of items involved
     * 	-	perform a query, for each relations, where I fetch all related once
     *  -	add related.
     * @param string $lang
     * @param string $classUri
     * @param moduleDescriptor $moduleDescriptor
     * @param objectDescriptor $classDescriptor
     * @param displayContext $displayContext
     * @param Connection $connection
     *
     */
    public static function all($params=array(), WeDo_ClassURI &$classUri, &$moduleDescriptor, &$classDescriptor, &$connection) {
        try {
            $collection = self::_getCollection($classUri, $moduleDescriptor);
            $cursor = $connection->find($collection, $params);
            return iterator_to_array($cursor);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public static function countList(WeDo_ClassURI &$classUri, &$moduleDescriptor, &$classDescriptor, &$connection) {
        try {
            return self::_getCollection($classUri, $moduleDescriptor, $connection)->count();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public static function insertToCatalog(&$object, &$connection) {
        
    }

    public static function insert(&$object, &$moduleDescriptor, &$classDescriptor, &$connection) {
        try {
           
            $collection = self::_getCollection($object->getClassUri(), $moduleDescriptor);
            $content = $object->_toDb(); 
            $id = $connection->insert($collection, $content);
            $object->setId($id);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public static function updateItem($object, &$moduleDescriptor, &$classDescriptor, &$connection){
        try {
            $collection = self::_getCollection($object->getClassUri(), $moduleDescriptor);
            $content = $object->_toDb(); 
            $criteria = array("_id" => $object->getId());
            $params = array();
            $connection->update($collection, $content, $criteria, $params);
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    public static function update(&$criteriaAsArray, &$contentAsArray, WeDo_ClassURI &$classUri, &$moduleDescriptor, &$classDescriptor, &$connection){
        try {
            $collection = self::_getCollection($classUri, $moduleDescriptor);
            $params = array("multiple" => true);
            $connection->update($collection, $contentAsArray, $criteriaAsArray, $params);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public static function deleteItem($objectId, &$moduleDescriptor, &$classDescriptor, &$connection) {
        try {
            return self::_getCollection($classUri, $moduleDescriptor, $connection)->remove(array("_id" => $objectId));   
        } catch (Exception $e) {
            throw $e;
        }
    }

    public static function updateFields(WeDo_ClassURI &$classUri, $arr_fields, $arr_where_clauses, &$moduleDescriptor, &$classDescriptor, &$connection) {
        try {

        } catch (Exception $e) {
            throw $e;
        }
    }

    private static function _getCollection(WeDo_ClassURI &$classUri, &$moduleDescriptor) {
        try {
            $moduleName = $classUri->getPrefix();
            $className = $classUri->projectToZendClassName();

            return $moduleDescriptor->getClassTableName($className, "mainTable");
             
        } catch (Exception $e) {
            throw $e;
        }
    }

}

?>