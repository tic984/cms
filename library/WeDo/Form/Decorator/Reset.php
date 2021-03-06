<?php

class WeDo_Form_Decorator_Reset extends WeDo_Form_Decorator
{

    public function render($content)
    {

        $separator = $this->getSeparator();
        $placement = $this->getPlacement();
        $element = $this->getElement();

        $element_name = $element->getLabel();
        $html_view = $element->getView();

        if (!$element instanceof Zend_Form_Element_Reset)
            return $content;
        if (null === ($view = $element->getView()))
            return $content;

        return $html_view->formReset(
                        $this->getElement()->getName(), $this->getElement()->getValue()
        );
    }

}

?>