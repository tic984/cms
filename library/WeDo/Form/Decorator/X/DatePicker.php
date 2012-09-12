<?php

class WeDo_Form_Decorator_X_DatePicker extends WeDo_Form_XUiWidgetDecorator
{

    public function render($content)
    {
        $element = $this->getElement();
        $element->dateFormat ='dd-mm-yy';
           
        if (!$element instanceof Zend_Form_Element)
        {
            return $content;
        }
        if (null === $element->getView())
        {
            return $content;
        }

        $separator = $this->getSeparator();
        $placement = $this->getPlacement();
        $label = $this->buildLabel();
        $input = $this->buildInput();
        $errors = $this->buildErrors();


        $desc = $this->buildDescription();
        $extra_class = $this->getOption('class');
        if (is_array($extra_class))
            $itemClass = empty($extra_class) ? 'input' : sprintf("input %s", implode(" ", $extra_class));
        else
            $itemClass = empty($extra_class) ? 'input' : sprintf("input %s", $extra_class);

        $outputtpl = <<<OUTPUT
                <div class="%s">
             		%s
                        %s
                    	%s
                </div>
OUTPUT
        ;

        $output = sprintf($outputtpl, $itemClass, $label, $input, $errors);

        switch ($placement)
        {
            case (self::PREPEND):
                return $output . $separator . $content;
            case (self::APPEND):
            default:
                return $content . $separator . $output;
        }
    }

}

?>