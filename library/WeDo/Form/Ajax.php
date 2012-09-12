<?php

class WeDo_Form_Ajax extends WeDo_Form_Form {

    private $_id;
    const AJAXFORM_OPTIONS = 'afoptions';
    
    public function __construct($options = null) {
        
        $custom_options = isset($options[self::AJAXFORM_OPTIONS]) ? $options[self::AJAXFORM_OPTIONS] : array();
        //cleans the custom options out
        unset($options[self::AJAXFORM_OPTIONS]);
        
        if(!isset($options['id']))
            $options['id'] = 'af_'.rand(0,100);
        $this->_id = $options['id'];
        parent::__construct($options);
        $this->removeDecorator('HtmlTag');
        $this->getView()->jQuery()->addJavascriptFile('/js/admin/ajaxform.js');
        $this->getView()->jQuery()->addJavascriptFile('/js/admin/WeDo/ajaxform.js');
        $this->getView()->jQuery()->addOnload($this->_getAjaxFormJs($custom_options));
    }
    
    private function _getAjaxFormJs($custom_options)
    {
        $js = sprintf("WeDo_AjaxForm.add('%s');\n", $this->_id);
        
        foreach($custom_options as $k => $v)
            $js .= sprintf("\tWeDo_AjaxForm.setOption('%s','%s','%s');\n", $this->_id,$k, $v);
        
        $js .= sprintf("\tWeDo_AjaxForm.ajaxify('%s');\n", $this->_id);
        return $js;
    }

}

?>
