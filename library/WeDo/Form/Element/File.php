<?php

/**
 * Description of Hidden
 *
 * @author Alessio
 */
class WeDo_Form_Element_File extends Zend_Form_Element_File
{
    public function __construct($spec, $options = null)
    {
        parent::__construct($spec, $options);
        $this->setDecorators(array(new WeDo_Form_Decorator_File()));
    }
}

?>
