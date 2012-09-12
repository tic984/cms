<?php

class WeDo_Form_Element_Uploadify extends Zend_Form_Element_File {
    
   

    public function __construct($spec, $options = array()) {
        $uploadify_options = isset($options['uploadifyOptions']) ? $options['uploadifyOptions'] : array();
        $default_options = isset($options['options']) ? $options['options'] : array();
        
        parent::__construct($spec, $default_options);
        
        $this->_initUploadifyOptions($uploadify_options);
        $this->setDecorators(array(new WeDo_Form_Decorator_Uploadify()));
        $this->getView()->headLink()->appendStylesheet('/js/admin/uploadify/uploadify.css');
        $this->getView()->jQuery()->addJavascriptFile('/js/admin/uploadify/jquery.uploadify-3.1.min.js');
        $this->getView()->jQuery()->addJavascriptFile('/js/admin/WeDo/uploadify.js');
    }

    private function _initUploadifyOptions($uploadifyOptions) {
        
        $this->getView()->jQuery()->addOnload(sprintf("WeDo_Uploadify.add('%s');", $this->getAttrib('id')));
             
        $formData = array("fileDataName" => $this->getName());

        foreach ($uploadifyOptions as $k => $v) {
            switch ($k) 
            {
                case 'formData':
                    foreach ($v as $param => $value)
                       $formData[$param] = $value;
                    break;
                default:break;
            }
        }
         
        $this->getView()->jQuery()->addOnload(sprintf("WeDo_Uploadify.uploadifyItem('%s');", $this->getAttrib('id')));
    }

    private function _getOnloadCode() {
        return sprintf(" WeDo_Uploadify.uploadifyItem('%s');", $this->getAttrib('id'));
    }

}

?>
