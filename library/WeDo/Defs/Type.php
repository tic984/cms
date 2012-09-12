<?php

class WeDo_Defs_Type extends WeDo_Descriptors_Descriptor
{
    const DESCRIPTOR_PATH = 'etc/typedefs.xml';
    const FIELD_MODEL_DB_BASE = 1;
    const FIELD_MODEL_DB_EXTENDED = 2;

    const DISPLAY_TYPE_LIST = 1;

    public function __construct()
    {
        parent::fromFile(APP_PATH.self::DESCRIPTOR_PATH);
    }

    public function getFieldModelForDatabase($field, $fieldModel = self::FIELD_MODEL_DB_BASE)
    {
        $res = $this->toSimpleXml()->$field->database;
        if (self::FIELD_MODEL_DB_BASE)
            return strval($res);
    }

    public function getFieldModelForApplication($field)
    {
        $res = $this->toSimpleXml()->$field;
        return strval($res['phptype']);
    }

    public function getFieldBehaviourForFrom($field)
    {
        $res = $this->toSimpleXml()->$field;
        return strval($res['formbehaviour']);
    }

    public function getFieldModelForForm($field)
    {
        $res = $this->toSimpleXml()->$field;
        return strval($res['formtype']);
    }

    //@TODO: verify whether it would be better to put this in AdminListObject
    //reasons for moving it: -> just because regards the list
    //reasons for keeping: ->just because this knows about all typedefs
    public function renderFor(&$field, $type, $display=self::DISPLAY_TYPE_LIST)
    {
        switch ($display)
        {
            case self::DISPLAY_TYPE_LIST:
                return $this->renderForList($field, $type);
        }
    }

    public function renderForList($field, $type)
    {
        switch ($type)
        {
            case 'string':
                $value = substr($field, 0, 150);
                break;
            case 'text':
                $value = substr($field, 0, 150);
                break;
            case 'active':
                $value = ($active == 'Y');
                break;
            default:
                $value = substr($field, 0, 150);
                break;
        }
        return $value;
    }

}

?>