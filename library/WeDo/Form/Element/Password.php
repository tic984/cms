<?php

class WeDo_Form_Element_Password extends Zend_Form_Element_Password
{
    
    public function __construct($spec, $options = null)
    {
        parent::__construct($spec, $options);
        $this->setDecorators(array(new WeDo_Form_Decorator_Password()));
    }
}

?>
