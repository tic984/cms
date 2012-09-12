<?php

class WeDo_Db_Helper
{

    public static function arrayToSql($array)
    {
        return implode(", ", $array);
    }

    public static function quote($item)
    {
        if (strpos($item, ".") === false && $item != '*' && strpos($item, '`') === false && strpos($item, "(") === false) //then it's a single field, i can quote it
            return "`$item`";
        if (strpos($item, "(") !== false)
            return $item; //if it is a mysql function

        list($table, $field) = explode(".", $item);

        if ($field != '*' && strpos($field, '`') === false)
            return "$table.`$field`";
        return $item;
    }

    public static function fieldsArrayToSql($array, $alias='')
    {
        if ($alias == '')
            return array_map(array('DbHelper', 'quote'), $array);
        $res = array();
        foreach ($array as $item)
            $res[] = self::quote("$alias.$item");
        return self::arrayToSql($res);
    }

    public static function escape($content)
    {
        if(is_object($content))  { print_r($content); print("is a string"); }
        return mysql_real_escape_string($content);
    }

    public static function getBaseSelectFieldsAsSqlForFo($catAlias, $transLangAlias='')
    {
        $arr = array("$catAlias.*");
        if ($transLangAlias != '')
            $arr[] = "$transLangAlias.lang";
        return self::arrayToSql($arr);
    }

    public static function toDb(&$object, &$classDescriptor)
    {
        $typedefs_descriptor = WeDo_Application::getSingleton('defs/wedo_defs_type');

        foreach ($classDescriptor->getAllFields() as $f)
        {
            $mysqlType = $typedefs_descriptor->getFieldModelForDatabase($f);

            $content = $object->get($f);
            //$should_apply_stripslashes = (get_magic_quotes_gpc()===1);
            //$content = ($should_apply_stripslashes) ? stripslashes($content) : $content;
            switch ($mysqlType)
            {
                case 'varchar':
                    $object->set($f, mysql_real_escape_string($content));
                    break;
                case 'date':
                    $object->set($f, self::dateToDb($content));
                    break;
                case 'datetime':
                    break;
                case 'text':
                    $object->set($f, mysql_real_escape_string($content));
                    break;
                default:
                    break;
            }
        }
    }

    public static function dateToDb($content)
    {
        $date = DateTime::createFromFormat('m/d/Y', $content);
        return $date->format("Y-m-d");
        
    }

    public static function getAliasForRelationWeDo_Db_Query_SelectField($relname, $field)
    {
        return sprintf("rel_%s_%s", $relname, $field);
    }

    public static function splitAliasForRelationWeDo_Db_Query_SelectField($alias)
    {
        list($relname, $field) = explode("_", substr($relname, 4));
        return array($relname, $field);
    }

    public static function strip($content)
    {
        return stripslashes($content);
    }

    public static function enumOptionsToSelect($arr, $dic_context = '')
    {
        $res = array();
        if (is_array($dic_context) && !empty($dic_context))
        {
            foreach ($arr as $pos => $v)
                $res[$v] = $dic_context[$v];
        } else
        {
            foreach ($arr as $pos => $v)
                $res[$v] = $v;
        }
        return $res;
    }

    public static function rsToOptions($recordSet, $keyfields, $valuefields)
    {
        $options = array();
        $cur_keyfield = current($keyfields);
        $cur_valuefile = current($valuefields);
        foreach ($recordSet as $row)
            $options[$row[$cur_keyfield]] = $row[$cur_valuefile];
        return $options;
    }

}

?>