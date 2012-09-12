<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DatePicker
 *
 * @author Alessio
 */
class WeDo_Form_Element_DatePicker extends ZendX_JQuery_Form_Element_DatePicker
{
    public function __construct($spec, $options = null)
    {
        parent::__construct($spec, $options);
        $this->setDecorators(array(new WeDo_Form_Decorator_X_DatePicker()));
    }
}

?>
