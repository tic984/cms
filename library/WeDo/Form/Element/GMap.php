<?php
//TO BE COMPLETED
class WeDo_Form_Element_GMap extends Zend_Form_Element_Text
{
    
    public function __construct($spec, $options = array()) {
        $custom_options = isset($options['latlang']) ? $options['latlang'] : array();
        $default_options = isset($options['options']) ? $options['options'] : array();
        parent::__construct($spec, $default_options);
        
        $this->_initLatLang($custom_options);
        $this->setDecorators(array(new WeDo_Form_Decorator_Text()));
        $this->getView()->jQuery()->addJavascriptFile('/js/admin/WeDo/gmap.js/gmap.js');
     }

    private function _initLatLang($custom_options) {
       
        $this->getView()->jQuery()->addOnload(sprintf("WeDo_GMap.add('%s');", $this->getName()));
        foreach($custom_options as $k => $v)
                $this->getView()->jQuery()->addOnload(sprintf("WeDo_GMap.setOption('%s','%s','%s');", $this->getName(),$k, $v));
        $this->getView()->jQuery()->addOnload(sprintf("WeDo_GMap.ckEditorizeItem('%s');", $this->getName()));
    }
}

?>
