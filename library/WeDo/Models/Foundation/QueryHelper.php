<?php

class WeDo_Models_Foundation_QueryHelper
{

    public static function loadById($params, &$classUri, &$moduleDescriptor, &$classDescriptor)
    {
        try {
            $maintable = $moduleDescriptor->getClassTableName($classUri->projectToZendClassName(), "mainTable");

            $base_fields = WeDo_Db_Helper::getBaseSelectFieldsAsSqlForFo('cat');
            $untranslated_fields = WeDo_Db_Helper::fieldsArrayToSql($classDescriptor->getUnTranslatedFields(), "mainTable");


            $query = new WeDo_Db_Query_Select();
            $query->select($base_fields)
                    ->select($untranslated_fields)
                    ->from(array($maintable => "mainTable"))
                    ->innerJoinOn(array('tbl_catalog' => 'cat'), 'cat.id = mainTable.id')
                    ->where(array("cat.id = '?'" => $params['id']));
            return $query;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public static function all(&$classUri, &$moduleDescriptor, &$classDescriptor, &$displayContext)
    {
        try {

            $maintable = $moduleDescriptor->getClassTableName($classUri->projectToZendClassName(), "mainTable");
            $untranslated_fields = WeDo_Db_Helper::fieldsArrayToSql($classDescriptor->getUnTranslatedFields($displayContext->getView()), "mainTable");
            
            if (!empty($untranslated_fields))
            {
                $query = new WeDo_Db_Query_Select();
                $query->select('cat.*')
                        ->select($untranslated_fields)
                        ->from(array($maintable => "mainTable"))
                        ->innerJoinOn(array('tbl_catalog' => 'cat'), 'cat.id = mainTable.id')
                        ->where(array("cat.status NOT IN ('revision', 'deleted')"));
            } else
            {

                $query = new WeDo_Db_Query_Select();
                $query->select('cat.*')
                        ->from(array('tbl_catalog' => 'cat'))
                        ->where(array("cat.status NOT IN ('revision', 'deleted')"));
            }

            if (!empty($displayContext) && $displayContext->useOrderBy())
                $query->order($displayContext->getOrderBy());
            if (!empty($displayContext) && $displayContext->useLimit())
                $query->limit($displayContext->getStart(), $displayContext->getLen());

            return $query;
        } catch (Exception $e) {
            throw $e;
        }
    }

}

?>