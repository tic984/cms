<?php

class WeDo_Form_Element_Text extends Zend_Form_Element_Text
{
    
    public function __construct($spec, $options = null)
    {
        parent::__construct($spec, $options);
        $this->setDecorators(array(new WeDo_Form_Decorator_Text()));
    }
}

?>
