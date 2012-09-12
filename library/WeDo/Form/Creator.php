<?php

class WeDo_Form_Creator
{

    private $_classURI;
    private $_objectDescriptor;
    private $_form;

    public function __construct($classURI, $options=null)
    {
        $this->_classURI = $classURI;
        $this->_form = WeDo_Form_Factory::getForm($classURI, $options);
        $this->_objectDescriptor = WeDo_Application::getSingleton('app/WeDo_ModuleManager')->getClassDescriptor($this->_classURI);
    }

    public function createForm($fieldsName=array(), $fields_options=array())
    {
        if (empty($fieldsName))
            $fieldsName = WeDo_Application::getSingleton('app/WeDo_ModuleManager')->getClassDescriptor($this->_classURI)->getAllFields();

        foreach ($this->_objectDescriptor->getAllFields('') as $fieldName)
        {
            if ($this->_objectDescriptor->fieldIsRelation($fieldName))
            {
                $varDef = $this->_objectDescriptor->getRelDef($fieldName);
                continue;
            } 
            if($this->_objectDescriptor->getFieldVardefLabel($fieldName)== 'image')
                continue;
            
            $varDef = $this->_objectDescriptor->getVarDef($fieldName);
            $fieldOptions = isset($fields_options[$fieldName]) ? $fields_options[$fieldName] : array();
            $element = $this->getField($fieldName, $varDef, $fieldOptions);
            $this->_form->addElement($element);
        }
        return $this;
    }

    public function getForm()
    {
        return $this->_form;
    }

    private function getField($fName, $varDef, $fieldOptions)
    {
        $options = array();
        $fieldMeta = $this->_objectDescriptor->getFieldVardef($fName);

        $options['required'] = (isset($fieldMeta['required']) && ($fieldMeta['required'] == 'Y'));
        $options['label'] = strval($fieldMeta->formlabel);
        $options['validators'] = array(); //$fieldMeta->validation->methods->custom;
        //$arrDecorators = array(array(ucfirst($html_layout)));

        $options['errormsg'] = trim(strval($fieldMeta->validation->msg));
       
        $options = array_merge($options, $fieldOptions);
        
        return $this->_form->getField($fName, $varDef, $options);
    }

}

?>