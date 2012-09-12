<?php

class WeDo_Defs_Relations extends WeDo_Descriptors_Descriptor
{
    const DESCRIPTOR_PATH = 'etc/reldefs.xml';
    const ROLE_RELATING = 'relating';
    const ROLE_RELATED = 'related';

    const MODEL_CENTRALIZED = 'centralized';
    const MODEL_INTABLE = 'intable';

    public function __construct()
    {
        parent::fromFile(APP_PATH.self::DESCRIPTOR_PATH);
    }

    public function getRelationModel($reldef)
    {
        $node = $this->toSimpleXml()->$reldef;
        return strval($node['model']);
    }

    public function getRelationType($reldef)
    {
        $node = $this->toSimpleXml()->$reldef;
        return strval($node['reltype']);
    }

    public function getRelating($reldef)
    {
        $node = $this->toSimpleXml()->$reldef;
        return strval($node['relating']);
    }

    public function getRelated($reldef)
    {
        $node = $this->toSimpleXml()->$reldef;
        return strval($node['related']);
    }

    public function getForeignKeys($reldef, $role)
    {
        try {
            if (empty($reldef))
                throw new Exception("Empty reldef");
            $res = array();
            $nodelist = $this->toSimpleXml()->$reldef->$role->foreign_keys->key;
            if (empty($nodelist))
                throw new Exception("Reldef whith name '$reldef' has no foreign key set for role $role");
            foreach ($nodelist as $key)
                $res[] = strval($key);
            return $res;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function loadRelDef($reldef)
    {
        try {
            $res = $this->toSimpleXml()->$reldef;
            if (empty($res))
                throw new Exception("Reldef whith name '$reldef' not found in reldef descriptor");
            return $res;
        } catch (Exception $e) {
 //           Logger::getLogger(__CLASS__)->error($e->getTraceAsString());
            throw $e;
        }
    }

}

?>