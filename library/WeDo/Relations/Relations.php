<?php

class Devel_Action_Helper_Relations
{

    private $_objDescriptor;

    /**
     * 
     * Array of simplexmlobject, it's the content of reldef node in objectdescriptor
     * @var array
     */
    private $_obj_reldef;

    /**
     * 
     * Array of simplexmlobject, it's the content or reldef node in reldefs.xml
     * @var unknown_type
     */
    private $_reldef_reldef;
    private $_classUri;
    private $_callerMapRef;

    public function __construct($classUri)
    {
        $this->_classUri = $classUri;
        $this->_objDescriptor = WeDo_Application::getSingleton('app/WeDo_ModuleManager')->getClassDescriptor($this->_classUri);
        $this->_obj_reldef = array();
        $relations_varname = $this->_objDescriptor->getRelations();
  
        foreach ($relations_varname as $fieldName)
        {
            
            $this->_obj_reldef[$fieldName] = $this->_objDescriptor->getFieldRelDef($fieldName);
            $reldefname = $this->_obj_reldef[$fieldName]['reldef'];
            $this->_reldef_reldef[$fieldName] = WeDo_Application::getSingleton('defs/WeDo_Relations_Defs')->loadRelDef($reldefname);
        }
    }

    /**
     * 
     * Now I have meta infos of the role:
     * 
     *  '@attributes' => 
      array (
      'sortable' => 'S',
      'render' => 'none',
      'display' => 'none',
      'mandatory' => 'N',
      ),
      'foreign_keys' =>
      SimpleXMLElement::__set_state(array(
      'key' => 'name',
      )),

      the model (intable, centralized), the reltype(1,n), (n,1), (n,m)
     * 
     * 
     * @param unknown_type $fieldName
     */
    public function addRelation($fieldName)
    {
        $caller_role = ($this->_reldef_reldef[$fieldName]->relating === $this->_classUri) ? 'relating' : 'related';
        $relmodel = $this->_reldef_reldef[$fieldName]['model'];
        $reltype = $this->_reldef_reldef[$fieldName]['reltype'];
        $relmeta = $this->_reldef_reldef[$fieldName]['$caller_role'];

        switch ($reltype)
        {
            case '1,n':
                if ($caller_role == 'relating') //if it's a 1n,and i'm the relating, count is worthless
                    RelationHelper::count1nStraightAdmin($this->_callerMapRef['id'], $relmodel, $reltype, $relmeta);
                else
                    RelationHelper::count1nInverseAdmin($this->_callerMapRef['id'], $relmodel, $reltype, $relmeta);
                break;
            case 'n,1':
                if ($caller_role == 'relating')
                    RelationHelper::fetchn1Straight($id, $model, $reldef, $relname, $related, $lang, $fkeys, $connection);
                break;
            case 'n,m':
                if ($caller_role == 'relating')
                    RelationHelper::fetchnmStraight($id, $model, $relname, $related, $lang, $fkeys, $connection);
                break;
        }

        return $this;
    }

    public function setCallerItemMapRef(&$itemMap)
    {
        $this->_callerMapRef = $itemMap;
        return $this;
    }

}