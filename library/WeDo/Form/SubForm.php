<?php

class WeDo_Form_SubForm extends Zend_Form_SubForm {

    public function __construct($options = null) {
        parent::__construct($options);
        
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
            default:
                $type = sprintf("WeDo_Form_Element_%s", ucfirst($type));
                return new $type($name, $options);
        }
    }

}

?>
