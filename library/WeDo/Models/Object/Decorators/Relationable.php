<?php

class WeDo_Models_Object_Decorators_Relationable extends WeDo_Models_Object_Decorators_Decorator
{
    private $_related;
    
    public function __construct($object) {
        $this->_object = $object;
        $this->_related = WeDo_Application::getSingleton('app/WeDo_ModuleManager')->getRelationsFor($object->getClassUri());
    }
    
    public function setRelationValue($relationName, $fields)
    {
        if (isset($this->_related[$relationName]))
        {
            foreach ($fields as $row)
            {
                $id = $row['id'];
                unset($row['id']);
                $this->_related[$relationName][$id] = $row;
            }
        }
        return $this;
    }
}
?>
