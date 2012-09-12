<?php

class WeDo_Form_Form extends Zend_Form {

    public function __construct($options = null) {
        parent::__construct($options);
        $this->removeDecorator('HtmlTag');
    }

    public function addIdField($val = -1) {
        $element = new WeDo_Form_Element_Hidden('id');
        $element->setRequired(true)
                ->setValidators(array('Int'))
                ->setValue($val);
        $this->addElement($element);
        return $this;
    }

    public function addOwnerField($val = -1) {
        $element = new WeDo_Form_Element_Hidden('owner');
        $element->setRequired(true)
                ->setFilters(array('Int'))
                ->setValue($val);
        $this->addElement($element);
        return $this;
    }

    public function addSubmit($label = "Invia") {
        $element = new WeDo_Form_Element_Submit('submit');
        $element->setValue($label);
        $this->addElement($element);
        return $this;
    }

    public function addField($fName, $varDef, $options=array()) {
        $element = $this->getField($fName, $varDef, $options);
        $this->addElement($element);
        return $this;
    }

    public function getField($fName, $varDef, $options=array()) {

        $html_layout = WeDo_Application::getSingleton('defs/WeDo_Defs_Type')->getFieldModelForForm($varDef);

        $arrValidators = array();
        $arrDecorators = array(array(ucfirst($html_layout)));
        
        $element = $this->_create($html_layout, $fName, $options);
        
        if (isset($options['label']) && $options['label'] != '')
            $element->setLabel($options['label']);
        if (isset($options['required']) && ($options['required']))
            $element->setRequired(true);
        
        if (isset($options['validators']) && !empty($options['validators'])) {
            foreach ($arrValidators as $validator)
                $element->addValidator($validator);
        }
        if(isset($options['value']) && !empty($options['value']))
            $element->setValue($options['value']);
        
        if (isset($options['errormsg']) && !empty($options['errormsg']))
        {
            if(is_array($options['errormsg']))
                $element->setErrorMessages($options['errormsg']);
            else $element->setErrorMessages(array($options['errormsg']));
        }
        return $element;
    }

    protected function _create($type, $name, $options) {
        switch ($type) {
            case 'slider':
                return new ZendX_JQuery_Form_Element_Slider($name,$options);
            case 'spinner':
                $item = new ZendX_JQuery_Form_Element_Spinner($name, $options);
                break;
            case 'datetimepicker':
            case 'datepicker':
                return new ZendX_JQuery_Form_Element_DatePicker($name,$options);
            case 'ckeditor':
                $type = "WeDo_Form_Element_CkEditor";
                return new $type($name, $options);
            default:
                $type = sprintf("WeDo_Form_Element_%s", ucfirst($type));
                return new $type($name, $options);
        }
    }
    
    public function setIsAjaxForm($className)
    {
        parent::setOptions(array("id" => $className));
        $this->getView()->jQuery()->addJavascriptFile('/js/admin/ajaxform.js');
        $this->getView()->jQuery()->addJavascriptFile('/js/admin/WeDo/ajaxform.js');
        $this->getView()->jQuery()->addOnload($this->_getAjaxFormJs($className));
        return $this;
    }
    
    private function _getAjaxFormJs($className)
    {
        $js = sprintf("WeDo_AjaxForm.add('%s');", $className);
        $js .= sprintf("WeDo_AjaxForm.ajaxify('%s');", $className);
        return $js;
    }

}

?>
