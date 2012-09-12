<?php

class WeDo_Form_Element_CkEditor extends Zend_Form_Element_Textarea {
    
   const CKEDITOR_OPTIONS = 'ckoptions';

    public function __construct($spec, $options = array()) {
        $custom_options = isset($options[self::CKEDITOR_OPTIONS]) ? $options[self::CKEDITOR_OPTIONS] : array();
        //cleans the custom options out
        unset($options[self::CKEDITOR_OPTIONS]);
        parent::__construct($spec, $options);
        
        $this->_initCkEditor($custom_options);
        $this->setDecorators(array(new WeDo_Form_Decorator_Textarea()));
        $this->getView()->jQuery()->addJavascriptFile('/js/admin/WeDo/ckeditor.js');
        $this->getView()->jQuery()->addJavascriptFile('/js/admin/ckeditor/ckeditor.js');
        $this->getView()->jQuery()->addJavascriptFile('/js/admin/ckeditor/adapters/jquery.js');
     }

    private function _initCkEditor($custom_options) {
       
        $this->getView()->jQuery()->addOnload(sprintf("WeDo_CkEditor.add('%s');", $this->getName()));
        foreach($custom_options as $k => $v)
                $this->getView()->jQuery()->addOnload(sprintf("WeDo_CkEditor.setOption('%s','%s','%s');", $this->getName(),$k, $v));
        $this->getView()->jQuery()->addOnload(sprintf("WeDo_CkEditor.ckEditorizeItem('%s');", $this->getName()));
    }

}

?>
