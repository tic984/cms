<?php

//require_once APPLICATION_PATH . 'code/core/helpers/TranslatedFoundationQueryDecorator.class.php';

class WeDo_Models_Foundation_Dal
{
    const SQL_SELECT_MAX_ORDER = "SELECT MAX(`ord`) FROM `tbl_catalog` WHERE `class`='%s'";

    /**
     *
     * Retrieves an element from Database.
     * Does not use 'views' for fetching, it simply returns the FULL object.
     * A note on relations: extract fields that are specified on reldefs.xml, according to the role.
     *
     * returns a WeDo_Models_RawObject
     *
     * @param array $params
     * @param string $classUri
     * @param ModuleDescriptor $moduleDescriptor
     * @param ObjectDescriptor $classDescriptor
     * @param Connection $connection
     */
    public static function loadBy($params, WeDo_ClassURI &$classUri, &$moduleDescriptor, &$classDescriptor, &$connection)
    {
        try {
            
            $id = $params['id'];
            $lang = $params['lang'];
            
            $moduleName = $classUri->getPrefix();
            $className = $classUri->getClassName();
            
            $query = WeDo_Models_Foundation_QueryHelper::loadById($params, $classUri, $moduleDescriptor, $classDescriptor);
            
            if ($moduleDescriptor->classHasTranslatedFields($className))
                $query = WeDo_Models_Foundation_Translated_QueryHelper::loadById($query, $params, $className, $moduleDescriptor, $classDescriptor);
            /* qui potrei aggiungere tutte le altre 'implementazioni' ad esempio se � multisite, etc etc */
            $relations = array();

            if ($moduleDescriptor->classHasRelations($className))
            {
                $reldefsdescriptor = WeDo_Application::getSingleton('defs/WeDo_Defs_Relations');

                require_once APPLICATION_PATH . "/code/WeDo_Relations_Helper.class.php";

                foreach ($classDescriptor->getRelations() as $relationFieldName)
                {
                    $relname = $classDescriptor->getRelDef($relationFieldName);
                    $model = $reldefsdescriptor->getRelationModel($relname); //intable or centralized
                    $reltype = $reldefsdescriptor->getRelationType($relname); //1,n or n,1 or n,m
                    $relating = $reldefsdescriptor->getRelating($relname); //who is relating
                    $related = $reldefsdescriptor->getRelated($relname); //who is related
                    $myrole = ($relating == $classUri) ? Reldefs::ROLE_RELATING : Reldefs::ROLE_RELATED;
                    $other_role = ($myrole == Reldefs::ROLE_RELATING) ? Reldefs::ROLE_RELATED : Reldefs::ROLE_RELATING;

                    if (empty($relname))
                        throw new Exception("Relation '$relationFieldName' has no valid reldef!");
                    //Extract foreign keys based on other actor's role
                    //These are fields whose value will be extracted in the query.
                    //I extract in the main query all fields that can be extracted (1n).
                    //actually, it might be better to pick them in a different query, but i'm not sure about.

                    $fkeys = $reldefsdescriptor->getForeignKeys($relname, $other_role);

                    switch ($reltype)
                    {
                        case '1,n':
                            if ($myrole == Reldefs::ROLE_RELATING)
                                WeDo_Relations_Helper::fetch1nStraight($query, $model, $relname, $relationFieldName, $related, $lang, $fkeys);
                            else
                                WeDo_Relations_Helper::fetch1nInverse($query, $model);
                            break;
                        case 'n,1':
                            if ($myrole == Reldefs::ROLE_RELATING)
                                $relations[$relationFieldName] = WeDo_Relations_Helper::fetchn1Straight($id, $model, $relname, $relationFieldName, $related, $lang, $fkeys, $connection);
                            else
                                ; //$arr_n1[$relationFieldName] = WeDo_Relations_Helper::fetchn1Reverse();
                            break;
                        case 'n,m':
                            if ($myrole == Reldefs::ROLE_RELATING)
                                $relations[$relationFieldName] = WeDo_Relations_Helper::fetchnmStraight($id, $model, $relname, $related, $lang, $fkeys, $connection);
                            else
                                $relations[$relationFieldName] = WeDo_Relations_Helper::fetchnmInverse($query, $relationFieldName, $related, $lang, $fkeys);
                            break;
                    }
                }
            }

            $map = $connection->fetchAssociative($query->getQuery());
           
            return new WeDo_Models_RawObject($map, $relations);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public static function getMaxOrderPosition(WeDo_ClassURI &$classUri, &$connection)
    {
        try {
            $sql = sprintf(self::SQL_SELECT_MAX_ORDER, WeDo_Db_Helper::escape($classUri->toString()));
            $value = $connection->fetchResult($sql);
            return ($value + 1);
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
    public static function all($lang, WeDo_ClassURI &$classUri, &$moduleDescriptor, &$classDescriptor, &$displayContext, &$connection)
    {
        try {
            $moduleName = $classUri->getPrefix();
            $className = $classUri->projectToZendClassName();

            $query = WeDo_Models_Foundation_QueryHelper::all($classUri, $moduleDescriptor, $classDescriptor, $displayContext);

            if ($moduleDescriptor->classHasTranslatedFields($className))
                $query = WeDo_Models_Foundation_Translated_QueryHelper::all($query, $lang, $className, $moduleDescriptor, $classDescriptor, $displayContext);

            $relations_properties = array();
            $ref_relations_map = array();
            $ref_relations_ouptut = array();
            $class_role_in_relations = array();

            /**
             * Here i extract all infos I can in a single query (that's, 1n) by appending to the query all joins i can.
             * If it's not a 1n, i extract related items using a IN()
             */
            if ($moduleDescriptor->classHasRelations($className))
            {
                $reldefsdescriptor = WeDo_Application::getSingleton('defs/WeDo_Defs_Relations');
                /* I prepare an array with relationsName */

                foreach ($classDescriptor->getRelationsInView($displayContext->getView()) as $relationFieldName)
                {
                    
                    $relname = $classDescriptor->getRelDef($relationFieldName);
                    $model = $reldefsdescriptor->getRelationModel($relname); //intable or centralized
                    $reltype = $reldefsdescriptor->getRelationType($relname); //1,n or n,1 or n,m
                    $relating = $reldefsdescriptor->getRelating($relname); //who is relating
                    $related = $reldefsdescriptor->getRelated($relname); //who is related
                    $myrole = ($relating == $classUri) ? Reldefs::ROLE_RELATING : Reldefs::ROLE_RELATED;
                    $other_role = ($myrole == Reldefs::ROLE_RELATING) ? Reldefs::ROLE_RELATED : Reldefs::ROLE_RELATING;

                    $class_role_in_relations[$relationFieldName] = $myrole;
                     
                    //Extract foreign keys based on other actor's role
                    //These are fields whose value will be extracted in the query.
                    $fkeys = $reldefsdescriptor->getForeignKeys($relname, $other_role);
                    
                    if ($reltype == '1,n')
                    {
                        if ($myrole == Reldefs::ROLE_RELATING)
                            WeDo_Relations_Helper::fetch1nStraightConcat($query, $model, $relationFieldName, $related, $fkeys, $lang);
                        else
                            WeDo_Relations_Helper::fetch1nInverseConcat($query, $model, $fkeys);
                    }
                    else
                    {
                        $relations_properties[$reltype][] = array('role' => $myrole,
                            'relname' => $relname,
                            'related' => $related,
                            'relating' => $relating,
                            'model' => $model,
                            'lang' => $lang,
                            'fkeys' => $fkeys,
                            'relFieldName' => $relationFieldName
                        );
                    }
                }
            }

            $listItems = $connection->fetchRowsIndexed($query->getQuery());

            $arr_ids = array_keys($listItems);

            //In order to optimize query, I pass by ref the rel_map array and rel_ouptut.
            //rel_map is the mapping relating-related, while relations_output are the related objects obtained using IN...
            WeDo_Relations_Helper::fetchRelationsForList($relations_properties, $arr_ids, $connection, $ref_relations_map, $ref_relations_ouptut);


            //now I have map and objects. I merge them
            //Foreach row in tbl_relations, I pick the element..

            foreach ($ref_relations_map as $relname => $relation_row_array)
            {
                if ($class_role_in_relations[$relname] == Reldefs::ROLE_RELATING)
                {
                    foreach ($relation_row_array as $relation_row)
                    {
                        $relating_id = $relation_row['relating'];
                        $related_id = $relation_row['related'];
                        $listItems[$relating_id][$relname][$related_id] = $ref_relations_ouptut[$relname][$related_id];
                    }
                } else
                {
                    foreach ($relation_row_array as $relation_row)
                    {
                        $relating_id = $relation_row['relating'];
                        $related_id = $relation_row['related'];
                        $listItems[$related_id][$relname][$relating_id] = $ref_relations_ouptut[$relname][$relating_id];
                    }
                }
            }

            return $listItems;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public static function countList($lang, WeDo_ClassURI &$classUri, &$moduleDescriptor, &$classDescriptor, &$connection)
    {
        try {
            $moduleName = $classUri->getPrefix();
            $className = $classUri->getClassName();
            
            $maintable = $moduleDescriptor->getClassTableName($className, "mainTable");
            $transltable = $moduleDescriptor->getClassTableName($className, "translatedTable");

            $translated_fields = WeDo_Db_Helper::fieldsArrayToSql($classDescriptor->getTranslatedFields(), "translatedTable");
            $untranslated_fields = WeDo_Db_Helper::fieldsArrayToSql($classDescriptor->getUnTranslatedFields(), "mainTable");
            $base_fields = WeDo_Db_Helper::getBaseSelectFieldsAsSqlForFo('cat', 'translatedTable');

            $s = new WeDo_Db_Query_Select();
            $s->select(array('COUNT(1)' => 'howmany'))
                    ->from(array($maintable => "mainTable"))
                    ->innerJoinOn(array('tbl_catalog' => 'cat'), 'cat.id = mainTable.id');

            if ($moduleDescriptor->classHasTranslatedFields($className))
            {
                $s->leftJoinOn(array($transltable => "translatedTable"), 'translatedTable.id = mainTable.id')
                        ->where(array("translatedTable.lang = '?'" => $lang));
            }

            return $connection->fetchResult($s->getQuery());
        } catch (Exception $e) {
            throw $e;
        }
    }

    public static function insertToCatalog(&$object, &$connection)
    {
        try {
            $order_value = self::getMaxOrderPosition($object->getClassUri(), $connection);
            $q = new WeDo_Db_Query_Insert('tbl_catalog', WeDo_Db_Query_Insert::RETURN_ID);
            $q->addItem('class', $object->getClassUri()->toString())
                    ->addItem('status', $object->getStatus())
                    ->addItem('owner', $object->getOwner())
                    ->addItem('ord', $order_value);
            return $connection->performInsertQuery($q);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public static function insertToTables($id, &$object, &$moduleDescriptor, &$classDescriptor, &$connection)
    {
        try {
            $classUri = $object->getClassUri();
            //prepares object for writing to db.
            WeDo_Db_Helper::toDb($object, $classDescriptor);
            //list($moduleName, $className) = WeDo_Helpers_Application::explodeClassUri($object->ogetClassUri());
            
            $maintable = $moduleDescriptor->getClassTableName($classUri->projectToZendClassName(), "mainTable");          

            $mainTableQuery = new WeDo_Db_Query_Insert($maintable);
            $mainTableQuery->addItem('id', $id);

            foreach ($classDescriptor->getUnTranslatedFields() as $f)
                $mainTableQuery->addItem($f, $object->get($f));
         
            $connection->performInsertQuery($mainTableQuery);
            
            if ($moduleDescriptor->classHasTranslatedFields($classUri->projectToZendClassName()))
            {
                $transltable = $moduleDescriptor->getClassTableName($classUri->projectToZendClassName(), "translatedTable");
                $translatedTableQuery = new WeDo_Db_Query_Insert($transltable);
                $translatedTableQuery->addItem('id', $id);
                $translatedTableQuery->addItem('lang', $object->getLang());

                foreach ($classDescriptor->getTranslatedFields() as $f)
                    $translatedTableQuery->addItem($f, $object->get($f));
                $connection->performInsertQuery($translatedTableQuery);
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    public static function updateTables($id, &$object, &$moduleDescriptor, &$classDescriptor, &$connection)
    {
        try {
            $classUri = $object->getClassUri();
            //prepares object for writing to db.
            WeDo_Db_Helper::toDb($object, $classDescriptor);
            //list($moduleName, $className) = WeDo_ClassURI::explodeClassUri($object->getClassUri());

            $maintable = $moduleDescriptor->getClassTableName($classUri->projectToZendClassName(), "mainTable");
            

            $mainTableQuery = new WeDo_Db_Query_Update($maintable);

            foreach ($classDescriptor->getUnTranslatedFields() as $f)
                $mainTableQuery->add(array($f => $object->get($f)));
            $mainTableQuery->where(array("id = '?'" => $object->getId()));
            
            $connection->performUpdateQuery($mainTableQuery);
            
            if ($moduleDescriptor->classHasTranslatedFields($classUri->projectToZendClassName()))
            {
                $transltable = $moduleDescriptor->getClassTableName($object->getClassUri()->getClassName(), "translatedTable");
                $translatedTableQuery = new WeDo_Db_Query_Update($transltable);

                foreach ($classDescriptor->getTranslatedFields() as $f)
                    $translatedTableQuery->add(array($f => $object->get($f)));

                $translatedTableQuery->where(array("id = '?'" => $object->getId(), "lang = '?'" => $object->getLang()));
                $connection->performUpdateQuery($translatedTableQuery);
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    public static function deleteObject($objectId, &$moduleDescriptor, &$classDescriptor, &$connection)
    {
        try {
            $q = new WeDo_Db_Query_Update('tbl_catalog');
            $q->add(array("status" => 'deleted'))
                    ->add(array("ts_delete" => date("Y-m-d h:i:s")))
                    ->where(array("id = '?'" => $objectId));
            $connection->performUpdateQuery($q);
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    public static function updateFields(WeDo_ClassURI &$classUri, $arr_fields, $arr_where_clauses, &$moduleDescriptor, &$classDescriptor, &$connection)
    {
        try {
            
            $moduleName = $classUri->getPrefix();
            $className = $classUri->projectToZendClassName();
            
            $mainTable = $moduleDescriptor->getClassTableName($className, "mainTable");
            $mainQuery = new WeDo_Db_Query_Update($mainTable);
            $translatedQuery = false;
            
            foreach($arr_fields as $field => $newValue)
            {
                if(WeDo_Application::getSingleton("app/WeDo_ModuleManager")->getClassDescriptor($classUri)->isFieldTranslatable($field))
                {
                    if($translatedQuery==false)
                    {
                        $translTable = $moduleDescriptor->getClassTableName($className, "translatedTable");
                        $translatedQuery = new WeDo_Db_Query_Update($mainTable);
                    } 
                    $translatedQuery->add(array($field => $newValue));
                }
                else 
                    $mainQuery->add(array($field => $newValue));
            }
            $mainQuery->where($arr_where_clauses);
            $connection->performUpdateQuery($mainQuery);
           
            if($translatedQuery !== false) 
            {
                $translatedQuery->where($arr_where_clauses);
                $connection->performUpdateQuery($translatedQuery);
            }
            return true;
 
        } catch (Exception $e) {
            throw $e;
        }
    }

}

?>