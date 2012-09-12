<?php

class WeDo_Relations_Helper
{

    public static function fetch1nStraight(&$query, $model, $relname, $relationFieldName, $related, $lang, $foreignkeys)
    {
        try {

            if ($model == 'intable')
                self::fetch1nStraightInTable($query, $relationFieldName, $related, $lang, $foreignkeys);
            return self::fetch1nStraightCentralized($query, $relname, $relationFieldName, $related, $lang, $foreignkeys);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 
     * counts items related. referred by admin because it will not receive as argument a query, or a part of.
     * Main use is for Backend. Actually useless because if i'm the caller(straight) and it's a 1n, it's one or it's zero.
     * hold it for other (future) usage
     */
    public static function count1nStraightAdmin($caller_id, $relmodel, $reltype, $relmeta)
    {
        try {
            if ($relmodel == 'intable')
                self::count1nStraightInTable($caller_id, $reltype, $relmeta);
            return self::count1nStraightCentralized($caller_id, $reltype, $relmeta);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 
     * counts items related. referred by admin because it will not receive as argument a query, or a part of.
     * Main use is for Backend.
     */
    public static function count1nInverseAdmin($caller_id, $relmodel, $reltype, $relmeta)
    {
        try {
            if ($relmodel == 'intable')
                self::count1nInverseInTable($reltype, $relmeta);
            return self::count1nInverseCentralized($reltype, $relmeta);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public static function fetch1nInverse()
    {
        
    }

    private static function count1nInverseInTable($reltype, $relmeta)
    {
        try {
            $q = new WeDo_Db_Query_Select();
        } catch (Exception $e) {
            
        }
    }

    private static function count1nInverseCentralized($reltype, $relmeta)
    {
        try {
            $q = new WeDo_Db_Query_Select();
        } catch (Exception $e) {
            
        }
    }

    private static function count1nStraightInTable($query, $relationFieldName, $related, $lang, $foreignkeys)
    {
        
    }

    public static function fetchn1Straight($id, $model, $reldef, $relname, $related, $lang, $fkeys, $connection)
    {
        try {
            if ($model == 'intable')
                return self::fetchn1StraightInTable($id, $reldef, $relname, $related, $lang, $fkeys, $connection);
            return self::fetchn1StraightCentralized($query, $reldef, $relname, $related, $lang, $foreignkeys, $connection);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     *
     * nm relations make use of tbl_relations query
     * @param $id
     * @param $model
     * @param $reldef
     * @param $related
     * @param $lang
     * @param $fkeys
     * @param $connection
     */
    public static function fetchnmStraight($id, $model, $relname, $related, $lang, $fkeys, $connection)
    {
        try {
            list($relatedModuleName, $relatedClassName) = WeDo_Helpers_Application::explodeClassUri($related);
            $relatedModuleDescriptor = WeDo_Application::getSingleton("app/WeDo_ModuleManager")->getModuleDescriptor($relatedModuleName);
            $maintable = $relatedModuleDescriptor->getClassTableName($relatedClassName, "mainTable");

            $class_is_translated = $relatedModuleDescriptor->classHasTranslatedFields($relatedClassName);

            $relatedClassDescriptor = WeDo_Application::getSingleton("app/WeDo_ModuleManager")->getClassDescriptor($related);

            $foreign_key_field = $relatedClassDescriptor->getRelationFieldByRelationName($relname);

            $query = new WeDo_Db_Query_Select();

            if ($class_is_translated)
            {
                $translatedTable = $relatedModuleDescriptor->getClassTableName($relatedClassName, "translatedTable");
                $query->select(array("translatedTable.id", "translatedTable.lang"));
                $query->leftJoinOn(array($translatedTable => "translatedTable"), "translatedTable.id = mainTable.id AND translatedTable.lang='$lang'");

                foreach ($fkeys as $fieldname)
                {
                    if (WeDo_Application::getSingleton("app/WeDo_ModuleManager")->getClassDescriptor($related)->isFieldTranslatable($fieldname))
                        $query->select(array("translatedTable.$fieldname"));
                    else
                        $query->select(array("mainTable.$fieldname"));
                }
            } else
            {
                foreach ($fkeys as $fieldname)
                    $query->select(array("mainTable.$fieldname"));
            }

            $query->from(array($maintable => "mainTable"))
                    ->innerJoinOn(array("tbl_catalog" => "cat"), "cat.id = mainTable.id")
                    ->innerJoinOn(array("tbl_relations" => "rel"), "cat.id = rel.related AND rel.relation_name='$relname'")
                    ->where("cat.status NOT IN ('revision', 'deleted')")
                    ->where(array("rel.relating = '?'" => $id));

            $res = $connection->fetchRowsAssociative($query->getQuery());

            return $res;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public static function fetch1nStraightConcat(&$query, $model, $relname, $related, $fkeys, $lang)
    {
        try {
            if ($model == 'intable')
                return self::fetch1nStraightInTableConcat($query, $relname, $related, $fkeys, $lang);
            return self::fetch1nStraightCentralizedConcat($query, $relname, $related, $fkeys, $lang);
        } catch (Exception $e) {
            throw $e;
        }
    }

    private static function fetch1nStraightInTableConcat(&$query, $relname, $related, $fkeys, $lang)
    {
        try {
            list($relatedModuleName, $relatedClassName) = WeDo_Helpers_Application::explodeClassUri($related);
            $relatedModuleDescriptor = WeDo_Application::getSingleton("app/WeDo_ModuleManager")->getModuleDescriptor($relatedModuleName);
            $relFKeys = $fkeys; //$relatedModuleDescriptor->getClassPrimaryKeys($relatedClassName);

            $maintable = $relatedModuleDescriptor->getClassTableName($relatedClassName, "mainTable");
            $translatedTable = $relatedModuleDescriptor->getClassTableName($relatedClassName, "translatedTable");

            //Sets the table alias before joining in.
            $tablealias = $relname;
            $translated_table_alias = $relname . "_trad";
            //Sets Field alias before joining, because I don't want that different columns overrides themselves because of the same name.
            $fields = array();

            foreach ($relFKeys as $fieldname)
            {
                $field_alias = DbHelper::getAliasForRelationWeDo_Db_Query_SelectField($relname, $fieldname);
                if (WeDo_Application::getSingleton("app/WeDo_ModuleManager")->getClassDescriptor($related)->isFieldTranslatable($fieldname))
                    $fields[] = "$translated_table_alias.$fieldname";
                else
                    $fields[] = "$tablealias.$fieldname";
            }
            $concat = sprintf("CONCAT(%s)", implode(", ' - ', ", $fields));
            $query->select(array($concat => $relname))
                    ->leftJoinOn(array($maintable => $tablealias), "$tablealias.id = mainTable.$relname")
                    ->leftJoinOn(array($translatedTable => $translated_table_alias), "$translated_table_alias.id = mainTable.$relname AND $translated_table_alias.lang='$lang'");
        } catch (Exception $e) {
            throw $e;
        }
    }

    private static function fetch1nStraightCentralizedConcat(&$query, $relname, $related, $fkeys, $lang)
    {
        try {
            list($relatedModuleName, $relatedClassName) = WeDo_Helpers_Application::explodeClassUri($related);
            $relatedModuleDescriptor = WeDo_Application::getSingleton("app/WeDo_ModuleManager")->getModuleDescriptor($relatedModuleName);
            $relFKeys = $fkeys; //$relatedModuleDescriptor->getClassPrimaryKeys($relatedClassName);

            $maintable = $relatedModuleDescriptor->getClassTableName($relatedClassName, "mainTable");
            $translatedTable = $relatedModuleDescriptor->getClassTableName($relatedClassName, "translatedTable");

            //Sets the table alias before joining in.
            $tablealias = $relname;
            $translated_table_alias = $relname . "_trad";

            $relations_table = "tbl_relations";
            $relations_table_alias = $relations_table . "_" . $relname;
            //Sets Field alias before joining, because I don't want that different columns overrides themselves because of the same name.
            $fields = array();

            foreach ($relFKeys as $fieldname)
            {
                $field_alias = DbHelper::getAliasForRelationWeDo_Db_Query_SelectField($relname, $fieldname);
                if (WeDo_Application::getSingleton("app/WeDo_ModuleManager")->getClassDescriptor($related)->isFieldTranslatable($fieldname))
                    $fields[] = "$translated_table_alias.$fieldname";
                else
                    $fields[] = "$tablealias.$fieldname";
            }
            $concat = sprintf("CONCAT(%s) ", implode(", ' - ' ,", $fields));

            $query->select(array($concat => $relname))
                    ->leftJoinOn(array($relations_table => $relations_table_alias), "$relations_table_alias.relating = $tablealias.id")
                    ->leftJoinOn(array($maintable => $tablealias), "$tablealias.id = mainTable.$relname")
                    ->leftJoinOn(array($translatedTable => $translated_table_alias), "$translated_table_alias.id = mainTable.$relname AND $translated_table_alias.lang='$lang'")
            ;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     *
     * Appends to the current query all relations of kind 1n not enrolled on tbl_relazioni.
     *
     *
     * In any case, for preventing overriding of fields with joins, every relation field receive an alias, that is
     * rel_relationname_relationfield.
     * @param WeDo_Db_Query_Select $query
     * @param string $relname
     * @param classuri $related
     * @param string $lang
     */
    private static function fetch1nStraightInTable(&$query, $relname, $related, $lang, $foreignkeys)
    {
        try {
            list($relatedModuleName, $relatedClassName) = WeDo_Helpers_Application::explodeClassUri($related);
            $relatedModuleDescriptor = WeDo_Application::getSingleton("app/WeDo_ModuleManager")->getModuleDescriptor($relatedModuleName);
            $maintable = $relatedModuleDescriptor->getClassTableName($relatedClassName, "mainTable");

            $class_is_translated = $relatedModuleDescriptor->classHasTranslatedFields($relatedClassName);

            //Sets the table alias before joining in.
            $tablealias = $relname;

            $query->leftJoinOn(array($maintable => $tablealias), "$tablealias.id = mainTable.$relname");

            if ($class_is_translated)
            {
                $translatedTable = $relatedModuleDescriptor->getClassTableName($relatedClassName, "translatedTable");
                $translated_table_alias = $relname . "_trad";
                //Sets Field alias before joining, because I don't want that different columns overrides themselves because of the same name.
                foreach ($foreignkeys as $fieldname)
                {
                    $field_alias = DbHelper::getAliasForRelationWeDo_Db_Query_SelectField($relname, $fieldname);
                    if (WeDo_Application::getSingleton("app/WeDo_ModuleManager")->getClassDescriptor($related)->isFieldTranslatable($fieldname))
                        $query->select(array("$translated_table_alias.$fieldname" => $field_alias));
                    else
                        $query->select(array("$tablealias.$fieldname" => $field_alias));
                }

                $query->leftJoinOn(array($translatedTable => $translated_table_alias), "$translated_table_alias.id = mainTable.$relname AND $translated_table_alias.lang='$lang'");
            }

            else
            {
                foreach ($foreignkeys as $fieldname)
                {
                    $field_alias = DbHelper::getAliasForRelationWeDo_Db_Query_SelectField($relname, $fieldname);
                    $query->select(array("$tablealias.$fieldname" => $field_alias));
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    private static function fetch1nStraightCentralized(&$query, $relname, $relationFieldName, $related, $lang)
    {
        try {
            
        } catch (Exception $e) {
            throw $e;
        }
    }

    private static function fetchn1StraightInTable($id, $reldef, $relname, $related, $lang, $foreignkeys, $connection)
    {
        try {
            list($relatedModuleName, $relatedClassName) = WeDo_Helpers_Application::explodeClassUri($related);
            $relatedModuleDescriptor = WeDo_Application::getSingleton("app/WeDo_ModuleManager")->getModuleDescriptor($relatedModuleName);
            $maintable = $relatedModuleDescriptor->getClassTableName($relatedClassName, "mainTable");
            $relatedClassDescriptor = WeDo_Application::getSingleton("app/WeDo_ModuleManager")->getClassDescriptor($related);


            //field for join: i Extract fields that are marked
            $foreign_key_field = $relatedClassDescriptor->getRelationFieldByRelationName($reldef);
            if (empty($foreign_key_field))
                throw new Exception("Undefined foreign key field for $relatedClassName");

            $class_is_translated = $relatedModuleDescriptor->classHasTranslatedFields($relatedClassName);

            $query = new WeDo_Db_Query_Select();

            if ($class_is_translated)
            {
                $translatedTable = $relatedModuleDescriptor->getClassTableName($relatedClassName, "translatedTable");
                $query->select(array("translatedTable.id", "translatedTable.lang"));
                foreach ($foreignkeys as $fieldname)
                {
                    if (WeDo_Application::getSingleton("app/WeDo_ModuleManager")->getClassDescriptor($related)->isFieldTranslatable($fieldname))
                        $query->select(array("translatedTable.$fieldname"));
                    else
                        $query->select(array("mainTable.$fieldname"));
                }
                $query->leftJoinOn(array($translatedTable => "translatedTable"), "translatedTable.id = mainTable.id AND translatedTable.lang='$lang'");
            } else
            {

                foreach ($foreignkeys as $fieldname)
                    $query->select(array("mainTable.$fieldname"));
            }

            $query->from(array($maintable => "mainTable"))
                    ->innerJoinOn(array("tbl_catalog" => "cat"), "cat.id = mainTable.id")
                    ->where("cat.status NOT IN ('deleted', 'revision') ")
                    ->where(array("mainTable.$foreign_key_field = '?'" => $id));

            $res = $connection->fetchRowsAssociative($query->getQuery());

            return $res;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     *
     * Retrieves arrays for list. Arrays retrieved here are for n1, nm relations.
     * Role is important
     * It is important also where relations are saved.
     * @param array $relations_properties
     * @param array $arr_ids
     */
    public static function fetchRelationsForList(&$relations_properties, &$arr_ids, &$connection, &$ref_relations_map, &$ref_relations_ouptut)
    {
        try {
            foreach ($relations_properties as $reltype => $rel_prop)
            {
                if ($rel_prop[0]['model'] == Reldefs::MODEL_CENTRALIZED)
                {
                    if ($rel_prop[0]['role'] == Reldefs::ROLE_RELATING)
                        self::fetchRelationsListCentralizedStraigth($reltype, $rel_prop[0], $arr_ids, $connection, $ref_relations_map, $ref_relations_ouptut);
                    else
                        self::fetchRelationsListCentralizedInverse($reltype, $rel_prop[0], $arr_ids, $connection, $ref_relations_map, $ref_relations_ouptut);
                }
                else
                {
                    if ($rel_prop[0]['role'] == Reldefs::ROLE_RELATING)
                        self::fetchRelationsListIntableStraigth($reltype, $rel_prop[0], $arr_ids, $connection, $ref_relations_map, $ref_relations_ouptut);
                    else
                        self::fetchRelationsListIntableInverse($reltype, $rel_prop[0], $arr_ids, $connection, $ref_relations_map, $ref_relations_ouptut);
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     *
     * I guess it is not useful to know wheter it's a n1 or nm...
     * @param string $reltype
     * @param array $relations_properties
     * @param array $arr_ids
     *
     * must join catalog, tbl_relations, related table, related translated table
     */
    private static function fetchRelationsListCentralizedStraigth($reltype, &$rel_prop, $arr_ids, &$connection, &$ref_relations_map, &$ref_relations_ouptut)
    {
        try {
            $relFieldName = $rel_prop['relFieldName'];
            $relname = $rel_prop['relname'];
            $related = $rel_prop['related'];
            $lang = $rel_prop['lang'];
            $fkeys = $rel_prop['fkeys'];

            list($relatedModuleName, $relatedClassName) = WeDo_Helpers_Application::explodeClassUri($related);
            $relatedModuleDescriptor = WeDo_Application::getSingleton("app/WeDo_ModuleManager")->getModuleDescriptor($relatedModuleName);
            $maintable = $relatedModuleDescriptor->getClassTableName($relatedClassName, "mainTable");
            $translatedTable = $relatedModuleDescriptor->getClassTableName($relatedClassName, "translatedTable");
            $relatedClassDescriptor = WeDo_Application::getSingleton("app/WeDo_ModuleManager")->getClassDescriptor($related);

            $query = new WeDo_Db_Query_Select();
            $query->select(array("translatedTable.id", "translatedTable.lang"));
            foreach ($fkeys as $fieldname)
            {
                if (WeDo_Application::getSingleton("app/WeDo_ModuleManager")->getClassDescriptor($related)->isFieldTranslatable($fieldname))
                    $query->select(array("translatedTable.$fieldname"));
                else
                    $query->select(array("mainTable.$fieldname"));
            }
            $sql_in = implode(",", $arr_ids);
            $query->from(array($maintable => "mainTable"))
                    ->innerJoinOn(array($translatedTable => "translatedTable"), "mainTable.id = translatedTable.id AND translatedTable.lang='$lang'")
                    ->innerJoinOn(array('tbl_catalog' => 'cat'), "cat.id = mainTable.id")
                    ->innerJoinOn(array("tbl_relations" => 'rel'), "rel.related = mainTable.id AND rel.relation_name='$relname'")
                    ->where(array("rel.relating IN (?)" => $sql_in))
                    ->where(array("cat.status='?'" => 'active'))
                    ->where(array("rel.active='S'"))
                    ->group('cat.id');

            $ref_relation_content = $connection->fetchRowsIndexed($query->getQuery());
            $ref_relations_ouptut[$relFieldName] = $ref_relation_content;
            foreach ($ref_relation_content as $foo)
                $ref_relations_ouptut[$relFieldName][$foo['id']] = $foo;

            // Now I extract the map for relating items each other
            $query_map = new WeDo_Db_Query_Select();

            $query_map->select(array("relating", "related", "ord"))
                    ->from('tbl_relations')
                    ->where(array("relation_name='?'" => $relname))
                    ->where(array("relating IN (?)" => $sql_in));

            $ref_relations_map[$relFieldName] = $connection->fetchRowsAssociative($query_map->getQuery());
        } catch (Exception $e) {
            throw $e;
        }
    }

    private static function fetchRelationsListCentralizedInverse($reltype, &$rel_prop, $arr_ids, &$connection, &$ref_relations_map, &$ref_relations_ouptut)
    {
        try {
            $relFieldName = $rel_prop['relFieldName'];
            $relname = $rel_prop['relname'];
            $relating = $rel_prop['relating'];
            $lang = $rel_prop['lang'];
            $fkeys = $rel_prop['fkeys'];

            list($relatingModuleName, $relatingClassName) = WeDo_Helpers_Application::explodeClassUri($relating);
            $relatingModuleDescriptor = WeDo_Application::getSingleton("app/WeDo_ModuleManager")->getModuleDescriptor($relatingModuleName);
            $maintable = $relatingModuleDescriptor->getClassTableName($relatingClassName, "mainTable");
            $translatedTable = $relatingModuleDescriptor->getClassTableName($relatingClassName, "translatedTable");
            $relatingClassDescriptor = WeDo_Application::getSingleton("app/WeDo_ModuleManager")->getClassDescriptor($relating);

            $query = new WeDo_Db_Query_Select();
            $query->select(array("translatedTable.id", "translatedTable.lang"));
            foreach ($fkeys as $fieldname)
            {
                if (WeDo_Application::getSingleton("app/WeDo_ModuleManager")->getClassDescriptor($relating)->isFieldTranslatable($fieldname))
                    $query->select(array("translatedTable.$fieldname"));
                else
                    $query->select(array("mainTable.$fieldname"));
            }
            $sql_in = implode(",", $arr_ids);
            $query->from(array($maintable => "mainTable"))
                    ->innerJoinOn(array($translatedTable => "translatedTable"), "mainTable.id = translatedTable.id AND translatedTable.lang='$lang'")
                    ->innerJoinOn(array('tbl_catalog' => 'cat'), "cat.id = mainTable.id")
                    ->innerJoinOn(array("tbl_relations" => 'rel'), "rel.relating = mainTable.id AND rel.relation_name='$relname'")
                    ->where(array("rel.related IN (?)" => $sql_in))
                    ->where(array("cat.status='?'" => 'active'))
                    ->where(array("rel.active='S'"))
                    ->group('cat.id');

            $ref_relation_content = $connection->fetchRowsIndexed($query->getQuery());
            $ref_relations_ouptut[$relFieldName] = $ref_relation_content;
            foreach ($ref_relation_content as $foo)
                $ref_relations_ouptut[$relFieldName][$foo['id']] = $foo;

            // Now I extract the map for relating items each other
            $query_map = new WeDo_Db_Query_Select();

            $query_map->select(array("relating", "related", "ord"))
                    ->from('tbl_relations')
                    ->where(array("relation_name='?'" => $relname))
                    ->where(array("related IN (?)" => $sql_in));

            $ref_relations_map[$relFieldName] = $connection->fetchRowsAssociative($query_map->getQuery());
        } catch (Exception $e) {
            throw $e;
        }
    }

    private static function fetchRelationsListIntableStraigth($reltype, &$rel_prop, $arr_ids, &$connection, &$ref_relations_map, &$ref_relations_ouptut)
    {
        try {
            
        } catch (Exception $e) {
            throw $e;
        }
    }

    private static function fetchRelationsListIntableInverse($reltype, &$rel_prop, $arr_ids, &$connection, &$ref_relations_map, &$ref_relations_ouptut)
    {
        try {
            
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * retrieves just the pairs relating - related for building up the relation map
     *
     */
    //	public static function fetchMap($myrole,$relname,$related,$model)
    //	{
    //		$query = new WeDo_Db_Query_Select();
    //		if($model == Reldefs::MODEL_CENTRALIZED)
    //		{
    //			if($myrole == Reldefs::ROLE_RELATING)
    //			{
    //				$query->select(array("relating", "related", "ord"))
    //						->from('tbl_relations')
    //						->where(array("relation_name='?'"))
    //						->
    //			}
    //			else
    //			{
    //
	//			}
    //		} else {
    //			if($myrole == Reldefs::ROLE_RELATING)
    //			{
    //
	//			}
    //			else
    //			{
    //
	//			}
    //		}
    //	}
}