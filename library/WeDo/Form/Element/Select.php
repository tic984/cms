<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Hidden
 *
 * @author Alessio
 */
class WeDo_Form_Element_Select extends Zend_Form_Element_Select
{
    public function __construct($spec, $options = null)
    {
        parent::__construct($spec, $options);
        $this->setDecorators(array(new WeDo_Form_Decorator_Select()));
    }
}

?>
